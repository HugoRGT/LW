<?php

/*********************************************************
 *        Bibliothèque de fonctions spécifiques          *
 *               à l'application Cuiteur                 *
 *********************************************************/

 // Force l'affichage des erreurs
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting( E_ALL );

// Définit le fuseau horaire par défaut à utiliser. Disponible depuis PHP 5.1
date_default_timezone_set('Europe/Paris');

//définition de l'encodage des caractères pour les expressions rationnelles multi-octets
mb_regex_encoding ('UTF-8');

define('IS_DEV', true);//true en phase de développement, false en phase de production

 // Paramètres pour accéder à la base de données
define('BD_SERVER', 'localhost');
/*define('BD_NAME', 'cuiteur_bd');
define('BD_USER', 'cuiteur_userl');
define('BD_PASS', 'cuiteur_passl');*/
define('BD_NAME', 'rougetet_cuiteur');
define('BD_USER', 'rougetet_u');
define('BD_PASS', 'rougetet_p');


// paramètres de l'application
define('LMIN_PSEUDO', 4);
define('LMAX_PSEUDO', 30); //longueur du champ dans la base de données
define('LMAX_EMAIL', 80); //longueur du champ dans la base de données
define('LMAX_NOMPRENOM', 60); //longueur du champ dans la base de données


define('LMIN_PASSWORD', 4);
define('LMAX_PASSWORD', 20);

define('AGE_MIN', 18);
define('AGE_MAX', 120);

define('LMAX_VILLE', 50);
define('LMAX_BIO', 255);
define('LMAX_WEB', 120);


//_______________________________________________________________
/**
 * Génération et affichage de l'entete des pages
 *
 * @param ?string    $titre  Titre de l'entete (si null, affichage de l'entete de cuiteur.php avec le formulaire)
 */
function hr_aff_entete(?string $titre = null):void{
    echo '<div id="bcContenu">',
            '<header>',
                '<a href="deconnexion.php" title="Se déconnecter de cuiteur"></a>',
                '<a href="cuiteur.php" title="Ma page d\'accueil"></a>',
                '<a href="recherche.php" title="Rechercher des personnes à suivre"></a>',
                '<a href="compte.php" title="Modifier mes informations personnelles"></a>';
    if ($titre === null){
        echo    '<form action="../php/cuiteur.php" method="POST">',
                    '<textarea name="txtMessage"></textarea>',
                    '<input type="submit" name="btnPublier" value="" title="Publier mon message">',
                '</form>';
    }
    else{
        echo    '<h1>', $titre, '</h1>';
    }
    echo    '</header>';    
}
function hr_aff_entete_empty(?string $titre = null):void{
    echo '<div id="bcContenu">',
            '<header id="headerEmpty">';
    if ($titre === null){
        echo    '<form action="../php/cuiteur.php" method="POST">',
                    '<textarea name="txtMessage"></textarea>',
                    '<input type="submit" name="btnPublier" value="" title="Publier mon message">',
                '</form>';
    }
    else{
        echo    '<h1>', $titre, '</h1>';
    }
    echo    '</header>';    
}
function hr_aff_enteteV2(mixed $auteur ,?string $titre = null):void{
    echo '<div id="bcContenu">',
            '<header>',
                '<a href="deconnexion.php" title="Se déconnecter de cuiteur"></a>',
                '<a href="cuiteur.php" title="Ma page d\'accueil"></a>',
                '<a href="recherche.php" title="Rechercher des personnes à suivre"></a>',
                '<a href="compte.php" title="Modifier mes informations personnelles"></a>';
    if ($titre === null){
        echo    '<form action="../php/cuiteur.php" method="POST">',
                    '<textarea name="txtMessage">',($auteur===null ? '' : "@$auteur " ), '</textarea>',
                    '<input type="submit" name="btnPublier" value="" title="Publier mon message">',
                '</form>';
    }
    else{
        echo    '<h1>', $titre, '</h1>';
    }
    echo    '</header>';    
}
//_______________________________________________________________
/**
 * Génération et affichage du bloc d'informations utilisateur
 *
 * @param bool    $connecte  true si l'utilisateur courant s'est authentifié, false sinon
 */
