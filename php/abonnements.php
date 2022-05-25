<?php

ob_start(); //démarre la bufferisation
session_start();

require_once 'bibli_generale.php';
require_once 'bibli_cuiteur.php';

$bd = hr_bd_connect();

//INFORMATIONS USER GET
$id = (int)$_GET['id']; //on n'est jamais trop prudent
$sql = "SELECT * FROM users WHERE usID = '".$id."'";
$res = hr_bd_send_request($bd, $sql);//INFOS de GET['id']
$t =  mysqli_fetch_assoc($res);

//UTILISATEUR INTROUVABLE
if (mysqli_num_rows($res) == 0){
    // libération des ressources
    mysqli_free_result($res);

    hr_aff_debut('Cuiteur | utilisateur introuvable','../styles/cuiteur.css');
    hr_aff_entete('utilisateur introuvable');
    rc_aff_infosV2($request);
    echo    '<ul>',
                '<li>L\'utilisateur ', $id, ' n\'existe pas</li>',
            '</ul>';
    hr_aff_pied();
    mysqli_close($bd);
    hr_aff_fin();
    exit;   //==> FIN DU SCRIPT
}

//INFORMATIONS USER SESSION
$sql = "SELECT * FROM users WHERE usID = ".$_SESSION['usID']."";
$request = hr_bd_send_request($bd,$sql);






hr_aff_debut('Cuiteur | Abonnements de '.hr_html_proteger_sortie($t['usPseudo']).'','../styles/cuiteur.css');
hr_aff_entete('Les abonnements de '.$t['usPseudo']);
rc_aff_infosV2($request);






//RECUPERE ABONNEMENTS DE USER GET
$sql = "SELECT usID,usPSeudo
        FROM (users INNER JOIN estabonne ON usID=eaIDAbonne) 
        WHERE estabonne.eaIDuser=$id
        ORDER BY usPseudo";
$res = hr_bd_send_request($bd,$sql);//ABONNEMENTS DE GET['id']


echo '<form method="post" action="abonnements.php?id='.urlencode($_GET['id']).'">',
'<ul >';

// VERIFIE SI L'UTILISATEUR SESSION VEUT S'ABONNER OU SE DESABONNER DE L'UTILISATEUR GET
/*if(isset($_POST[$_GET['id']])){
    hrl_sub_user($bd,$_GET['id'],$_SESSION['usID']);
}
if(isset($_POST[$_GET['id']])){
    hrl_unsub_user($bd,$_GET['id'],$_SESSION['usID']);
}

//VERIFIE SI USER SESSION EST ABONNE A USER GET
$sql = "SELECT * FROM estabonne WHERE eaIDUser = ".$_SESSION['usID']." AND eaIDAbonne = ".$_GET['id']."";
$requestSub = hr_bd_send_request($bd,$sql);

if(mysqli_num_rows($requestSub) === 0){//USER DON'T FOLLOW $id
    hr_aff_user($bd,$_GET['id'],$t,false,$_GET['id']===$_SESSION['usID']);
}else{
    hr_aff_user($bd,$_GET['id'],$t,true,$_GET['id']===$_SESSION['usID']);
}


$i = 0;
while($i < mysqli_num_rows($res)){
    $i++;
    $t = mysqli_fetch_assoc($res);
    if(isset($_POST['btnValider'])){
        if(isset($_POST[$t['usID']])){
            hrl_sub_user($bd,$t['usID'],$_SESSION['usID']);
        }
        if(isset($_POST[$t['usID']])){
            hrl_unsub_user($bd,$t['usID'],$_SESSION['usID']);
        }
    }
    hr_aff_abo($bd,$t['usID']);
}
//REDIRECTION SUR LA PAGE cuiteur.php SI APPUIE SUR BOUTON VALIDER
if(isset($_POST['btnValider'])){
    Header('Location:cuiteur.php');
}*/
//VERIFIE SI USER SESSION EST ABONNE A USER GET
$sql = "SELECT * FROM estabonne WHERE eaIDUser = ".$_SESSION['usID']." AND eaIDAbonne = ".$_GET['id']."";
$requestSub = hr_bd_send_request($bd,$sql);
if(mysqli_num_rows($requestSub) === 0){//USER DON'T FOLLOW $id
    hr_aff_user($bd,$_GET['id'],$t,false,$_GET['id']===$_SESSION['usID']);
}else{
    hr_aff_user($bd,$_GET['id'],$t,true,$_GET['id']===$_SESSION['usID']);
}
if(isset($_POST['btnValider'])){ 
    print_r($_POST);
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
            echo '<br>'.$v;
            ($v === 'abonne' ? array_push($sub,$c) : array_push($unsub,$c));
        }
    }
    echo '<br>';print_r($sub);
    $size = count($sub);
    if($size > 0){
        for($i = 0 ; $i<$size; $i++){
            ($i === $size-1 ? $insert .= "($id,$sub[$i],$today)" : $insert .= "($id,$sub[$i],$today),");    
        }
        hr_bd_send_request($bd,$insert);
    }
    echo '<br>';print_r($unsub);
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
$i = 0;
while($i < mysqli_num_rows($res)){
    $i++;
    $t = mysqli_fetch_assoc($res);
    hr_aff_abo($bd,$t['usID']);
}

echo '<input id="validerAbo" type="submit" name="btnValider" value="Valider">',
'</ul>',
'</form>';









hr_aff_pied();
hr_aff_fin();



mysqli_free_result($res);
mysqli_close($bd);








function hrl_sub_user(mysqli $bd,int $id,int $user):void{
    $date_abonnement = date('Ymd');
    $sql = "INSERT INTO estabonne(eaIDUser,eaIDAbonne,eaDate) VALUES ($user,$id,$date_abonnement)";
    hr_bd_send_request($bd,$sql);
}

function hrl_unsub_user(mysqli $bd,int $id,int $user):void{
    $sql = "DELETE FROM estabonne WHERE eaIDUser = $user AND eaIDAbonne = $id";
    hr_bd_send_request($bd,$sql);
}

ob_end_flush();

?>
