<?php

ob_start(); //démarre la bufferisation
session_start();

require_once 'bibli_generale.php';
require_once 'bibli_cuiteur.php';


$bd = hr_bd_connect();

$id = (int)$_GET['id']; //on n'est jamais trop prudent

$sql = "SELECT * FROM users WHERE usID = '".$id."'";
$res = hr_bd_send_request($bd, $sql);
$sql = "SELECT * FROM users WHERE usID = ".$_SESSION['usID']."";
$request = hr_bd_send_request($bd,$sql);
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

$t =  mysqli_fetch_assoc($res);

hr_aff_debut('Cuiteur | '.hr_html_proteger_sortie($t['usPseudo']).'','../styles/cuiteur.css');
hr_aff_entete('Le profil de '.$t['usPseudo']);
rc_aff_infosV2($request);

//AJOUT D'UN ABONNEMENT
if(isset($_POST['btnSAbonner'])){
    $follower = $_SESSION['usID'];
    $followed = $_GET['id'];
    $date_abonnement = date('Ymd');
    $sql = "INSERT INTO estabonne(eaIDUser,eaIDAbonne,eaDate) VALUES ($follower,$followed,$date_abonnement)";
    hr_bd_send_request($bd,$sql);
    Header('Refresh:0');
}

//RETIRE UN ABONNEMENT
if(isset($_POST['btnDesabonner'])){
    $follower = $_SESSION['usID'];
    $followed = $_GET['id'];
    $sql = "DELETE FROM estabonne WHERE eaIDUser = $follower AND eaIDAbonne = $followed";
    hr_bd_send_request($bd,$sql);
    Header('Refresh:0');
}

hrl_aff_user($bd,$id);

hr_aff_pied();
hr_aff_fin();

mysqli_close($bd);

function hrl_aff_user(mysqli $bd):void{
    //GET INFO USERS
    $sql = "SELECT * FROM users WHERE usID = '".$_GET['id']."'";
    $res = hr_bd_send_request($bd,$sql);
    $t = mysqli_fetch_assoc($res);
    //GET COUNT BLABLAS
    $sql = "SELECT COUNT(*) AS NB FROM blablas WHERE blIDAuteur = '".$_GET['id']."'";
    $res = hr_bd_send_request($bd,$sql);
    $tBla = mysqli_fetch_assoc($res);
    //GET COUNT MENTIONS
    $sql = "SELECT COUNT(*) AS NB FROM mentions WHERE meIDUser = '".$_GET['id']."'";
    $res = hr_bd_send_request($bd,$sql);
    $tMen = mysqli_fetch_assoc($res);
    //GET COUNT ABONNES
    $sql = "SELECT COUNT(*) AS NB FROM estabonne WHERE eaIDAbonne = '".$_GET['id']."'";
    $res = hr_bd_send_request($bd,$sql);
    $tFol = mysqli_fetch_assoc($res);
    //GET COUNT ABONNEMENTS
    $sql = "SELECT COUNT(*) AS NB FROM estabonne WHERE eaIDUser = '".$_GET['id']."'";
    $res = hr_bd_send_request($bd,$sql);
    $tSub = mysqli_fetch_assoc($res);

    if($t['usAvecPhoto'] == 1 && file_exists('../upload/'.$_GET['id'].'.jpg')){
        $img = '<img id="ppUsr" src="../upload/'.$_GET['id'].'.jpg" alt="photo de l\'utilisateur">';
    }else{
        $img = '<img id="ppUsr" src="../images/anonyme.jpg" alt="photo de l\'utilisateur">';
    }

    echo '<div class="profil">';
    echo $img,
    '<span>',
        hr_html_a('utilisateur.php',hr_html_proteger_sortie($t['usPseudo']),'id',$_GET['id'],'Voir les infos de l\'utilisateur').' '.hr_html_proteger_sortie($t['usNom']).'<br>',
        hr_html_a('blablas.php',hr_html_proteger_sortie($tBla['NB']).' blablas','id',$_GET['id'],'Voir les blablas de l\'utilisateur'),
        ' - ',
        hr_html_a('mentions.php',hr_html_proteger_sortie($tMen['NB']).' mentions','id',$_GET['id'],'Voir les mentions de l\'utilisateur'),
        ' - ',
        hr_html_a('abonnes.php',hr_html_proteger_sortie($tFol['NB']).' abonnés','id',$_GET['id'],'Voir les abonnés de l\'utilisateur'),
        ' - ',
        hr_html_a('abonnements.php',hr_html_proteger_sortie($tSub['NB']).' abonnements','id',$_GET['id'],'Voir les abonnements de l\'utilisateur'),
    '</span>';
    echo '</div>';

    $naissance = hr_html_proteger_sortie(hr_amj_clair($t['usDateNaissance'])) === '' ? 'Non renseigné(e)' : hr_html_proteger_sortie(hr_amj_clair($t['usDateNaissance']));
    $inscription = hr_html_proteger_sortie(hr_amj_clair($t['usDateInscription'])) === '' ? 'Non renseigné(e)' : hr_html_proteger_sortie(hr_amj_clair($t['usDateInscription']));
    $ville = $t['usVille'] === '' ? 'Non renseigné(e)' : hr_html_proteger_sortie($t['usVille']);
    $bio = $t['usBio'] === '' ? 'Non renseigné(e)' : hr_html_proteger_sortie($t['usBio']);
    $web = $t['usWeb'] === '' ? 'Non renseigné(e)' : hr_html_proteger_sortie($t['usWeb']);;

    echo '<form id="userInfo" method="post" action="utilisateur.php?id='.$_GET['id'].'">',
        '<table >',
        '<tr>',
            '<td><label for=""> Date de naissance : </label></td>',
            '<td><p>'.$naissance.'<p></td>',
        '</tr>',
        '<tr>',
            '<td><label for=""> Date d\'inscription : </label></td>',
            '<td><p>'.$inscription.'<p></td>',
        '</tr>',
        '<tr>',
            '<td><label for=""> Ville de résidence : </label></td>',
            '<td><p>'.$ville.'<p></td>',
        '</tr>',
        '<tr class="containerBio">',
            '<td><label for=""> Mini-bio : </label></td>',
            '<td><p>'.$bio.'<p></td>',
        '</tr>',
        '<tr>',
            '<td><label for=""> Site web : </label></td>',
            '<td><p>'.$web.'<p></td>',
        '</tr>',
        '<tr>';
    
    


    if($_GET['id'] === $_SESSION['usID']){
      echo  '</table>',
        '</form>';
    }else{
        hrl_aff_profil_not_user($bd,$t);
    }
}


function hrl_aff_profil_not_user($bd,array $t = array()):void{
    $follower = $_SESSION['usID'];
    $followed = $_GET['id'];
    $sql = "SELECT eaIDUser FROM estabonne WHERE eaIDUser = $follower AND eaIDAbonne = $followed";
    $res = hr_bd_send_request($bd,$sql);

    if(mysqli_num_rows($res) === 0){
        echo '<td colspan="2">',
                    '<input type="submit" name="btnSAbonner" value="S\'abonner">',
                '</td>',
                '</tr>',
            '</table>',
            '</form>';
    }else{
        echo '<td colspan="2">',
                    '<input type="submit" name="btnDesabonner" value="Se Désabonner">',
                '</td>',
                '</tr>',
            '</table>',
            '</form>';
    }
   
    
    
}



ob_end_flush();

?>