function hr_aff_infos(bool $connecte = true):void{
    echo '<aside>';
    if ($connecte){
        echo
            '<h3>Utilisateur</h3>',
            '<ul>',
                '<li>',
                    '<img src="../images/pdac.jpg" alt="photo de l\'utilisateur">',
                    //'<a href="utilisateur.php" title="Voir les infos de l\'utilisateur">'.hr_html_proteger_sortie($t['usPseudo']).'</a> '.hr_html_proteger_sortie($t['usNom']).'',
                    '<a href="utilisateur.php" title="Voir les infos de l\'utilisateur">pdac</a> Pierre Dac',
                '</li>',
                '<li><a href="../index.html" title="Voir la liste de mes messages">100 blablas</a></li>',
                '<li><a href="../index.html" title="Voir les personnes que je suis">123 abonnements</a></li>',
                '<li><a href="../index.html" title="Voir les personnes qui me suivent">34 abonnés</a></li>',                 
            '</ul>',
            '<h3>Tendances</h3>',
            '<ul>',
                '<li>#<a href="../index.html" title="Voir les blablas contenant ce tag">info</a></li>',
                '<li>#<a href="../index.html" title="Voir les blablas contenant ce tag">lol</a></li>',
                '<li>#<a href="../index.html" title="Voir les blablas contenant ce tag">imbécile</a></li>',
                '<li>#<a href="../index.html" title="Voir les blablas contenant ce tag">fairelafete</a></li>',
                '<li><a href="../index.html">Toutes les tendances</a><li>',
            '</ul>',
            '<h3>Suggestions</h3>',             
            '<ul>',
                '<li>',
                    '<img src="../images/yoda.jpg" alt="photo de l\'utilisateur">',
                    '<a href="../index.html" title="Voir mes infos">yoda</a> Yoda',
                '</li>',       
                '<li>',
                    '<img src="../images/paulo.jpg" alt="photo de l\'utilisateur">',
                    '<a href="../index.html" title="Voir mes infos">paulo</a> Jean-Paul Sartre',
                '</li>',
                '<li><a href="../index.html">Plus de suggestions</a></li>',
            '</ul>';
    }
    echo '</aside>',
         '<main>';   
}
//_______________________________________________________________
/**
 * Génération et affichage du bloc d'informations utilisateur
 *
 * @param bool    $connecte  true si l'utilisateur courant s'est authentifié, false sinon
 */
function rc_aff_infosV2(mysqli_result $r):void{
    $t =  mysqli_fetch_assoc($r);
    $photo = $t['usAvecPhoto'];
    $id_user = $t['usID'];
    if($photo == 1 && file_exists('../upload/'.$_SESSION['usID'].'.jpg')){
        $img = '<img src="../upload/'.$_SESSION['usID'].'.jpg" alt="photo de l\'utilisateur">';
    }else{
        $img = '<img src="../images/anonyme.jpg" alt="photo de l\'utilisateur">';
    }
    $pseudo = $t['usPseudo'];
    $nom = $t['usNom'];
    $bd = hr_bd_connect();
    //GET COUNT BLABLAS
    $sql = "SELECT COUNT(*) AS NB FROM blablas WHERE blIDAuteur = $id_user";
    $res = hr_bd_send_request($bd,$sql);
    $tBla = mysqli_fetch_assoc($res);
    //GET COUNT ABONNES
    $sql = "SELECT COUNT(*) AS NB FROM estabonne WHERE eaIDAbonne = $id_user";
    $res = hr_bd_send_request($bd,$sql);
    $tFol = mysqli_fetch_assoc($res);
    //GET COUNT ABONNEMENTS
    $sql = "SELECT COUNT(*) AS NB FROM estabonne WHERE eaIDUser = $id_user";
    $res = hr_bd_send_request($bd,$sql);
    $tSub = mysqli_fetch_assoc($res);
    
    echo '<aside>';
        echo
            '<h3>Utilisateur</h3>', 
            '<ul>',
                '<li>',
                    $img,
                    hr_html_a('utilisateur.php',hr_html_proteger_sortie($pseudo),'id',$id_user,'Voir mes infos').' '.hr_html_proteger_sortie($nom),
                '</li>',
                '<li>'.hr_html_a('blablas.php',$tBla['NB'].' blablas','id',$id_user,'Voir la liste de mes messages').'</li>',
                '<li>'.hr_html_a('abonnements.php',$tSub['NB'].' abonnements','id',$id_user,'Voir les personnes que je suis').'</li>',
                '<li>'.hr_html_a('abonnes.php',$tFol['NB'].' abonnés','id',$id_user,'Voir les personnes qui me suivent').'</li>',                 
            '</ul>',
            '<h3>Tendances</h3>',
            '<ul>',
                '<li>#<a href="tendances.php" title="Voir les blablas contenant ce tag">info</a></li>',
                '<li>#<a href="tendances.php" title="Voir les blablas contenant ce tag">lol</a></li>',
                '<li>#<a href="tendances.php" title="Voir les blablas contenant ce tag">imbécile</a></li>',
                '<li>#<a href="tendances.php" title="Voir les blablas contenant ce tag">fairelafete</a></li>',
                '<li><a href="tendances.php">Toutes les tendances</a><li>',
            '</ul>',
            '<h3>Suggestions</h3>',             
            '<ul>',
                '<li>',
                    '<img src="../images/yoda.jpg" alt="photo de l\'utilisateur">',
                    '<a href="../index.html" title="Voir mes infos">yoda</a> Yoda',
                '</li>',       
                '<li>',
                    '<img src="../images/paulo.jpg" alt="photo de l\'utilisateur">',
                    '<a href="../index.html" title="Voir mes infos">paulo</a> Jean-Paul Sartre',
                '</li>',
                '<li><a href="../index.html">Plus de suggestions</a></li>',
            '</ul>';
    echo '</aside>',
         '<main>';   
}
//_______________________________________________________________
/**
 * Génération et affichage du pied de page
 *
 */
