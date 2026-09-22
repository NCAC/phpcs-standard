# Analyse du rapport Aikido et plan d'actions correctives

**Dépôt :** `ncac/php-cognitive-complexity`
**Date de l'analyse :** 21 septembre 2026
**Outil :** Aikido (contrôles de posture du dépôt)

---

## 1. Synthèse

Les trois constats sont classés `caution` (avertissement), aucun n'est critique. Deux se corrigent en quelques minutes ; le troisième (épinglage des actions) est le seul qui réduit un risque réel de chaîne d'approvisionnement.

| #   | Constat                                   | Sévérité Aikido | Risque réel              | Effort                   | Priorité            |
| --- | ----------------------------------------- | --------------- | ------------------------ | ------------------------ | ------------------- |
| 1   | Bus factor                                | caution         | Faible (dépôt personnel) | Élevé / hors de contrôle | Basse               |
| 2   | Security policy absente                   | caution         | Faible à moyen           | ~2 min                   | Haute (gain rapide) |
| 3   | Workflow audit : 14 actions non épinglées | caution         | Moyen à élevé            | ~15-30 min               | Haute               |

---

## 2. Analyse détaillée

### 2.1 Repo bus factor

**Constat Aikido :** les huit derniers commits proviennent d'un seul contributeur, la maintenance dépend donc d'une seule personne sans relais démontré.

**Analyse :**

- C'est une heuristique basée sur l'historique de commits, pas une vulnérabilité.
- Sur un dépôt personnel, ce constat est structurel et ne se « corrige » réellement qu'avec un second mainteneur.
- Il devient pertinent uniquement si le paquet est consommé par des tiers qui attendent une continuité de maintenance.

**Actions (optionnelles, priorité basse) :**

1. Ajouter un `CONTRIBUTING.md` décrivant comment proposer une contribution (installation, tests, style, processus de PR).
2. Ajouter un `CODEOWNERS` (`.github/CODEOWNERS`) pour formaliser la responsabilité du code.
3. Étiqueter des issues `good first issue` pour faciliter l'arrivée de contributeurs.
4. Si le projet a de vrais utilisateurs : envisager d'inviter un co-mainteneur de confiance.

**Décision recommandée :** accepter le risque et documenter (voir section 5).

---

### 2.2 Security policy

**Constat Aikido :** aucune politique de sécurité, donc pas de canal documenté pour signaler une vulnérabilité.

**Analyse :**

- Sans `SECURITY.md`, un chercheur en sécurité n'a pas de moyen clair de vous contacter en privé et risque d'ouvrir une issue publique, ce qui divulgue la faille avant correctif.
- GitHub affiche automatiquement le lien « Report a vulnerability » lorsque le signalement privé est activé.

**Actions :**

1. **Activer le signalement privé de vulnérabilités**
   _Settings → Advanced Security (ou « Code security ») → Private vulnerability reporting → Enable._
   Le libellé exact du menu varie selon l'évolution de l'interface GitHub.

2. **Créer `SECURITY.md`** à la racine (ou dans `.github/`) :

```markdown
# Security Policy

## Supported versions

Only the latest release receives security fixes.

## Reporting a vulnerability

Please use GitHub's private vulnerability reporting
(Security tab → "Report a vulnerability"). Do not open a public issue.

You can expect an initial response within 7 days.
```

3. **Commit et push** sur la branche par défaut.

**Vérification :** l'onglet _Security_ du dépôt affiche la politique et le bouton de signalement.

---

### 2.3 Workflow audit

**Constat Aikido :** le seul workflow a été entièrement analysé. Aucun problème de checkout non fiable ni d'injection de script. En revanche, les 14 références d'actions ne sont pas épinglées. L'absence de bloc `permissions` au niveau racine n'est pas un problème en soi selon Aikido.

**Analyse :**

- Une référence par tag (`actions/checkout@v4`) est mutable : si le dépôt de l'action est compromis (ou le tag déplacé), du code arbitraire s'exécute dans votre CI, avec accès aux secrets du workflow.
- Un SHA de commit complet (40 caractères) est immuable et constitue la seule référence réellement sûre.
- Le risque est plus élevé pour les **actions tierces** (hors `actions/*` et `github/*`), moins surveillées que les actions officielles.
- L'absence de `permissions:` racine signifie que `GITHUB_TOKEN` hérite des permissions par défaut du dépôt/de l'organisation. Ce n'est pas bloquant, mais restreindre au minimum est une bonne pratique de moindre privilège.

