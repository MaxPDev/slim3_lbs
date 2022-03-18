# writer in logger

# renomer dossier "errors" en "output" ou équivalent

# remind middleware, why ?

intérêt, utilisation
 factoriser du code commun à plusieurs routes
 sortir des contrôleurs tout ce qui n'est pas
directement lié aux fonctionnalités
 exemples :
 contrôle d'accès, autorisation
 contrôle de token
 ajout de headers : content-type, header CORS
 validation de données
 logging d'activité

PSR 7 standardise from des mw : réutilisable dans plusieurs framework.
Regarder exemple (pour slim par ex) middleware sur github : validation de donné, gérer headers CORS, Token csrf ds les formulaire, etc..., déjà prêt. Attention : compatible slim3 (attention slim4). Si pSR7, devrait être ok.

->recoder en fonction de ça

// page middleware avec récupération de route
pour vérifier token, le mw a besoin d'info de la route (??)

#erreur
Fatal error: Uncaught UnexpectedValueException: The stream or file "/var/www/src/app/conf/../log/debug.log" could not be opened in append mode: failed to open stream: Permission denied in /var/www/src/vendor/monolog/monolog/src/Monolog/Handler/StreamHandler.php on line 146
Permission
chown -R www-data:www-data "project foldername"s
ou chmod 777....

# refaire un tour de copréhension pour le JWT token. Modifier le secret dans les settings du container

# Ne pas oublié de rajouter les service dans /etc/hosts !

---

#Notes : Sécurité et API Rest

1. violation du contrôle d'accès : bypasser: uniquement les gens autorisés ont accès

2. Conception non sûr (échange donnée en clair après changement de structure...)

3.

---

API (3,4,6). select, alias, contrôler donnée, et attention au param des requêtes

4. size, limit..., / unité de temps

1 vs 5 : 5 : put exposé

6 récupéré d'un nom, mis dans le save sans contrôle, nom changé dans une commande par exemple.

---

ini, pas ini.dist + gitignore
