<?php

ob_start();
session_start();

require_once 'bibli_cuiteur.php';
require_once 'bibli_generale.php';


if(!hr_est_authentifie()){
    header('Location:../index.php');
}

$bd = hr_bd_connect();
$id = $_SESSION['usID'];
$sql = "SELECT * FROM users WHERE usID =$id";
$requet = hr_bd_send_request($bd,$sql);

if(isset($_POST['btnValider'])){ 
    //print_r($_POST);
    /**
     * Traitements des abonnements et désabonnements
     */
    $date = $today = date('Ymd');
    $insert = "INSERT INTO estabonne(eaIDUser,eaIDAbonne,eaDate) VALUE ";   
    $delete = "DELETE from estabonne WHERE eaIDUser=$id AND  eaIDAbonne IN (";
    $sub = array();
    $unsub = array();
    foreach ($_POST as $c => $v) {
        if($c !== 'btnValider'){
            ($v === 'abonne' ? array_push($sub,$c) : array_push($unsub,$c));
        }
    }
    $size = count($sub);
    if($size > 0){
        for($i = 0 ; $i<$size; $i++){
            ($i === $size-1 ? $insert .= " ($id,$sub[$i],$today)" : $insert .= " ($id,$sub[$i],$today),");    
        }
        hr_bd_send_request($bd,$insert);
    }
    $size = count($unsub);
    if($size > 0){
        for($i = 0 ; $i<$size; $i++){
            ($i === $size-1 ? $delete .= "$unsub[$i]" :  $delete .= "$unsub[$i]," );
        }
        $delete.=')';
        hr_bd_send_request($bd,$delete);
    }
    header('Location:cuiteur.php');
    exit();
}

hr_aff_debut('Cuiteur | Suggestion','../styles/cuiteur.css');
hr_aff_entete('Suggestions');
rc_aff_infosV2($requet);



$nbUserMax = 5;

$sql =      "SELECT DISTINCT usID, usPseudo,usNom, usAvecPhoto
            FROM users INNER JOIN estabonne ON usID=eaIDAbonne
            WHERE eaIDUser IN (SELECT eaIDAbonne FROM estabonne WHERE eaIDuser=$id)
            AND usID!=$id
            AND usID NOT IN (SELECT eaIDAbonne FROM estabonne WHERE eaIDuser=$id)
            ORDER BY usID LIMIT $nbUserMax";
$requet = hr_bd_send_request($bd,$sql);

$nbUser = mysqli_num_rows($requet);
if($nbUser<$nbUserMax){
    $nbUserTest = $nbUserMax - $nbUser;
    $sql = "SELECT usID, usPseudo, COUNT(usID), dejaAbonne.eaIDUser AS eaIDUser
            FROM (users INNER JOIN estabonne ON usID=eaIDUser) LEFT OUTER JOIN estabonne AS dejaAbonne ON usID=dejaAbonne.eaIDAbonne 
            AND dejaAbonne.eaIDUser=$id
            GROUP BY usID
            ORDER BY COUNT(usID) DESC
            LIMIT $nbUserTest";
}
$requet1 = hr_bd_send_request($bd,$sql);

rc_aff_suggestion($requet,$requet1);


hr_aff_fin();
hr_aff_pied();

function rc_aff_suggestion(mysqli_result $r,mysqli_result $r1){
    /*echo mysqli_num_rows($r);
    echo mysqli_num_rows($r1);*/
    $bd=hr_bd_connect();
    echo '<form method="post" action="suggestion.php">';
    echo '<ul>';
    while($t = mysqli_fetch_assoc($r)){
        $user_id = $t['usID'];
        $nom = $t['usNom'];
        $pseudo = $t['usPseudo'];
        $photo = $t['usAvecPhoto'];
        hr_aff_abo($bd,$user_id);
    }
    while($t = mysqli_fetch_assoc($r1)){
        $user_id = $t['usID'];
        if($user_id !== $_SESSION['usID'] && $t['eaIDUser'] === NULL){
            hr_aff_abo($bd,$user_id);
        }
    }
    echo '</ul>';

    if(mysqli_num_rows($r) === 0 && mysqli_num_rows($r1)){
        echo '<p>AUCUNE SUGGESTION DISPONIBLE</p>';
    }else{
        echo '<input id="validerAbo" type="submit" name="btnValider" value="Valider">';
    }
    

    echo '</form>';
}

?>