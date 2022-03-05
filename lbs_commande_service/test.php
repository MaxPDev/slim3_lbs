<?php

// 1. initialisation
$ch = curl_init();
// 2. configutation de la requête

$url = 'http://api.commande.local:19080/td/commandes/201316ba-2918-4549-be39-fff13a497e0a?embed=categorie';

curl_setopt($ch,CURLOPT_URL,"$url");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 1);
// 3. exécution et récupération du résultat
$output = curl_exec($ch);

//print_r($output);

$info = curl_getinfo($ch) ;

//print_r($info);

// 4. fermer
curl_close($ch);


