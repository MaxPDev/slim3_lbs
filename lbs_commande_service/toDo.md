# writer in logger

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