function hr_aff_pied(): void{
    echo    '</main>',
            '<footer>',
                '<a href="../index.html">A propos</a>',
                '<a href="../index.html">Publicité</a>',
                '<a href="../index.html">Patati</a>',
                '<a href="../index.html">Aide</a>',
                '<a href="../index.html">Patata</a>',
                '<a href="../index.html">Stages</a>',
                '<a href="../index.html">Emplois</a>',
                '<a href="../index.html">Confidentialité</a>',
            '</footer>',
    '</div>';
}

//_______________________________________________________________
/**
* Affichages des résultats des SELECT des blablas.
*
* La fonction gére la boucle de lecture des résultats et les
* encapsule dans du code HTML envoyé au navigateur 
*
* @param mysqli_result  $r       Objet permettant l'accès aux résultats de la requête SELECT
*/
function hr_aff_blablas(mysqli_result $r): void {
    while ($t = mysqli_fetch_assoc($r)) {
        if ($t['oriID'] === null){
            $id_orig = $t['autID'];
            $pseudo_orig = $t['autPseudo'];
            $photo = $t['autPhoto'];
            $nom_orig = $t['autNom'];
        }
        else{
            $id_orig = $t['oriID'];
            $pseudo_orig = $t['oriPseudo'];
            $photo = $t['oriPhoto'];
            $nom_orig = $t['oriNom'];
        }
        echo    '<li>', 
                    '<img src="../', ($photo == 1 ? "upload/$id_orig.jpg" : 'images/anonyme.jpg'), 
                    '" class="imgAuteur" alt="photo de l\'auteur">',
                    hr_html_a('utilisateur.php', '<strong>'.hr_html_proteger_sortie($pseudo_orig).'</strong>','id', $id_orig, 'Voir mes infos'), 
                    ' ', hr_html_proteger_sortie($nom_orig),
                    ($t['oriID'] !== null ? ', recuité par '
                                            .hr_html_a( 'utilisateur.php','<strong>'.hr_html_proteger_sortie($t['autPseudo']).'</strong>',
                                                        'id', $t['autID'], 'Voir mes infos') : ''),
                    '<br>',
                    hr_html_proteger_sortie($t['blTexte']),
                    '<p class="finMessage">',
                    hr_amj_clair($t['blDate']), ' à ', hr_heure_clair($t['blHeure']),
                    '<a href="../index.html">Répondre</a> <a href="../index.html">Recuiter</a></p>',
                '</li>';
    }
}
//_______________________________________________________________
/**
* Affichages des résultats des SELECT des blablas.
*
* La fonction gére la boucle de lecture des résultats et les
* encapsule dans du code HTML envoyé au navigateur 
*
* @param mysqli_result  $r       Objet permettant l'accès aux résultats de la requête SELECT
*/
function rc_aff_blablasV2(mysqli_result $r, ?string $id=null): void {
    while ($t = mysqli_fetch_assoc($r)) {
        if ($t['oriID'] === null){
            $id_orig = $t['autID'];
            $pseudo_orig = $t['autPseudo'];
            $photo = $t['autPhoto'];
            $nom_orig = $t['autNom'];
        }
        else{
            $id_orig = $t['oriID'];
            $pseudo_orig = $t['oriPseudo'];
            $photo = $t['oriPhoto'];
            $nom_orig = $t['oriNom'];
        }
        $blablas_id = $t['blID'];
        echo    '<li>', 
                    '<img src="../', ($photo == 1 ? "upload/$id_orig.jpg" : 'images/anonyme.jpg'), 
                    '" class="imgAuteur" alt="photo de l\'auteur">',
                    hr_html_a('utilisateur.php', '<strong>'.hr_html_proteger_sortie($pseudo_orig).'</strong>','id', $id_orig, 'Voir mes infos'), 
                    ' ', hr_html_proteger_sortie($nom_orig),
                    ($t['oriID'] !== null ? ', recuité par '
                                            .hr_html_a( 'utilisateur.php','<strong>'.hr_html_proteger_sortie($t['autPseudo']).'</strong>',
                                                        'id', $t['autID'], 'Voir mes infos') : ''),
                    '<br>',
                    hr_html_proteger_sortie($t['blTexte']),
                    '<p class="finMessage">',
                    hr_amj_clair($t['blDate']), ' à ', hr_heure_clair($t['blHeure']),
                    ($id !== $id_orig ? "<a href='./cuiteur.php?answer=true&auteur=$pseudo_orig&id=$id_orig'>Répondre</a> <a href='./cuiteur.php?recuit=true&aut=$id_orig&blID=$blablas_id'>Recuiter</a>" : 
                    "<a href='./cuiteur.php?delete=true&blID=$blablas_id'>Supprimer</a>"),
                    '</p>',
                '</li>';
    }
}

