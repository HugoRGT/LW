<?php
    ob_start();
    session_start();
    
    require_once 'bibli_generale.php';
    require_once 'bibli_cuiteur.php';
    
    hr_aff_debut('Cuiteur','../styles/cuiteur.css');

    if(!hr_est_authentifie()){
        header('Location:../index.php');
    }
    $bd = hr_bd_connect();
    $id = $_SESSION['usID'];

    if(isset($_POST['btnPublier']) || isset($_GET['recuit'])){
        rc_traitement_new_blablas();
    }
    $nb_blablas = 4; //nombre de blablas de base
    $sql = "SELECT  DISTINCT auteur.usID AS autID, auteur.usPseudo AS autPseudo, auteur.usNom AS autNom, auteur.usAvecPhoto AS autPhoto, 
            blTexte, blDate, blHeure,
            origin.usID AS oriID, origin.usPseudo AS oriPseudo, origin.usNom AS oriNom, origin.usAvecPhoto AS oriPhoto, blID
            FROM (((users AS auteur
            INNER JOIN blablas ON blIDAuteur = usID)
            LEFT OUTER JOIN users AS origin ON origin.usID = blIDAutOrig)
            LEFT OUTER JOIN estabonne ON auteur.usID = eaIDAbonne)
            LEFT OUTER JOIN mentions ON blID = meIDBlabla
            WHERE   auteur.usID = $id
            OR      eaIDUser = $id
            OR      meIDUser = $id
            ORDER BY blID DESC";  

    $requet = hr_bd_send_request($bd,$sql);
    $nb_blablas_total = mysqli_num_rows($requet);
    $auteur = null;
    if(!empty($_GET)){
        if(isset($_GET['more'])){
            $nb_blablas  = $_GET['nb'];
        }
        if(isset($_GET['answer'])){
            $auteur =  $_GET['auteur'];
        }
        if(isset($_GET['delete'])){
            $blID =  $_GET['blID'];
            rc_delete_blablas($bd,$blID);
        }
    }
    hr_aff_enteteV2($auteur,null);
    
    $sql = "SELECT * FROM users WHERE usID = ".$_SESSION['usID']."";
    $requet = hr_bd_send_request($bd,$sql);
    rc_aff_infosV2($requet);
    
    $sql = "SELECT  DISTINCT auteur.usID AS autID, auteur.usPseudo AS autPseudo, auteur.usNom AS autNom, auteur.usAvecPhoto AS autPhoto, 
            blTexte, blDate, blHeure,
            origin.usID AS oriID, origin.usPseudo AS oriPseudo, origin.usNom AS oriNom, origin.usAvecPhoto AS oriPhoto, blID
            FROM (((users AS auteur
            INNER JOIN blablas ON blIDAuteur = usID)
            LEFT OUTER JOIN users AS origin ON origin.usID = blIDAutOrig)
            LEFT OUTER JOIN estabonne ON auteur.usID = eaIDAbonne)
            LEFT OUTER JOIN mentions ON blID = meIDBlabla
            WHERE   auteur.usID = $id
            OR      eaIDUser = $id
            OR      meIDUser = $id
            ORDER BY blID DESC LIMIT $nb_blablas";
    $requet = hr_bd_send_request($bd,$sql);
    
    echo '<ul>';

    if (mysqli_num_rows($requet) == 0){
        echo '<li>Votre fil de blablas est vide</li>';
    }
    else{
        rc_aff_blablasV2($requet,$id);
        rc_aff_more_blablas($nb_blablas,'cuiteur.php',$nb_blablas_total);
    }
    echo '</ul>';
    hr_aff_pied();
    hr_aff_fin();
    ob_end_flush();
    
/*
 * Saisie et enregistrement d'un nouveau blabla
 */
function rc_traitement_new_blablas(): void{
    if(!hr_parametres_controle('post',array('txtMessage','btnPublier')) && !isset($_GET['recuit'])){
        hr_session_exit();
    }
    $bd = hr_bd_connect();
    $id = $_SESSION['usID'];
    $today = date('Ymd'); 
    $hours = date('H:i:s');
    $text = null;
    //GESTION DES RECUIT
    if(isset($_GET['recuit'])){
        $blIDAutOrig = $_GET['aut'];
        $blID = $_GET['blID'];
        $sql = "SELECT blTexte FROM blablas WHERE blID = '$blID'";
        $requet = hr_bd_send_request($bd,$sql);
        $t =  mysqli_fetch_assoc($requet);
        $text = $t['blTexte'];
        $sql = "INSERT INTO blablas(blTexte,blIDAuteur,blDate,blHeure,blIDAutOrig) VALUES ('$text','$id','$today','$hours','$blIDAutOrig')";
    }else{
        $text = mysqli_real_escape_string($bd,$_POST['txtMessage']);
        $sql = "INSERT INTO blablas(blTexte,blIDAuteur,blDate,blHeure) VALUES ('$text','$id','$today','$hours')";
    }
    
    hr_bd_send_request($bd,$sql);
    /** GESTION DES MENTIONS */
    /**utiliser mb_ereg_match ?? */
    //mb_ereg_match('@[[:alnum:]] {'.LMIN_PSEUDO.','.LMAX_PSEUDO.'}', $_POST['txtMessage']);
    preg_match_all('/(@[[:alnum:]]{'.LMIN_PSEUDO.','.LMAX_PSEUDO.'})*/', $text, $matches);
    $len = count($matches[0]);
    for($i = 0; $i<$len; $i++){
        if($matches[0][$i] !== ''){
            $mention = $matches[0][$i];
            $pseudo = substr($mention,1);
            $sql =  "SELECT * FROM users WHERE usPseudo = '$pseudo'";
            $requet = hr_bd_send_request($bd,$sql);;
            $t = mysqli_fetch_assoc($requet);
            if($t !==null){
                $mention_id = $t['usID'];
                $sql = "SELECT * FROM blablas WHERE blIDAuteur = $id ORDER by blID DESC LIMIT 1;";
                $requet1 = hr_bd_send_request($bd,$sql);
                $t1 = mysqli_fetch_assoc($requet1);
                $blID = $t1['blID'];
                $sql = "INSERT INTO mentions(meIDUser,meIDBlabla) VALUES ($mention_id, $blID)";
                $requet1 = hr_bd_send_request($bd,$sql);
            }
        }
    }
    /** GESTION DES TAGS */
    preg_match_all('/(#[[:alnum:]]{1,60})*/', $text, $matches);
    $len = count($matches[0]);
    for($i = 0; $i<$len; $i++){
        if($matches[0][$i] !== ''){
            $tags = substr($matches[0][$i],1);
            $sql = "SELECT * FROM blablas WHERE blIDAuteur = $id ORDER by blID DESC LIMIT 1;";
            $requet1 = hr_bd_send_request($bd,$sql);
            $t = mysqli_fetch_assoc($requet1);
            $blID = $t['blID'];
            $sql = "INSERT INTO tags(taID,taIDBlabla) VALUES ('$tags', $blID)";
            $requet1 = hr_bd_send_request($bd,$sql);
        }
    }
}


?>