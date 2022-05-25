<?php

ob_start(); //démarre la bufferisation
session_start();

require_once 'bibli_generale.php';
require_once 'bibli_cuiteur.php';


$bd = hr_bd_connect();

$tag = $_GET['tag'];
$tag === ''? $tag = 'Tendances' : $tag = $tag;

hr_aff_debut('Cuiteur | Tendances','../styles/cuiteur.css');
hr_aff_entete($tag);
$sql = "SELECT * FROM users WHERE usID = ".$_SESSION['usID']."";
$request = hr_bd_send_request($bd,$sql);
rc_aff_infosV2($request);


if($tag === 'Tendances'){
    hrl_aff_tend_jour($bd);
    hrl_aff_tend_semaine($bd);
    hrl_aff_tend_mois($bd);
    hrl_aff_tend_année($bd);
}else{
    $nb_blablas = 4; //nombre de blablas affiché au premier chargement de la page
    if(isset($_GET['more'])){
        $nb_blablas  = $_GET['nb'];
    }
    $tag = $_GET['tag'];
    $sql = "SELECT taID,taIDBlabla,COUNT(*) AS NBtag FROM tags WHERE taID='$tag'";
    $requet = hr_bd_send_request($bd,$sql);
    $t = mysqli_fetch_assoc($requet);
    $nb_blablas_total = $t['NBtag'];



    $sql = "SELECT taID, taIDBlabla,blID,blTexte,blDate,blHeure,
            auteur.usID AS autID, auteur.usPseudo AS autPseudo, auteur.usNom AS autNom, auteur.usAvecPhoto AS autPhoto, 
            origin.usID AS oriID, origin.usPseudo AS oriPseudo, origin.usNom AS oriNom, origin.usAvecPhoto AS oriPhoto
            FROM users AS auteur INNER JOIN blablas ON auteur.usID = blIDAuteur INNER JOIN tags ON blID = taIDBlabla 
            LEFT OUTER JOIN users AS origin ON origin.usID = blIDAutOrig
            WHERE taID = '$tag'
            ORDER BY blID DESC 
            LIMIT $nb_blablas";
    $requet = hr_bd_send_request($bd,$sql);
    rc_aff_blablasV2($requet);
    hr_aff_more_blablas_tendance($nb_blablas,'tendances.php',$nb_blablas_total,$tag);
}



hr_aff_pied();
hr_aff_fin();


function hrl_aff_tend_jour(mysqli $bd):void{
    echo '<h2 class = "titre">Top 10 du jour</h2>';
    $today = date('Ymd');
    echo $today;
    $sql = "SELECT taID,taIDBlabla, COUNT(*) AS NB, blDate, blHeure
            FROM tags INNER JOIN blablas ON blID = taIDBlabla
            WHERE blDate = $today
            ORDER BY NB DESC LIMIT 0,6";
    $request = hr_bd_send_request($bd,$sql);
    while($t = mysqli_fetch_assoc($request)){
        echo '<br>';
        print_r($t);
    }
    
}
function hrl_aff_tend_semaine(mysqli $bd):void{
    echo '<h2 class = "titre">Top 10 de la semaine</h2>';
}
function hrl_aff_tend_mois(mysqli $bd):void{
    echo '<h2 class = "titre">Top 10 du mois</h2>';
}
function hrl_aff_tend_année(mysqli $bd):void{
    echo '<h2 class = "titre">Top 10 de l\'année</h2>';
}


mysqli_close($bd);
ob_end_flush();

?>