**Actions :**

1. **Épingler chaque action sur un SHA complet**, avec le tag en commentaire pour la lisibilité :

```yaml
- uses: actions/checkout@<sha-complet-40-caracteres> # v4.2.2
```

2. **Automatiser l'épinglage** avec un outil dédié. Options :

   | Outil                     | Installation                                         | Commande                                                |
   | ------------------------- | ---------------------------------------------------- | ------------------------------------------------------- |
   | `pinact` (Go)             | `brew install pinact` ou binaire des releases GitHub | `pinact run`                                            |
   | `ratchet` (Go)            | binaire des releases GitHub                          | `ratchet pin .github/workflows/<fichier>.yml`           |
   | `pin-github-action` (npm) | `npx pin-github-action`                              | `npx pin-github-action .github/workflows/<fichier>.yml` |

   Vérifiez le diff produit avant de commiter : chaque `uses:` doit contenir un SHA de 40 caractères suivi du tag en commentaire.

3. **Ajouter Dependabot** pour que les SHA restent à jour sans effort manuel (il met aussi à jour le commentaire de version) :

```yaml
# .github/dependabot.yml
version: 2
updates:
  - package-ecosystem: github-actions
    directory: /
    schedule:
      interval: weekly
```

4. **Restreindre les permissions du token** au niveau racine du workflow :

```yaml
permissions:
  contents: read
```

Puis n'élever les permissions qu'au niveau du job qui en a réellement besoin (par exemple `contents: write` pour une release).

**Ordre conseillé :** traiter d'abord les actions tierces, puis les actions officielles.

**Vérification :**

- `grep -nE "uses: .+@" .github/workflows/*.yml` : toutes les lignes doivent se terminer par un SHA de 40 caractères (hors actions locales `./`).
- Lancer le workflow après modification pour confirmer qu'il passe toujours.
- Relancer l'analyse Aikido.

---

## 3. Plan d'action ordonné

| Ordre | Action                                            | Temps estimé | Constat traité  |
| ----- | ------------------------------------------------- | ------------ | --------------- |
| 1     | Activer Private vulnerability reporting           | 1 min        | Security policy |
| 2     | Créer et commiter `SECURITY.md`                   | 2 min        | Security policy |
| 3     | Ajouter `permissions: contents: read` au workflow | 2 min        | Workflow audit  |
| 4     | Épingler les actions tierces sur SHA              | 10-15 min    | Workflow audit  |
| 5     | Épingler les actions officielles sur SHA          | 5-10 min     | Workflow audit  |
| 6     | Ajouter `.github/dependabot.yml`                  | 2 min        | Workflow audit  |
| 7     | (Optionnel) `CONTRIBUTING.md` + `CODEOWNERS`      | 10 min       | Bus factor      |

---

## 4. Checklist de validation

- [ ] Private vulnerability reporting activé
- [ ] `SECURITY.md` présent sur la branche par défaut
- [ ] Toutes les actions épinglées sur un SHA de 40 caractères avec tag en commentaire
- [ ] `permissions: contents: read` au niveau racine du workflow
- [ ] `dependabot.yml` en place pour l'écosystème `github-actions`
- [ ] Workflow vert après les changements
- [ ] Nouvelle analyse Aikido effectuée
- [ ] (Optionnel) `CONTRIBUTING.md` et `CODEOWNERS` ajoutés

---

## 5. Risques résiduels et décisions

- **Bus factor :** risque accepté tant que le dépôt reste un projet personnel. À réévaluer si l'adoption par des tiers augmente. Dans Aikido, ce constat peut être ignoré avec une justification (« dépôt personnel, mainteneur unique assumé »).
- **Épinglage SHA :** protège contre le déplacement de tag, mais pas contre une action dont le code épinglé serait déjà malveillant. Pour les actions tierces critiques, relire brièvement le code au moment de l'épinglage.
- **Dependabot :** les PR de mise à jour de SHA doivent être relues, et non fusionnées à l'aveugle.