//_______________________________________________________________
/**
* Détermine si l'utilisateur est authentifié
*
* @global array    $_SESSION 
* @return bool     true si l'utilisateur est authentifié, false sinon
*/
function hr_est_authentifie(): bool {
    return  isset($_SESSION['usID']);
}

//_______________________________________________________________
/**
 * Termine une session et effectue une redirection vers la page transmise en paramètre
 *
 * Elle utilise :
 *   -   la fonction session_destroy() qui détruit la session existante
 *   -   la fonction session_unset() qui efface toutes les variables de session
 * Elle supprime également le cookie de session
 *
 * Cette fonction est appelée quand l'utilisateur se déconnecte "normalement" et quand une 
 * tentative de piratage est détectée. On pourrait améliorer l'application en différenciant ces
 * 2 situations. Et en cas de tentative de piratage, on pourrait faire des traitements pour 
 * stocker par exemple l'adresse IP, etc.
 * 
 * @param string    URL de la page vers laquelle l'utilisateur est redirigé
 */
function hr_session_exit(string $page = '../index.php'):void {
    session_destroy();
    session_unset();
    $cookieParams = session_get_cookie_params();
    setcookie(session_name(), 
            '', 
            time() - 86400,
            $cookieParams['path'], 
            $cookieParams['domain'],
            $cookieParams['secure'],
            $cookieParams['httponly']
        );
    header("Location: $page");
    exit();
}






