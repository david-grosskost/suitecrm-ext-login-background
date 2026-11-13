# Modèle d'extension SuiteCRM

Point de départ de toute extension des projets SuiteCRM : on ne modifie jamais le code de
SuiteCRM, on lui ajoute des extensions. Ce dépôt contient une extension d'exemple
(`exemple`) qui passe toutes les vérifications : on la remplace par la sienne.

## Créer une extension

1. Sur GitHub, **Use this template → Create a new repository**, nommé
   `suitecrm-ext-<slug>` (ex. `suitecrm-ext-siret-validation`).
2. Le cloner, puis lancer `make` : le devkit s'installe (Docker suffit, pas besoin de PHP).
3. Remplacer l'exemple :
   - `extension.json` : `slug`, nom, description en mots métier, types ;
   - `composer.json` : `name` en `…/suitecrm-ext-<slug>`, description ;
   - `legacy/include/exemple/`, `tests/ExempleTest.php` : son code et ses tests.
4. Vérifier, comme le fera la CI :

```bash
make extension@validate   # format : manifeste, composer.json, place des fichiers
make quality@lint         # style du code, scripts
make quality@stan         # analyse statique (PHPStan, niveau max)
make test@unit            # tests unitaires (PHPUnit)
```

5. Pousser une branche et ouvrir une pull request : un développeur la relit avant le merge.

## Où mettre quoi

| Dossier | Copié dans SuiteCRM | Pour |
|---|---|---|
| `legacy/Extension/…` | `public/legacy/custom/Extension/…` | hooks, champs, libellés : SuiteCRM les assemble au Quick Repair. Le slug figure dans chaque nom de fichier (ex. `legacy/Extension/application/Ext/LogicHooks/<slug>.php`) |
| `legacy/include/<slug>/` | `public/legacy/custom/include/<slug>/` | les classes PHP appelées par les hooks : la logique, testée sans SuiteCRM |
| `backend/`, `config/` | `extensions/<slug>/` | la couche SuiteCRM 8 (services Symfony), chargée automatiquement |
| `database/seeders/` | — | ses données de démo, chargées par le seed des démos |
| `tests/` | — | ses tests unitaires |

`make extension@validate` refuse un fichier rangé ailleurs : deux extensions ne peuvent
ainsi jamais écrire le même fichier.

## Le manifeste `extension.json`

| Clé | Contenu |
|---|---|
| `slug` | identifiant, en minuscules et tirets : le préfixe de tous ses fichiers |
| `name` | nom, en français |
| `description` | ce que fait l'extension, en mots métier (20 caractères au moins) |
| `types` | parmi `logic-hook`, `fields`, `ui`, `scheduler`, `lang` |
| `suitecrm` | versions de SuiteCRM compatibles, ex. `>=8.10 <9` |

## Ce qui vient du devkit

Les commandes `make`, la CI, les hooks Git, les règles de qualité et la signature des
classes SuiteCRM pour PHPStan viennent de
[suitecrm-devkit](https://github.com/david-grosskost/suitecrm-devkit), à la version de
`composer.json`. Corriger une règle se fait dans le devkit, puis on monte sa version ici
(`composer.json` et `.github/workflows/*.yml`, ensemble).
