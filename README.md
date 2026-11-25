# Fond de la page de connexion (`login-background`)

Une **image choisie dans l'administration** s'affiche en fond de la page de connexion de
SuiteCRM 8, recadrée pour couvrir l'écran. Sans image, la page garde son fond habituel.

| Fiche | |
|---|---|
| Version de SuiteCRM | 8.10 (`>=8.10 <9`) |
| Types | `ui` |
| Dépend de | — |
| Installée d'office | non |

## Installer

Sur une instance, par **Administration → Chargeur de module** : l'extension y figure dans
la liste des paquets disponibles, cliquer sur **Installer**, puis recharger l'interface
(Ctrl+F5). Depuis le terminal, dans suitecrm-core :

```bash
make extensions@install SLUGS="login-background"
```

La désinstallation se fait au même endroit.

## Documentation

| Page | Contenu |
|---|---|
| [Utilisation](docs/utilisation.md) | choisir, remplacer ou retirer l'image |
| [Fonctionnement](docs/fonctionnement.md) | image gardée en base, point d'entrée sans connexion, module Angular |
| [Développement](docs/developpement.md) | modifier le module, vérifier, tester en local, publier |

Les commandes, la CI et le format des extensions sont décrits dans la documentation de
[suitecrm-devkit](https://github.com/david-grosskost/suitecrm-devkit#documentation).