/*
AFFICHER FORM INFORMATIONS PERSONNELLES
*/
function hr_aff_info_perso($bd,array $errs):void{
    if(isset($_POST['btnValiderPerso'])){
        if(count($errs) > 0){
            echo '<p class="error">Les erreurs suivantes ont été détectées :';
            foreach ($errs as $v) {
                echo '<br> - ', $v;
            }
            echo '</p>';    
        }
        if(count($errs) === 0){
            echo '<p class="success">La mise à jour des informations sur votre compte a bien été effectuée</p>'; 

            list($annee, $mois, $jour) = explode('-', $_POST['naissance']);
            $aaaammjj = $annee*10000  + $mois*100 + $jour;

            $nomprenom = hr_bd_proteger_entree($bd, $_POST['nomprenom']);

            $ville = hr_bd_proteger_entree($bd,$_POST['ville']);

            $bio = hr_bd_proteger_entree($bd,$_POST['bio']);

            $sql = "UPDATE users SET usNom='$nomprenom',usVille='$ville',usBio='$bio',usDateNaissance=$aaaammjj  WHERE usID = '".$_SESSION['usID']."'";
            hr_bd_send_request($bd,$sql);
        }
    }

    $sql = "SELECT * FROM users WHERE usID = '".$_SESSION['usID']."'";
    $res = hr_bd_send_request($bd,$sql);
    $t = mysqli_fetch_assoc($res);

    $dateChange = hr_amj_date($t['usDateNaissance']);
    $dateChange = strtr($dateChange,'/','-');
    $date = date('Y-m-d',strtotime(hr_html_proteger_sortie($dateChange)));

    echo '<form method="post" action="compte.php">',
                    '<table>';
                    hr_aff_ligne_input('Nom', array('type' => 'text', 'name' => 'nomprenom', 'value' => hr_html_proteger_sortie($t['usNom']), 'required' => null));
                    hr_aff_ligne_input('Date de naissance', array('type' => 'date', 'name' => 'naissance', 'value' => $date, 'required' => null));
hr_aff_ligne_input('Ville', array('type' => 'text', 'name' => 'ville', 'value' => hr_html_proteger_sortie($t['usVille'])));
    echo '<tr class="containerBio">',
            '<td ><label for="bio">Mini-bio</label></td>',
            '<td><textarea id="bio" name="bio" rows="13" cols="35">',hr_html_proteger_sortie($t['usBio']),'</textarea></td>',
        '</tr>';
    echo '<tr>',
            '<td colspan="2">',
                '<input type="submit" name="btnValiderPerso" value="Valider">',
            '</td>',
        '</tr>',
    '</table>',
    '</form>';
}










/*
AFFICHER FORM INFORMATIONS COMPTE CUITEUR
*/
function hr_aff_info_account($bd,array $errs):void{
    if(isset($_POST['btnValiderAccount'])){
        if(count($errs) > 0){
            echo '<p class="error">Les erreurs suivantes ont été détectées :';
            foreach ($errs as $v) {
                echo '<br> - ', $v;
            }
            echo '</p>';
        }
        if(count($errs) === 0){
            echo '<p class="success">La mise à jour des informations sur votre compte a bien été effectuée</p>'; 

            $mail = hr_bd_proteger_entree($bd, $_POST['mail']);

            $site = hr_bd_proteger_entree($bd, $_POST['site']);
            
            $sql = "UPDATE users SET usMail='$mail',usWeb='$site' WHERE usID = '".$_SESSION['usID']."'";
            hr_bd_send_request($bd,$sql);
        }
    }

    $sql = "SELECT * FROM users WHERE usID = '".$_SESSION['usID']."'";
    $res = hr_bd_send_request($bd,$sql);
    $t = mysqli_fetch_assoc($res);

    echo '<form method="post" action="compte.php">',
                    '<table>';
                    hr_aff_ligne_input('Adresse mail', array('type' => 'text', 'name' => 'mail', 'value' => hr_html_proteger_sortie($t['usMail']), 'required' => true));
                    hr_aff_ligne_input('Site web', array('type' => 'text', 'name' => 'site', 'value' => hr_html_proteger_sortie($t['usWeb'])));
    echo '<tr>',
            '<td colspan="2">',
                '<input type="submit" name="btnValiderAccount" value="Valider">',
            '</td>',
        '</tr>',
    '</table>',
    '</form>';
}








