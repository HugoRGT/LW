<?php

ob_start();
session_start();

require_once 'bibli_cuiteur.php';
require_once 'bibli_generale.php';

$bd = hr_bd_connect();
$user_id = $_GET['id'];
$nb_blablas = 4; //nombre de blablas affiché au premier chargement de la page
$sql = "SELECT COUNT(blID) AS nb FROM blablas WHERE blIDAuteur=$user_id";
$requet = hr_bd_send_request($bd,$sql);
$t = mysqli_fetch_assoc($requet);
$nb_blablas_total = $t['nb'];
if(isset($_GET['more'])){
    $nb_blablas  = $_GET['nb'];
}


$sql =  "SELECT * FROM users WHERE usID=$user_id";
$requet = hr_bd_send_request($bd,$sql);
$t = mysqli_fetch_assoc($requet);
$pseudo = $t['usPseudo'];
$photo = $t['usAvecPhoto'];
$nom = $t['usNom'];

hr_aff_debut('Cuiteur | Blablas','../styles/cuiteur.css');
hr_aff_entete("Les blablas de $pseudo");

$id = $_SESSION['usID'];
$sql = "SELECT * FROM users WHERE usID = $id";
$res = hr_bd_send_request($bd,$sql);
rc_aff_infosV2($res);


$sql = 'SELECT  COUNT(blID) AS nbBlablas FROM blablas WHERE blIDAuteur='.$user_id;
$requet1 = hr_bd_send_request($bd,$sql);
$t1 = mysqli_fetch_assoc($requet1);
$nbBlablas = $t1['nbBlablas'];


hr_aff_user($bd,$_GET['id'],$t);



$sql = "SELECT  auteur.usID AS autID, auteur.usPseudo AS autPseudo, auteur.usNom AS autNom, auteur.usAvecPhoto AS autPhoto, 
        blTexte, blDate, blHeure,
        origin.usID AS oriID, origin.usPseudo AS oriPseudo, origin.usNom AS oriNom, origin.usAvecPhoto AS oriPhoto, blID
        FROM (users AS auteur
        LEFT OUTER JOIN blablas ON blIDAuteur = usID)
        LEFT OUTER JOIN users AS origin ON origin.usID = blIDAutOrig
        WHERE auteur.usID = $user_id
        ORDER BY blID DESC LIMIT $nb_blablas";
$requet = hr_bd_send_request($bd,$sql);
rc_aff_blablasV2($requet);



rc_aff_more_blablas($nb_blablas,'blablas.php',$nb_blablas_total,$user_id);


hr_aff_pied();
hr_aff_fin();




?>