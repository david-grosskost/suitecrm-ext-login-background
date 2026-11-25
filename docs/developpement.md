# Développement

Les commandes `make`, la CI et le format des extensions viennent de
[suitecrm-devkit](https://github.com/david-grosskost/suitecrm-devkit#documentation), à la
version de `composer.json`. Cette page ne décrit que ce qui est propre à l'extension.

## Modifier le module Angular

Après toute modification de `app/` : `make front@build`, puis commiter `app/` **et**
`public/` ensemble. La CI recompile le module (`make front@check`) et refuse un `public/`
qui ne correspond pas à `app/`.

## Vérifier

```bash
make extension@validate && make quality@lint && make quality@stan && make test@unit
make front@check
make package@build && make package@scan
```

## Tester sur une instance locale

1. `make package@build`, puis sur l'instance locale de suitecrm-core, **Administration →
   Chargeur de module** : **Choisir un fichier** (`build/login-background-<version>.zip`),
   **Télécharger**, **Installer**, puis Ctrl+F5.
2. **Administration → Page de connexion → Fond de la page de connexion** : envoyer une image,
   vérifier l'aperçu.
3. Se déconnecter : l'image est en fond de la page de connexion. Retirer l'image : le fond
   habituel revient.
4. Dans suitecrm-core : `make test@regression`.

## Publier une version

Pull request relue, puis merge (squash) : release-please ouvre la pull request
« chore(main): release x.y.z ». La merger crée le tag, la release et son paquet. Penser à la
`version` de `config/extension.php`, affichée par SuiteCRM. suitecrm-core la récupère avec
`make composer@update PACKAGES="david-grosskost/suitecrm-ext-login-background"`.

Mettre à jour `README.md` et `docs/` dans la même pull request que le changement qu'ils
décrivent.