/*
AFFICHER FORM PARAMETRES COMPTE CUITEUR
*/
function hr_aff_settings_account($bd,array $errs):void{
    $sql = "SELECT * FROM users WHERE usID = '".$_SESSION['usID']."'";
    $res = hr_bd_send_request($bd,$sql);
    $t = mysqli_fetch_assoc($res);

    if(isset($_POST['btnValiderSettings'])){
        if(count($errs) > 0){
            echo '<p class="error">Les erreurs suivantes ont été détectées :';
            foreach ($errs as $v) {
                echo '<br> - ', $v;
            }
            echo '</p>';
        }
        if(count($errs) === 0){
            echo '<p class="success">La mise à jour des informations sur votre compte a bien été effectuée</p>';
            if(isset($_FILES['image']) && isset($_POST['usePhoto'])){
                $temp = explode(".", $_FILES['image']['name']);
                $newfilename = $_SESSION['usID']. '.' . end($temp);
                move_uploaded_file($_FILES['image']['tmp_name'],'../upload/'.$newfilename);
                if(file_exists('../upload/'.$_SESSION['usID'].'.jpg')){
                    $sql = "UPDATE users SET usAvecPhoto = 1 WHERE usID = ".$_SESSION['usID']."";
                    hr_bd_send_request($bd,$sql);
                }else{
                    $sql = "UPDATE users SET usAvecPhoto = 0 WHERE usID = ".$_SESSION['usID']."";
                    hr_bd_send_request($bd,$sql);
                }
            }

            if($_POST['passe1'] !== '' && $_POST['passe2'] !== ''){
                $password = hr_bd_proteger_entree($bd, password_hash($_POST['passe1'],PASSWORD_DEFAULT));
                $sql = "UPDATE users SET usPasse='$password' WHERE usID = '".$_SESSION['usID']."'";
                hr_bd_send_request($bd,$sql);
            }

        }
        $sql = "SELECT * FROM users WHERE usID = '".$_SESSION['usID']."'";
        $res = hr_bd_send_request($bd,$sql);
        $t = mysqli_fetch_assoc($res);
    }

    echo '<form method="post" action="compte.php" enctype="multipart/form-data">',
                    '<table>';
                    hr_aff_ligne_input('Changer le mot de passe : ', array('type' => 'password', 'name' => 'passe1', 'value' => NULL, null));
                    hr_aff_ligne_input('Répétez le mot de passe : ', array('type' => 'password', 'name' => 'passe2', 'value' => NULL, null));

    if($t['usAvecPhoto'] == 1 && file_exists('../upload/'.$_SESSION['usID'].'.jpg')){
        $img = '<img src="../upload/'.$_SESSION['usID'].'.jpg" alt="'.$_SESSION['usID'].'.jpg" width="50px" height="50px">';
    }else{
        $img = '<img src="../images/anonyme.jpg" alt="Default_User_Image" width="50px" height="50px">';
    }
    echo '<tr>',
        '<td><label for=""> Votre photo actuelle </label></td>',
        '<td>',
            $img,
            '<p>Taille 20ko maximum</p>',
            '<p>Image JPG carrée (mini 50x50px)</p>',
            '<input type="file" name="image">',
        '</td>',
    '</tr>'; 
       
    echo    '<tr>',
            '<td>','<label for="">','Utiliser votre photo','</label>','</td>',
            '<td>',
                '<input type="radio" name="dontUsePhoto" value=1><label for="non">non</label>',
                '<input type="radio" name="usePhoto"><label for="oui">oui</label>',
            '</td>',
        '</tr>';
    echo '<tr>',
            '<td colspan="2">',
                '<input type="submit" name="btnValiderSettings" value="Valider">',
            '</td>',
        '</tr>',
    '</table>',
    '</form>';
}

//
/**
 * Supprime un blablas
 * 
 */
function rc_delete_blablas($bd,$blID){
    $sql =  "DELETE FROM tags WHERE taIDBlabla=$blID;";
    $sql1 =  "DELETE FROM mentions WHERE meIDBlabla=$blID;";
    $sql2 =  "DELETE FROM blablas WHERE blID=$blID;";
            
    $requet = hr_bd_send_request($bd,$sql);
    $requet = hr_bd_send_request($bd,$sql1);
    $requet = hr_bd_send_request($bd,$sql2);
}
function rc_aff_more_blablas(int $nb_blablas): void {
    $nb_blablas += 4;
    echo    "<li class='plusBlablas'>
                <a href='./cuiteur.php?more=true&nb=$nb_blablas' ><strong>Plus de blablas</strong></a>
                <img src='../images/speaker.png' width='75' height='82' alt='Image du speaker '",'Plus de blablas',"'>
        </li>";
    echo '</ul>';
}
/**
 * 
 *
 */
function rc_aff_titre_section(string $titre):void{
    echo '<h2 class="titreAccountSettings">',$titre,'</h2>',
        '<img src="../images/trait.png" alt="trait.png"/>';
}
?>
