## Routing

Le routing se fonde sur un paramètre `GET` : `page`. Une route ressemblera
par exemple à ceci : `http://localhost/index.php?page=profile`.

On définit une liste de routes disponibles dans le fichier
`configs/settings.php`, liste que le service `services/routing.php` vient
ensuite lire pour déterminer quelle page afficher en fonction de ce qui est
demandé par l'utilisateur et de ce qui est autorisé.

Une route est définie par un couple « nom de page – contrôleur ».
Par exemple :

```php
const AVAILABLE_ROUTES = [
    'homepage' => 'homepage_controller.php',
    'profile' => 'profile_controller.php',
];
```

Ainsi, la route `http://localhost/index.php?page=homepage` demandera le
contrôleur `controllers/homepage_controller.php`, lequel incluera son template
associé dans la partie `main` du layout (`views/layout.phtml`).

C'est une relation « View – Controller » classique, fondamentale dans une
architecture MVC :

```text
index.php
=> views/layout.phtml
=> controller/xxx_controller.php
=> views/xxx.phtml
```

> **Bonus**
> 
> Bien que le routing fondé sur les paramètres `GET` soit parfaitement valable
> et encore parfois utilisé (forums phpBB par exemple), l'essentiel du web
> aujourd'hui emploie un routing inspiré de chemins de fichiers via le
> caractère `/`. Par exemple, plutôt que `localhost?page=homepage`,
> notre route ressemblerait à `localhost/homepage`.
> 
> C'est toutefois plus difficile à implémenter à la main, et si c'est autant
> présent aujourd'hui, c'est principalement grâce à l'utilisation massive
> d'outils tels que Wordpress ou Symfony qui nous mâchent le travail sur le
> routing (et on les en remercie).
> 
> Dans le cadre de vos projets artisanaux, sans frameworks, nous vous
> recommandons donc de continuer à utiliser les paramètres `GET`. Mais si vous
> avez le cœur au challenge, tenter d'implémenter un routing type
> `localhost/homepage` vous en apprendra beaucoup sur les requêtes HTTP et la
> manière dont PHP gère celles-ci.


## Gestion des erreurs des formulaires

Le contrôleur reçoit les données du formulaires sous la forme du tableau
`$_POST`. On s'assure que toutes les données nécessaires soient bien définies
(`isset($_POST['name'])`, par exemple).

Ensuite, avec ces données brutes, on hydrate une nouvelle instance du modèle
concerné. Par exemple, pour la création d'un article de blog simpliste :

```php
$article = new Article();
if (isset($_POST['title']) && isset($_POST['content'])) {
    $article->setTitle($_POST['title']);
    $article->setContent($_POST['content']);
// ...
```

Maintenant, notons que chaque modèle (`Message`, `Category`, etc.) dispose
d'une méthode `validate`. Le développeur définit librement le corps de cette
méthode, dont le rôle est très simple : renvoyer un tableau `$errors`.
Lorsqu'on l'appelle, la méthode définit un tableau `$errors` vide, puis elle
vérifie rigoureusement que chaque valeur entrée dans chaque propriété
correspond à ce qu'on attend d'elle : unicité, nombre de caractères, etc.
Si une erreur est détectée, par exemple un titre trop long, on ajoute une
entrée au tableau `$errors`. Si aucune erreur n'a été détectée, le tableau
`$errors` demeure vide. Dans tous les cas, le tableau `$errors` – qu'il soit
vide ou non – est systématiquement retourné à la fin de la méthode.

Dans notre contrôleur, il ne reste alors plus qu'à utiliser cette méthode
pour savoir si l'instance d'un modèle contient des erreurs ou non.

```php
$article = new Article();
if (isset($_POST['title']) && isset($_POST['content'])) {
    $article->setTitle($_POST['title']);
    $article->setContent($_POST['content']);
    $errors = $article->validate();
    if (empty($errors)) {
        // Logique d'ajout à la base de données
    }
}
```

Ainsi, l'article n'est inséré en base de données que si le tableau d'erreurs
retourné par sa méthode `validate` est vide. Dans le cas contraire, si des
erreurs sont présentes, on affiche à nouveau le formulaire en listant cette
fois les erreurs à l'utilisateur, une à une, via un `foreach` dans le
template.

> **Bonus**
> 
> C'est bien sûr une technique de gestion d'erreurs parmi d'autres. Son
> principal défaut est que notre tableau `$errors` est un simple tableau à
> clés numériques qui ne distingue pas les champs les uns des autres. On se
> contente alors de lister les erreurs au-dessus du formulaire, sans réelle
> indication visuelle et intuitive des *inputs* exacts ayant levé les erreurs.
> 
> Une solution plus élégante mais aussi plus compliquée consisterait justement
> à afficher les erreurs sous leurs champs respectifs et non de les lister
> indistinctement au-dessus du formulaire. N'hésitez pas à tenter de mettre
> ce type de gestion en place : c'est généralement beaucoup plus agréable en
> termes d'expérience utilisateur (UX). (:
# 3wa_distorsion
