<?php

ob_start(); //démarre la bufferisation

require_once 'bibli_generale.php';
require_once 'bibli_cuiteur.php';

$bd = hr_bd_connect();

$sql = 'SELECT usPseudo, usNom, blTexte, blDate, blHeure
        FROM users
        INNER JOIN blablas ON blIDAuteur = usID
        WHERE usID = 2
        ORDER BY blID DESC';

$res = hr_bd_send_request($bd, $sql);

hr_aff_debut('Cuiteur | Blablas');

echo '<h1>', 'Les blablas de ';


// Récupération des données et encapsulation dans du code HTML envoyé au navigateur 
$i = 0;
while ($t = mysqli_fetch_assoc($res)) {
    if ($i == 0){
        echo hr_html_proteger_sortie($t['usPseudo']), '</h1><ul>';
    }
    echo    '<li>', 
                hr_html_proteger_sortie($t['usPseudo']), ' ', hr_html_proteger_sortie($t['usNom']), '<br>',
                hr_html_proteger_sortie($t['blTexte']), '<br>',
                hr_amj_clair($t['blDate']), ' à ', hr_heure_clair($t['blHeure']),
            '</li>';
    ++$i;
}
echo '</ul>';

// libération des ressources
mysqli_free_result($res);
mysqli_close($bd);

hr_aff_fin();

// facultatif car fait automatiquement par PHP
ob_end_flush();



?>
