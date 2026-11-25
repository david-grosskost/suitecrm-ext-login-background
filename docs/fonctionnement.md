# Fonctionnement

## Fichiers

| Fichier | Rôle |
|---|---|
| `app/` → `public/` | le module Angular et sa version compilée (`make front@build`) |
| `config/extension.php` | déclare le module à SuiteCRM 8 |
| `legacy/include/login-background/Storage.php` | l'image gardée : enregistrer, remplacer, retirer |
| `legacy/include/login-background/ConfigTable.php`, `Settings.php` | les réglages en base (table `config`) |
| `legacy/include/login-background/Upload.php` | le contrôle du fichier envoyé, par son contenu |
| `legacy/include/login-background/Image.php` | la réponse du point d'entrée (404, 304, 200) |
| `legacy/include/login-background/Page.php`, `PageView.php`, `page.tpl` | la page d'administration |
| `legacy/include/login-background/SettingsPage.php`, `Serve.php` | le branchement dans SuiteCRM |
| `legacy/Extension/…` | la section de l'administration, le point d'entrée, les libellés |
| `tests/` | tests de l'envoi, du stockage, de la réponse et de la page |

## Le module Angular

Chargé par SuiteCRM 8 au démarrage de l'interface, sans recompiler son front, il met
l'image en fond de la page de connexion.

## Le point d'entrée

`legacy/index.php?entryPoint=loginBackground` sert **seulement** cette image. Il est ouvert
sans connexion, puisque la page de connexion l'affiche avant. Il répond « introuvable » s'il
n'y a pas d'image, sinon renvoie vers l'adresse de l'image en place (`…&v=<version>`), gardée
un an en cache par le navigateur : l'image n'est téléchargée qu'une fois, et une nouvelle
image a une nouvelle adresse.

## L'envoi

Le fichier envoyé est lu par `UploadFile` de SuiteCRM, qui vérifie qu'il vient d'être envoyé,
sa taille et, pour une image, qu'elle ne cache pas de code. `Upload` contrôle ensuite son
type par son contenu, pas par son extension.

## Données en base

L'image est gardée dans la table `config` de SuiteCRM (catégorie `login_background`, découpée
en morceaux) : comprise dans la sauvegarde de la base, aucun fichier à écrire.

## Installation et désinstallation

Pas de script : le Chargeur de module copie les fichiers et lance la réparation rapide. Après
une désinstallation, l'image reste en base ; réinstallée, l'extension la retrouve.

## Points d'attention

L'extension passe le scanner du Chargeur de module : aucune fonction de fichier, aucun `echo`
(la page passe par Smarty, l'image par `print`).
