# Spécifications du Sniff de nommage des variables

---

## Règle 1 : Variables explicitement déclarées

**Contrainte**

> Toute variable introduite dans le code, que ce soit par une affectation directe, une affectation dans une expression complexe (conditions, boucles, etc.), une déclaration multiple ou capturée via `use` dans une closure, doit respecter le format _snake_case_.

**Définition du snake_case**

> - Uniquement des lettres minuscules, chiffres et underscores
> - Pas d’espace, pas de majuscule, pas de tiret
> - Exemples valides : `$ma_variable`, `$variable_1`, `$is_active`
> - Exemples invalides : `$maVariable`, `$Ma_variable`, `$ma-variable`, `$ma variable`

**Cas à couvrir**

> - Affectation directe : `$ma_variable = 123;`
> - Affectation dans une expression complexe : `if (($ma_variable = calculValeur()) > 0) { ... }`, `while ($compteur = getCompteur()) { ... }`, `foreach ($tableau as $element => $valeur) { ... }`
> - Déclaration multiple : `$a = $b = $c = true;`
> - Déclaration dans les boucles, conditions, fonctions, classes, etc.
> - Variables globales, locales, statiques
>   - Exemple variable statique correcte : `static $compteur_total = 0;`
>   - Exemple variable statique incorrecte : `static $compteurTotal = 0;`
> - Variables déclarées explicitement dans le corps d’une closure doivent respecter le _snake_case_
> - **Variables capturées via `use` dans une closure doivent respecter le _snake_case_**
>   - Exemple : `$ma_variable = 1; $closure = function() use ($ma_variable) { ... };`
> - Variables dans des fichiers inclus (`include`, `require`, etc.) :
>   - Exemple : dans `config.php` → `$ma_variable = 'valeur';` (doit être en snake_case)
> - Variables dans des namespaces :
>   - Exemple :
>     ```php
>     namespace MonNamespace;
>     $ma_variable = 123; // doit être en snake_case
>     ```
> - Les paramètres de closure et de fonctions classiques doivent être en **snake_case**
>   - Exemple correct : `function($parametre_snake_case) { ... }`, `function($mon_parametre) { ... }`
>   - Exemple incorrect : `function($parametreCamelCase) { ... }`, `function($monParametre) { ... }`
> - Propriétés avec chiffres ou acronymes :
>   - Les chiffres sont autorisés à la fin ou au sein du nom, tant que le reste du nom respecte le format snake_case.
>   - Les acronymes doivent être en snake_case (`$is_html`).
>   - Exemple correct : `$is_html`, `$user_2`
>   - Exemple incorrect : `$isHTML`, `$isHtml`, `$user2`
> - Propriétés dynamiques créées à l'exécution via une variable (ex : `$obj->$var`) : **le sniff ne vérifie que le nom de la variable `$var` (qui doit être en snake_case)**, jamais le nom de la propriété accédée en dur (`$obj->maPropriete`).
>   - Exemple correct : `$propriete_dynamique = 'maPropriete'; $obj->$propriete_dynamique = 'valeur';` ($propriete_dynamique respecte snake_case)
>   - Exemple incorrect : `$proprieteDynamique = 'maPropriete'; $obj->$proprieteDynamique = 'valeur';` ($proprieteDynamique ne respecte pas snake_case)
> - Variables temporaires d'usage courant (compteurs, index, etc.) :
>   - Les variables comme `$i`, `$j`, `$tmp`, `$n` doivent respecter le snake_case (lettres minuscules, pas de majuscule, pas d'underscore, pas de tiret).
>   - Exemple correct : `$i`, `$tmp`, `$n`
>   - Exemple incorrect : `$I`, `$Tmp`, `$tmpValeur`
> - Variables avec chiffres ou acronymes :
>   - Les chiffres sont autorisés à la fin ou au sein du nom, tant que le reste du nom respecte le format.
>   - Les acronymes doivent être en minuscules pour snake_case (`$is_html`).
>   - Exemple correct : `$user2`, `$is_html`
>   - Exemple incorrect : `$isHTML`, `$user_2_name`
> - Variables commençant par un ou deux underscores :
>   - L'utilisation d'un ou deux underscores en préfixe est tolérée pour indiquer la visibilité ou des usages internes, mais le reste du nom doit respecter le snake_case.
>   - Exemple correct : `$__ma_variable_cachee`
>   - Exemple incorrect : `$__MaVariableCachee`

**Exceptions**

> - Les variables superglobales PHP (`$_POST`, `$_GET`, etc.) ne sont pas concernées
> - Les propriétés d’objets ou de classes peuvent être traitées dans une règle séparée
> - Les propriétés de classe, de trait et dynamiques sont soumises à la règle camelCase (voir Règle 2)

**Correction automatique**

> - Pour tous les cas (variables locales, paramètres de fonction/closure), le Sniff propose une correction automatique du nom si celui-ci ne respecte pas la convention (snake_case).
> - Pour les propriétés de classe ou de trait, la correction automatique est proposée si le nom ne respecte pas camelCase.
> - **Exception : propriétés dynamiques**
>   - Pour la création de propriétés dynamiques, le Sniff ne propose pas de correction automatique du nom de la propriété (car non vérifié), mais il corrige le nom de la variable si besoin.
>   - Dans ce cas, le Sniff signale l'erreur et ajoute automatiquement la ligne suivante avant l'affectation pour ignorer la règle :
>     `// @phpcs:ignore DartyCmsCodingStandard.NamingConventions.VariableNameSniff`
>   - Cette approche permet d'alerter le développeur tout en évitant une correction risquée.

**Message d’erreur**

> Si une variable ne respecte pas le _snake_case_, le Sniff doit retourner :
>
> - "Le nom de la variable `$nom_variable` doit être en snake_case."
> - Si un paramètre de fonction ou de closure ne respecte pas le _snake_case_, le Sniff doit retourner :
>   - "Le nom du paramètre `$nom_parametre` doit être en snake_case."

---

## Règle 2 : Propriétés de classe, de trait

**Contrainte**

> Les noms de propriétés déclarées dans une classe ou un trait doivent être en _camelCase_.

**Définition du camelCase**

> - Commence par une minuscule, chaque mot suivant commence par une majuscule
> - Pas d’underscore, pas de tiret
> - Exemples valides : `$maPropriete`, `$isActive`, `$compteurTotal`
> - Exemples invalides : `$ma_propriete`, `$MaPropriete`, `$ma-propriete`, `$ma propriete`

**Cas à couvrir**

> - Propriétés publiques, privées, protégées
> - Propriétés statiques
> - Propriétés dans les classes et les traits
>   - Exemple correcte : `public static $compteurTotal = 0;`
>   - Exemple incorrecte : `public static $compteur_total = 0;`
> - Propriétés commençant par un ou deux underscores :
>   - L'utilisation d'un ou deux underscores en préfixe est tolérée pour indiquer la visibilité ou des usages internes, mais le reste du nom doit respecter le camelCase.
>   - Exemple correct : `$__maProprieteCachee`, `$maProprietePrivee`
>   - Exemple incorrect : `$__MaProprieteCachee`, `$ma_propriete_privee`
> - Propriétés dans des contextes particuliers (annotations, attributs PHP8) :
>   - Les règles de nommage s'appliquent uniquement au code PHP (fichiers .php, .inc, .module, ...), pas aux propriétés utilisées dans des annotations ou attributs PHP8, sauf indication contraire dans le projet.
> - Propriétés dynamiques créées via une variable (ex : `$obj->$var`) : **le sniff ne vérifie pas le nom de la propriété, uniquement celui de la variable utilisée**.

**Exceptions**

> - Les constantes de classe (`const`) ne sont pas concernées
> - Les variables locales, globales et paramètres de fonction/closure ne sont pas concernées par cette règle

**Correction automatique**

> - Pour tous les cas (propriétés de classe, dynamiques), le Sniff propose une correction automatique du nom si celui-ci ne respecte pas la convention camelCase.
> - **Exception : propriétés dynamiques**
>   - Pour la création de propriétés dynamiques, le Sniff ne propose pas de correction automatique du nom car il est impossible de garantir que la propriété n'existe pas déjà dans la classe ou l'objet (risque de bug ou d'effet de bord).
>   - Dans ce cas, le Sniff signale l'erreur et ajoute automatiquement la ligne suivante avant l'affectation pour ignorer la règle :
>     `// @phpcs:ignore DartyCmsCodingStandard.NamingConventions.VariableNameSniff`
>   - Cette approche permet d'alerter le développeur tout en évitant une correction risquée.

**Message d’erreur**

> - Si une propriété ne respecte pas le _camelCase_, le Sniff doit retourner :
>   - "Le nom de la propriété `$nom_propriete` doit être en camelCase."
> - Si une propriété dynamique créée à l'exécution ne respecte pas le _camelCase_, le Sniff doit retourner :
>   - "Le nom de la propriété dynamique `$nom_propriete` doit être en camelCase lors de sa création. Correction automatique non proposée, la ligne `// @phpcs:ignore DartyCmsCodingStandard.NamingConventions.VariableNameSniff` est ajoutée pour ignorer la règle."

## Propriétés dynamiques (accès via variable)

En PHP, il est possible d'accéder à une propriété dynamique via une variable : `$object->$variable`. Dans ce cas, **le nom de la variable** (`$variable`) est soumis à la règle du _snake_case_ comme toute autre variable (locale, globale, etc.).

- Exemple correct :
  ```php
  $propriete_dynamique = 'maPropriete';
  $object->$propriete_dynamique = 'valeur'; // $propriete_dynamique respecte snake_case
  ```
- Exemple incorrect :
  ```php
  $proprieteDynamique = 'maPropriete';
  $object->$proprieteDynamique = 'valeur'; // $proprieteDynamique ne respecte pas snake_case
  ```

> **Remarque** : Le sniff ne vérifie pas le nom de la propriété accédée en dur (`$object->maPropriete`), mais bien le nom de la variable utilisée pour l'accès dynamique (`$object->$variable`).
