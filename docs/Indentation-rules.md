# Règles d'indentation NCAC

Ce document synthétise les règles d'indentation utilisées par le sniff NCAC, avec un tableau récapitulatif des cas rencontrés et des exemples concrets.

## Bloc (`BLOCK`)

Un bloc est une section de code délimitée par des accolades `{ ... }` : fonctions, classes, méthodes, boucles, conditions, etc.

### Règle d'indentation

- **Ouverture de bloc** :
  - La ligne d'ouverture (`{`) est indentée selon le niveau courant (avant le push dans la stack).
  - Après l'ouverture, on ajoute un niveau à la stack pour les lignes suivantes.
- **Fermeture de bloc** :
  - La ligne de fermeture (`}`) est indentée selon le niveau après le pop de la stack (on retire le bloc avant de calculer l'indentation).

**Cas particulier : bloc sur une même ligne**

Quand une fermeture de bloc est suivie immédiatement d'une ouverture (`} else {`), la règle s'applique :

- La fermeture `}` réduit l'indentation de la ligne elle-même (après le pop de la stack)
- Puis l'ouverture `{` procède à un push de la stack pour les lignes suivantes

**Exemple**

```php
if ($x > 0) {
  echo 'positif';
} else {
  echo 'négatif';
}
```

Ici, la ligne `} else {` est indentée au niveau du bloc parent (niveau 1), car le pop de `}` est fait avant le calcul, puis le push de `{` s'applique pour les lignes suivantes.

### Exemple

```php
function foo() { // niveau 0
  $x = 1;         // niveau 1
  if ($x > 0) {   // niveau 1
    echo $x;      // niveau 2
  }               // niveau 1
}                 // niveau 0

class Bar {
  public function baz() {
    // ...
  }
}
```

**Résumé** :

- L'ouverture `{` n'augmente pas l'indentation de la ligne elle-même, mais des lignes suivantes.
- La fermeture `}` réduit l'indentation de la ligne elle-même.
