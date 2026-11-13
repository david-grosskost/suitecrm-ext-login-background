# Fond de la page de connexion

Extension SuiteCRM 8 : une **image choisie dans l'administration** s'affiche en fond de
la page de connexion, recadrée pour couvrir l'écran. Sans image, la page garde son fond
habituel.

## Utilisation

**Administration → Page de connexion → Fond de la page de connexion** : choisir une image
JPG, PNG ou WebP (5 Mo au plus ; une image large, 1920 × 1080 par exemple, rend le mieux),
**Enregistrer**. Un aperçu montre l'image en place ; **Retirer l'image** rend le fond
habituel. Réservé aux administrateurs.

## Fonctionnement

- L'image est gardée sur l'instance, dans `upload/login-background/` (volume persistant,
  compris dans la sauvegarde de chaque nuit). Elle reste si l'extension est désactivée,
  et revient à sa réactivation.
- Le dossier `upload/` n'est jamais servi par le serveur web : un point d'entrée de
  SuiteCRM, ouvert sans connexion puisque la page de connexion l'affiche avant,
  sert **seulement** cette image : `legacy/index.php?entryPoint=loginBackground`
  (« introuvable » s'il n'y en a pas).
- Un **module Angular**, que SuiteCRM 8 charge au démarrage sans recompiler son front,
  met cette image en fond de la page de connexion.

## Pour les développeurs

| Fichier | Rôle |
|---|---|
| `legacy/include/login-background/Storage.php` | l'image gardée : enregistrer, remplacer, retirer |
| `legacy/include/login-background/Upload.php` | contrôle du fichier envoyé, par son contenu |
| `legacy/include/login-background/Image.php` | réponse du point d'entrée (404, 304, 200) |
| `legacy/include/login-background/Page.php` | la page d'administration |
| `legacy/include/login-background/SettingsPage.php`, `Serve.php` | branchement dans SuiteCRM |
| `legacy/Extension/…` | section de l'administration, point d'entrée, libellés |
| `app/` → `public/` | le module Angular et sa version construite (`make front@build`) |

```bash
make extension@validate && make quality@lint && make quality@stan && make test@unit && make front@check
```
