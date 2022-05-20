<?php
ob_start(); //démarre la bufferisation
session_start();

require_once 'bibli_generale.php';
require_once 'bibli_cuiteur.php';

$bd = hr_bd_connect();
//$id = (int)$_GET['id'];

hr_aff_debut('Cuiteur | Rechercher','../styles/cuiteur.css');
hr_aff_entete('Rechercher des utilisateurs');

$sql = "SELECT * FROM users WHERE usID = ".$_SESSION['usID']."";
$res = hr_bd_send_request($bd,$sql);
rc_aff_infosV2($res);
$err = isset($_POST['btnRecherche']) ? rcl_traitement_recherche() : array(); 

rcl_aff_recherche($err);
if(count($err)<0){
    rcl_aff_res_recherche();
}

$bd = hr_bd_connect();

if(isset($_POST['btnValider'])){    
    foreach ($_POST as $c => $v) {
        if($c !== 'btnValider'){
            if($v === 'abonne'){
                $date = $today = date('Ymd'); 
                $sql = 'INSERT INTO estabonne(eaIDUser,eaIDAbonne,eaDate) VALUE (2,'.$c.','.$today.')';
                hr_bd_send_request($bd,$sql);
            }else{
                    $sql = 'DELETE FROM estabonne WHERE eaIDUser=2 AND eaIDAbonne='.$c;
                    hr_bd_send_request($bd,$sql);
            }
        }
       
    }
    var_dump($_POST);
}
hr_aff_pied();
hr_aff_fin();

ob_end_flush();

function rcl_aff_recherche(array $err): void{
    if(isset($_POST['btnRecherche'])){
        $value = hr_html_proteger_sortie($_POST['recherche']);
    }else{
        $value = '';
    }
    if(count($err)>0){
        echo '<p class="error">Les erreurs suivantes ont été détectées :';
        foreach ($err as $v) {
            echo '<br> - ', $v;
        }
        echo '</p>';
    }
    echo    '<form method="post" action="recherche.php">
                <input id="textrecherche" type="text" name="recherche" value=',$value,' >
                <input type="submit" name="btnRecherche" value="Rechercher">
            </form>';
    
}

function rcl_aff_res_recherche(): void{
    rc_aff_titre_section('Résultats de la recherche');
    $bd = hr_bd_connect();
    $search = $_POST['recherche'];
    $id = $_SESSION['usID']; //faire en sorte que id soit transmis par $_SESSION !!!!!!!!!!!!!!

    //$sql = 'SELECT usID,usPseudo,usAvecPhoto,usNom,COUNT(blID) AS nbBlablas FROM users,blablas WHERE usPseudo = \''.$search.'\' AND blIDAuteur=usID;';
    $sql =  'SELECT usID,usPseudo,usAvecPhoto,usNom,usAvecPhoto,eaIDAbonne
            FROM users LEFT OUTER JOIN estabonne ON usID=eaIDAbonne AND eaIDuser='.$id.'
            WHERE usPseudo LIKE \'%'.$search.'%\' OR usNom LIKE \'%'.$search.'%\'
            ORDER BY usPseudo';
    $requet = hr_bd_send_request($bd,$sql);
    
    echo '<form method="post" action="recherche.php">';
    while($t = mysqli_fetch_assoc($requet)){
        $usID = $t['usID'];
        $pseudo = $t['usPseudo'];
        $photo = $t['usAvecPhoto'];
        $nom = $t['usNom'];
        $estAbonne = $t['eaIDAbonne'];
      

        $sql = 'SELECT  COUNT(blID) AS nbBlablas FROM blablas WHERE blIDAuteur='.$usID;
        $requet1 = hr_bd_send_request($bd,$sql);
        $t1 = mysqli_fetch_assoc($requet1);
        $nbBlablas = $t1['nbBlablas'];

        $sql = 'SELECT COUNT(meIDBlabla) AS nbMention FROM mentions WHERE  meIDUser='.$usID ;
        $requet2 = hr_bd_send_request($bd,$sql);
        $t2 = mysqli_fetch_assoc($requet2);
        $nbMentions = $t2['nbMention'];

        $sql = 'SELECT COUNT(eaIDUser) AS nbAbonnee FROM estabonne WHERE eaIDAbonne='.$usID ;
        $requet3 = hr_bd_send_request($bd,$sql);
        $t3 = mysqli_fetch_assoc($requet3);
        $nbAbonnee = $t3['nbAbonnee'];

        $sql = 'SELECT COUNT(eaIDAbonne) AS nbAbonnement FROM estabonne WHERE eaIDUser='.$usID ;
        $requet4 = hr_bd_send_request($bd,$sql);
        $t4 = mysqli_fetch_assoc($requet4);
        $nbAbonnement = $t4['nbAbonnement'];


        echo    '<li>
                    <img src="../', ($photo == 1 ? "upload/$usID.jpg" : 'images/anonyme.jpg'), 
                    '" class="imgAuteur" alt="photo de l\'auteur">',
                    hr_html_a('utilisateur.php',hr_html_proteger_sortie($pseudo),'id', $usID, 'Voir mes infos'), 
                    ' ', hr_html_proteger_sortie($nom),
                    '<br>',
                    hr_html_a('blablas.php',hr_html_proteger_sortie($nbBlablas).' blablas','id',$usID,'Voir les blablas' ),
                    ' - ',
                    hr_html_a('mentions.php',hr_html_proteger_sortie($nbMentions).' mentions','id',$usID,'Voir les mentions' ),
                    ' - ',
                    hr_html_a('abonnes.php',hr_html_proteger_sortie($nbAbonnee).' abonnées','id',$usID,'Voir les abonnées' ),
                    ' - ',
                    hr_html_a('abonnements.php',hr_html_proteger_sortie($nbAbonnement).' abonnements','id',$usID,'Voir les abonnements' ),
                    '<br>',
                    '<input id="abonne" type="checkbox" name="'.$usID.'" value="'.($estAbonne===null ? 'abonne' : 'desabonne').'"><label for="abonne"><strong>',($estAbonne===null ? 's\'abonner' : 'se désabonner'),'</strong></label>',
                '</li>';
    }
    echo        '<input type="submit" name="btnValider">',
            '</form>';
}
function rcl_traitement_recherche(): array {
    if(!hr_parametres_controle('post', array('recherche','btnRecherche')) || isHTML($_POST['recherche']) ){
        hr_session_exit();
    }
    foreach($_POST as &$val){
        $val = trim($val);
    }
    $err = array();
    if(mb_strlen($_POST['recherche'])===0){
        $err['text'] = 'votre recherche est vide';
    }
    if(count($err)>0){
        return $err;
    }
    return $err;
}
?>