<?php

ob_start(); //démarre la bufferisation
session_start();

require_once 'bibli_generale.php';
require_once 'bibli_cuiteur.php';

echo $_SESSION['usID'] ;
$bd = hr_bd_connect();

//INFORMATIONS USER GET
$id = (int)$_GET['id']; //on n'est jamais trop prudent
$sql = "SELECT * FROM users WHERE usID = '".$id."'";
$res = hr_bd_send_request($bd, $sql);//INFOS de GET['id']

//INFORMATIONS USER SESSION
$sql = "SELECT * FROM users WHERE usID = ".$_SESSION['usID']."";
$request = hr_bd_send_request($bd,$sql);//INFOS de SESSION['usID']



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

//TABLEAU INFO USER GET
$t =  mysqli_fetch_assoc($res);

hr_aff_debut('Cuiteur | Abonnements de '.hr_html_proteger_sortie($t['usPseudo']).'','../styles/cuiteur.css');
hr_aff_entete('Les abonnements de '.$t['usPseudo']);
rc_aff_infosV2($request);

$sql = "SELECT usID,usPSeudo
FROM (users INNER JOIN estabonne ON usID=eaIDAbonne) WHERE estabonne.eaIDuser=".$_GET['id']."";
$res = hr_bd_send_request($bd,$sql);//ABONNEMENTS DE GET['id']






echo '<form method="post" action="abonnements.php?id='.urlencode($_GET['id']).'">',
'<ul >';

if(isset($_POST['sub'.$_GET['id']])){
    hrl_sub_user($bd,$_GET['id'],$_SESSION['usID']);
}
if(isset($_POST['unsub'.$_GET['id']])){
    hrl_unsub_user($bd,$_GET['id'],$_SESSION['usID']);
}

//CHECK IF USER IS SUBSCRIBED TO $id
$sql = "SELECT * FROM estabonne WHERE eaIDUser = ".$_SESSION['usID']." AND eaIDAbonne = $id";
$requestSub = hr_bd_send_request($bd,$sql);

if(mysqli_num_rows($requestSub) === 0){//USER DON'T FOLLOW $id
    hrl_aff_user($bd,$_GET['id'],false,$_GET['id']===$_SESSION['usID']);
}else{
    hrl_aff_user($bd,$_GET['id'],true,$_GET['id']===$_SESSION['usID']);
}


$i = 0;
while($i < mysqli_num_rows($res)){
    $i++;
    $t = mysqli_fetch_assoc($res);
    if(isset($_POST['sub'.$t['usID']])){
        hrl_sub_user($bd,$t['usID'],$_SESSION['usID']);
    }
    if(isset($_POST['unsub'.$t['usID']])){
        hrl_unsub_user($bd,$t['usID'],$_SESSION['usID']);
    }
    hrl_aff_abonnements($bd,$t['usID']);
}

echo '<input id = "validerAbo" type="submit" name="btnValider" value="Valider">',
'</ul>',
'</form>';
if(isset($_POST['btnValider'])){
    Header('Refresh:0');
}

hr_aff_pied();
hr_aff_fin();

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







function hrl_aff_user(mysqli $bd,int $id,bool $sub,bool $is_user):void{
    //GET INFO USERS
    $sql = "SELECT * FROM users WHERE usID = $id";
    $res = hr_bd_send_request($bd,$sql);
    $t = mysqli_fetch_assoc($res);
    //GET COUNT BLABLAS
    $sql = "SELECT COUNT(*) AS NB FROM blablas WHERE blIDAuteur = $id";
    $res = hr_bd_send_request($bd,$sql);
    $tBla = mysqli_fetch_assoc($res);
    //GET COUNT MENTIONS
    $sql = "SELECT COUNT(*) AS NB FROM mentions WHERE meIDUser = $id";
    $res = hr_bd_send_request($bd,$sql);
    $tMen = mysqli_fetch_assoc($res);
    //GET COUNT ABONNES
    $sql = "SELECT COUNT(*) AS NB FROM estabonne WHERE eaIDAbonne = $id";
    $res = hr_bd_send_request($bd,$sql);
    $tFol = mysqli_fetch_assoc($res);
    //GET COUNT ABONNEMENTS
    $sql = "SELECT COUNT(*) AS NB FROM estabonne WHERE eaIDUser = $id";
    $res = hr_bd_send_request($bd,$sql);
    $tSub = mysqli_fetch_assoc($res);


    if($t['usAvecPhoto'] == 1 && file_exists('../upload/'.$id.'.jpg')){
        $img = '<img id="ppUsr" src="../upload/'.$id.'.jpg" alt="photo de l\'utilisateur">';
    }else{
        $img = '<img id="ppUsr" src="../images/anonyme.jpg" alt="photo de l\'utilisateur">';
    }

    echo '<div class="profil">';
    echo $img,
    '<span>',
        hr_html_a('utilisateur.php',hr_html_proteger_sortie($t['usPseudo']),'id',$id,'Voir les infos de l\'utilisateur').' '.hr_html_proteger_sortie($t['usNom']).'<br>',
        hr_html_a('blablas.php',hr_html_proteger_sortie($tBla['NB']).' blablas','id',$id,'Voir les blablas de l\'utilisateur'),
        ' - ',
        hr_html_a('mentions.php',hr_html_proteger_sortie($tMen['NB']).' mentions','id',$id,'Voir les mentions de l\'utilisateur'),
        ' - ',
        hr_html_a('abonnes.php',hr_html_proteger_sortie($tFol['NB']).' abonnés','id',$id,'Voir les abonnés de l\'utilisateur'),
        ' - ',
        hr_html_a('abonnements.php',hr_html_proteger_sortie($tSub['NB']).' abonnements','id',$id,'Voir les abonnements de l\'utilisateur'),
    '</span>';

    if(!$is_user){
        if($sub){
            echo '<p class="finMessageAbo">',
            '<span> </span>',
            '<input type="checkbox" name="unsub'.$id.'"><label><strong>Se désabonner</strong></label>',
            '</p>';
        }else{
            echo '<p class="finMessageAbo">',
            '<span> </span>',
            '<input type="checkbox" name="sub'.$id.'"><label><strong>S\'abonner</strong></label>',
            '</p>';
        } 
    }

    echo '</div>';
}










