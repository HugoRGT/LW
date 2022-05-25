<?php
ob_start(); //démarre la bufferisation
session_start();

require_once 'bibli_generale.php';
require_once 'bibli_cuiteur.php';

$bd = hr_bd_connect();

hr_aff_debut('Cuiteur | Rechercher','../styles/cuiteur.css');
hr_aff_entete('Rechercher des utilisateurs');

$id = $_SESSION['usID']  ;

$sql = "SELECT * FROM users WHERE usID = $id";
$res = hr_bd_send_request($bd,$sql);
rc_aff_infosV2($res);
$err = isset($_POST['btnRecherche']) ? rcl_traitement_recherche() : array(); 

rcl_aff_recherche($err);
if(count($err)===0 && isset($_POST['btnRecherche'])){
    rcl_aff_res_recherche();
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

hr_aff_pied();
hr_aff_fin();


mysqli_close($bd);

ob_end_flush();


/**
 * Affiche les resultats de la recherche
 */
function rcl_aff_res_recherche(): void{
    rc_aff_titre_section('Résultats de la recherche');
    $bd = hr_bd_connect();
    $search = $_POST['recherche'];
    $id = $_SESSION['usID'];

    $sql =  "SELECT usID,usPseudo,usAvecPhoto,usNom,usAvecPhoto,eaIDAbonne
            FROM users LEFT OUTER JOIN estabonne ON usID=eaIDAbonne AND eaIDuser=$id
            WHERE usPseudo LIKE '%$search%' OR usNom LIKE '%$search%'
            ORDER BY usPseudo";
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
    
        /*$sql="SELECT COUNT(*) AS NB
        FROM blablas
        WHERE blIDAuteur=$usID
    UNION
        (SELECT COUNT(*) AS NB
        FROM estabonne
        WHERE eaIDUser=$usID)
    UNION
        (SELECT COUNT(*) AS NB
        FROM estabonne
        WHERE eaIDAbonne=$usID)
    UNION
        (SELECT COUNT(*) AS NB
        FROM mentions
        WHERE meIDUser=$usID)";

        $requet = hr_bd_send_request($bd,$sql); 
        echo mysqli_num_rows($requet).'<br>';
        $tr = mysqli_fetch_array($requet);
        print_r($tr);
        $nbBlablas = $tr['nbBlablas'];
        echo $nbBlablas.'<br>';

        $tr = mysqli_fetch_array($requet);
        print_r($tr);
        $nbMentions = $tr['nbMentions'];
        echo $nbMentions.'<br>';

        $tr = mysqli_fetch_array($requet);
        print_r($tr);
        $nbAbonnee = $tr['nbAbonnee'];
        echo $nbAbonnee.'<br>';

        $tr = mysqli_fetch_array($requet);
        print_r($tr);
        $nbAbonnement = $tr['nbAbonnement'];
        echo $nbAbonnement.'<br>';*/
       


        if($t['usAvecPhoto'] == 1 && file_exists('../upload/'.$usID.'.jpg')){
            $img = '<img class="imgAuteur" src="../upload/'.$usID.'.jpg" alt="'.$usID.'.jpg" width="50px" height="50px">';
        }else{
            $img = '<img class="imgAuteur" src="../images/anonyme.jpg" alt="Default_User_Image" width="50px" height="50px">';
        }
        echo '<li class="liAbonnement">', 
        $img,
        hr_html_a('utilisateur.php', '<strong>'.hr_html_proteger_sortie($pseudo).'</strong>','id', $usID, 'Voir mes infos'), 
        ' ', hr_html_proteger_sortie($nom),
        '<br>',
        hr_html_a('blablas.php',hr_html_proteger_sortie($nbBlablas).' blablas','id',$usID,'Voir les blablas' ),
        ' - ',
        hr_html_a('mentions.php',hr_html_proteger_sortie($nbMentions).' mentions','id',$usID,'Voir les mentions' ),
        ' - ',
        hr_html_a('abonnes.php',hr_html_proteger_sortie($nbAbonnee).' abonnées','id',$usID,'Voir les abonnées' ),
        ' - ',
        hr_html_a('abonnements.php',hr_html_proteger_sortie($nbAbonnement).' abonnements','id',$usID,'Voir les abonnements' );


        if($usID !== $_SESSION['usID']){
                echo '<p class="finMessageAbo">',
                    '<input id="abonne" type="checkbox" name="'.$usID.'" value="'.($estAbonne===null ? 'abonne' : 'desabonne').'">',
                    '<label for="abonne"><strong>',($estAbonne===null ? 's\'abonner' : 'se désabonner'),'</strong></label>',
                '</p>';
        }else{
            echo '<p class="finMessageAbo">',
            '<br>',
            '</p>';
        }

        echo '</li>';
    }
    echo '<input id="validerAbo" type="submit" name="btnValider" value="Valider">',
        '</form>';
}

/**
 * Traite les erreurs saisie dans la barre de recherche
 */
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