function hrl_aff_abonnements(mysqli $bd,int $id): void {
    $sql = "SELECT * FROM users WHERE usID = $id";
    $r = hr_bd_send_request($bd,$sql);
    $t = mysqli_fetch_assoc($r);
    //GET COUNT BLABLAS
     $sql = "SELECT COUNT(*) AS NB FROM blablas WHERE blIDAuteur = $id";
     $res = hr_bd_send_request($bd,$sql);
     $tBla = mysqli_fetch_assoc($res);
    //GET COUNT MENTIONS
     $sql = "SELECT COUNT(*) AS NB FROM mentions WHERE meIDUser = $id";
     $res = hr_bd_send_request($bd,$sql);
     $tMen = mysqli_fetch_assoc($res);
    //GET COUNT ABONNES
     $sql = "SELECT COUNT(*) AS NB FROM estabonne WHERE eaIDAbonne = $id";
     $res = hr_bd_send_request($bd,$sql);
     $tFol = mysqli_fetch_assoc($res);
    //GET COUNT ABONNEMENTS
     $sql = "SELECT COUNT(*) AS NB FROM estabonne WHERE eaIDUser = $id";
     $res = hr_bd_send_request($bd,$sql);
     $tSub = mysqli_fetch_assoc($res);





    $id = $t['usID'];
    $pseudo = $t['usPseudo'];
    $photo = $t['usAvecPhoto'];
    $nom = $t['usNom'];

    if($t['usAvecPhoto'] == 1 && file_exists('../upload/'.$id.'.jpg')){
        $img = '<img class="imgAuteur" src="../upload/'.$id.'.jpg" alt="'.$id.'.jpg" width="50px" height="50px">';
    }else{
        $img = '<img class="imgAuteur" src="../images/anonyme.jpg" alt="Default_User_Image" width="50px" height="50px">';
    }


    echo '<li class="liAbonnement">', 
    $img,
    hr_html_a('utilisateur.php', '<strong>'.hr_html_proteger_sortie($pseudo).'</strong>','id', $id, 'Voir mes infos'), 
    ' ', hr_html_proteger_sortie($nom),
    '<br>',
    hr_html_a('blablas.php',hr_html_proteger_sortie($tBla['NB']).' blablas','id',$id,'Voir les blablas de l\'utilisateur'),
    ' - ',
    hr_html_a('mentions.php',hr_html_proteger_sortie($tMen['NB']).' mentions','id',$id,'Voir les mentions de l\'utilisateur'),
    ' - ',
    hr_html_a('abonnes.php',hr_html_proteger_sortie($tFol['NB']).' abonnés','id',$id,'Voir les abonnés de l\'utilisateur'),
    ' - ',
    hr_html_a('abonnements.php',hr_html_proteger_sortie($tSub['NB']).' abonnements','id',$id,'Voir les abonnements de l\'utilisateur');


    
    //CHECK IF USER IS SUBSCRIBED TO $id
    $sql = "SELECT * FROM estabonne WHERE eaIDUser = ".$_SESSION['usID']." AND eaIDAbonne = $id";
    $res = hr_bd_send_request($bd,$sql);


    if(mysqli_num_rows($res) === 0){//USER DON'T FOLLOW $id
        echo '<p class="finMessageAbo">',
        '<span> </span>',
        '<input type="checkbox" name="sub'.$id.'"><label><strong>S\'abonner</strong></label>',
        '</p>';
    }else{
        echo '<p class="finMessageAbo">',
        '<span> </span>',
        '<input type="checkbox" name="unsub'.$id.'"><label><strong>Se désabonner</strong></label>',
        '</p>';
    }

    echo '</li>';
}



ob_end_flush();

?>
