<?php
    session_start();

    require_once('database.php');

    $reqUserTest = $bdd->prepare('SELECT IdUtilisateur FROM utilisateur WHERE IdUtilisateur = ?');
    $reqUserTest->execute([$_SESSION["IdUtilisateur"]]);
    if(($reqUserTest->rowCount() == 0 && isset($_SESSION["IdUtilisateur"]))){
        session_destroy();
        header('Location: index.php');
    }

    $_SESSION['RedirectionUrl'] = $_SERVER['REQUEST_URI'];

    if(isset($_POST['english'])){
        $_SESSION['Language'] = "en";
        header("Refresh:0");
    }else if(isset($_POST['arabic'])){
        $_SESSION['Language'] = "ar";
        header("Refresh:0");
    }else if(isset($_POST['french'])){
        $_SESSION['Language'] = "fr";
        header("Refresh:0");
    }
    
    if($_SESSION["Language"]!=null){
        if($_SESSION["Language"] == "en")
            $locale = "en_US.UTF-8";
        else if($_SESSION["Language"] == "ar")
        $locale = "ar_SY.utf8";
    }else{
        $locale = "";
    }


    if (defined('LC_MESSAGES')) {
        setlocale(LC_MESSAGES, $locale);
        bindtextdomain("messages", "./locale");
    }
    textdomain("messages");  


    $requserProfile = $bdd->prepare("SELECT * FROM utilisateur WHERE IdUtilisateur  = ?");
    $requserProfile->execute(array($_GET['id']));
    $userexist = $requserProfile->rowCount();

    $reqmoderateur = $bdd->prepare('SELECT utilisateur.IdUtilisateur FROM utilisateur INNER JOIN moderateur ON utilisateur.IdUtilisateur = moderateur.IdUtilisateur WHERE utilisateur.IdUtilisateur = ? ');
    $reqmoderateur->execute(array($_SESSION['IdUtilisateur']));
    $ismoderateur = $reqmoderateur->fetch();
    
    if($userexist == 1) {
        $UserProfileInfo = $requserProfile->fetch();
        $_UserProfile['IdUtilisateur'] = $UserProfileInfo['IdUtilisateur'];
        $_UserProfile['NomUtilisateur'] = $UserProfileInfo['NomUtilisateur'];
        $_UserProfile['PseudoUtilisateur'] = $UserProfileInfo['PseudoUtilisateur'];
        $_UserProfile['MailUtilisateur'] = $UserProfileInfo['MailUtilisateur'];
        $_UserProfile['AvatarUtilisateur'] = $UserProfileInfo['AvatarUtilisateur'];
        $_UserProfile['DescriptionUtilisateur'] = $UserProfileInfo['DescriptionUtilisateur'];
        $_UserProfile['DateInscriptionUtilisateur'] = $UserProfileInfo['DateInscriptionUtilisateur'];
        $_UserProfile["SignalementUtilisateur"]=$UserProfileInfo['SignalementUtilisateur'];
    }

    if(isset($_SESSION['IdUtilisateur']) AND $_SESSION['IdUtilisateur'] > 0) {
        $getid = intval($_SESSION['IdUtilisateur']);
        $requser = $bdd->prepare('SELECT * FROM utilisateur WHERE IdUtilisateur = ?');
        $requser->execute(array($getid));
        $userinfo = $requser->fetch();
    }
    if(empty($_GET['id'])){
        $_UserProfile['IdUtilisateur']= $userinfo['IdUtilisateur'];
        $_UserProfile['NomUtilisateur'] = $userinfo['NomUtilisateur'];
        $_UserProfile['PseudoUtilisateur'] =  $userinfo['PseudoUtilisateur'];
        $_UserProfile['MailUtilisateur'] = $userinfo['MailUtilisateur'];
        $_UserProfile['AvatarUtilisateur'] = $userinfo['AvatarUtilisateur'];
        $_UserProfile['DescriptionUtilisateur'] = $userinfo['DescriptionUtilisateur'];
        $_UserProfile['DateInscriptionUtilisateur'] = $userinfo['DateInscriptionUtilisateur'];
        $_UserProfile["SignalementUtilisateur"]=$userinfo['SignalementUtilisateur'];
    }

	if(isset($_POST['formnom'])) {
        if(isset($_POST['newnom']) AND !empty($_POST['newnom']) AND $_POST['newnom'] != $user['NomUtilisateur']) {
            $newnom = htmlspecialchars($_POST['newnom']);
            $nomlength = strlen($newnom);
            if($nomlength <= 255) {
			$insertnom = $bdd->prepare("UPDATE utilisateur SET NomUtilisateur = ? WHERE IdUtilisateur = ?");
			$insertnom->execute(array($newnom, $_SESSION['IdUtilisateur']));
            header('Location: profil.php');
            }else{
                $erreur = _("Votre nom ne peut pas dépasser 255 caractères !");
            }
         }
    }
    
    if(isset($_POST['formpseudo'])) {
        if(isset($_POST['newpseudo']) AND !empty($_POST['newpseudo']) AND $_POST['newpseudo'] != $user['PseudoUtilisateur']) {
            $newpseudo = htmlspecialchars($_POST['newpseudo']);
            $pseudolength = strlen($newpseudo);
            if($pseudolength <= 255) {
                $reqpseudo = $bdd->prepare('SELECT * FROM utilisateur WHERE PseudoUtilisateur = ?');
	            $reqpseudo->execute(array($newpseudo));
                $pseudoexist = $reqpseudo->rowCount();
                if($pseudoexist == 0) {
			        $insertnom = $bdd->prepare("UPDATE utilisateur SET PseudoUtilisateur = ? WHERE IdUtilisateur = ?");
			        $insertnom->execute(array($newpseudo, $_SESSION['IdUtilisateur']));
                    header('Location: profil.php');
                }else{
                    $erreur = _("Pseudo déjà utilisé");
                }
            }else{
                $erreur = _("Votre pseudo ne peut pas dépasser 255 caractères !");
            }
        }
    }

    if(isset($_POST['formdescription'])) {
        if(isset($_POST['newdescription']) AND !empty($_POST['newdescription']) AND $_POST['newdescription'] != $user['DescriptionUtilisateur']) {
            $newdescription = htmlspecialchars($_POST['newdescription']);
            $newdescriptionlength = strlen($newdescription);
            if($newdescriptionlength <= 300) {
			$insertnom = $bdd->prepare("UPDATE utilisateur SET DescriptionUtilisateur = ? WHERE IdUtilisateur = ?");
			$insertnom->execute(array($newdescription, $_SESSION['IdUtilisateur']));
            header('Location: profil.php');
            }else{
                $erreur = _("Votre description ne peut pas dépasser 255 caractères !");
            }
         }
    }

    if(isset($_POST['formmail'])) {
        if(isset($_POST['newmail']) AND !empty($_POST['newmail']) AND $_POST['newmail'] != $user['MailUtilisateur']) {
            $newmail = htmlspecialchars($_POST['newmail']);
            $reqmail = $bdd->prepare('SELECT * FROM utilisateur WHERE MailUtilisateur = ?');
	        $reqmail->execute(array($newmail));
            $mailexist = $reqmail->rowCount();
            if($mailexist == 0){
			$insertnom = $bdd->prepare("UPDATE utilisateur SET MailUtilisateur = ? WHERE IdUtilisateur = ?");
			$insertnom->execute(array($newmail, $_SESSION['IdUtilisateur']));
            header('Location: profil.php');
            }else{
                $erreur = _("Mail déjà utilisé");
            }
         }
    }

    if(isset($_POST['formmdp'])) {
        if(isset($_POST['newmdp1']) AND !empty($_POST['newmdp1']) AND isset($_POST['newmdp2']) AND !empty($_POST['newmdp2'])) {
            $mdp1 = sha1($_POST['newmdp1']);
            $mdp2 = sha1($_POST['newmdp2']);
            if($mdp1 == $mdp2) {
                $mdplength = strlen($_POST['newmdp1']);
				if($mdplength >= 8){
            $insertmdp = $bdd->prepare("UPDATE utilisateur SET MdpUtilisateur = ? WHERE IdUtilisateur = ?");
            $insertmdp->execute(array($mdp1, $_SESSION['IdUtilisateur']));
            $erreur = _("Mot de passe mit à jour");
                }else{
                    $erreur = _("Mot de passe trop court ! (8 caractères minimum)");
                }
            } else {
            $erreur = _("Vos deux mots de passes ne correspondent pas !");
            }
        } else {
            $erreur= _("Champs vides !");
        }
    }

    if(isset($_FILES['avatar']) AND !empty($_FILES['avatar']['name'])) {
        $tailleMax = 2097152;
        $extensionsValides = array('jpg', 'jpeg', 'gif', 'png');
        if($_FILES['avatar']['size'] <= $tailleMax) {
           $extensionUpload = strtolower(substr(strrchr($_FILES['avatar']['name'], '.'), 1));
           if(in_array($extensionUpload, $extensionsValides)) {;
              $chemin = "utilisateur/avatars/".$_SESSION['IdUtilisateur'].".".$extensionUpload;
              $photoresize = resize_crop_image(500, 500, $_FILES['avatar']['tmp_name'] , $_FILES['avatar']['tmp_name']);
              $resultat = move_uploaded_file($_FILES['avatar']['tmp_name'], $chemin);
              if($resultat) {
                 $updateavatar = $bdd->prepare('UPDATE utilisateur SET AvatarUtilisateur = :avatar WHERE IdUtilisateur = :id');
                 $updateavatar->execute(array(
                    'avatar' => $_SESSION['IdUtilisateur'].".".$extensionUpload,
                    'id' => $_SESSION['IdUtilisateur']
                    ));
                 header('Location: profil.php');
              } else {
                 $erreur = _("Erreur durant l'importation de votre photo de profil");
              }
           } else {
              $erreur = _("Votre photo de profil doit être au format jpg, jpeg, gif ou png");
           }
        } else {
           $erreur = _("Votre photo de profil ne doit pas dépasser 2Mo");
        }
    }

    $date=$_UserProfile['DateInscriptionUtilisateur'];

    function DateUSenFR($DateHeureEnUS) {
        $dateHeure = explode(' ' , $DateHeureEnUS);
        $tabDate = explode('-' , $dateHeure[0]);
        $dateenFR = $tabDate[2].'/'.$tabDate[1].'/'.$tabDate[0];
        return $dateenFR;
    }

    $profilInfo["IdUtilisateur"]=$_GET['id'];

    function resize_crop_image($max_width, $max_height, $source_file, $dst_dir, $quality = 80){
        $imgsize = getimagesize($source_file);
        $width = $imgsize[0];
        $height = $imgsize[1];
        $mime = $imgsize['mime'];
     
        switch($mime){
            case 'image/gif':
                $image_create = "imagecreatefromgif";
                $image = "imagegif";
                break;
     
            case 'image/png':
                $image_create = "imagecreatefrompng";
                $image = "imagepng";
                $quality = 7;
                break;
     
            case 'image/jpeg':
                $image_create = "imagecreatefromjpeg";
                $image = "imagejpeg";
                $quality = 80;
                break;
     
            default:
                return false;
                break;
        }
         
        $dst_img = imagecreatetruecolor($max_width, $max_height);
        $src_img = $image_create($source_file);
         
        $width_new = $height * $max_width / $max_height;
        $height_new = $width * $max_height / $max_width;
        if($width_new > $width){
            $h_point = (($height - $height_new) / 2);
            imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
        }else{
            $w_point = (($width - $width_new) / 2);
            imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
        }
         
        $image($dst_img, $dst_dir, $quality);
     
        if($dst_img)imagedestroy($dst_img);
        if($src_img)imagedestroy($src_img);
    }

    if($_GET["id"]!=null){
        $annonces = $bdd->prepare('SELECT * FROM annonce WHERE IdUtilisateur = ? ORDER BY IdAnnonce DESC');
        $annonces->execute(array($_GET["id"]));
    }else{
        $annonces = $bdd->prepare('SELECT * FROM annonce WHERE IdUtilisateur = ? ORDER BY IdAnnonce DESC');
        $annonces->execute(array($_SESSION['IdUtilisateur']));
    }

    if($_GET["id"]!=null){
        $nbannonces = $bdd->prepare('SELECT * FROM annonce WHERE IdUtilisateur = ? ORDER BY IdAnnonce DESC');
        $nbannonces->execute(array($_GET["id"]));
    }else{
        $nbannonces = $bdd->prepare('SELECT * FROM annonce WHERE IdUtilisateur = ? ORDER BY IdAnnonce DESC');
        $nbannonces->execute(array($_SESSION['IdUtilisateur']));
    }

    $nbsignalement = $_UserProfile["SignalementUtilisateur"];
    if(!empty($_POST['signal-prof'])) {
        $nbsignalement=$nbsignalement+1;
        $signal = $bdd->prepare('UPDATE utilisateur SET SignalementUtilisateur = ? WHERE IdUtilisateur = ?');
        $signal->execute([$nbsignalement , $_UserProfile['IdUtilisateur']]);
    }

    if(!(isset($_SESSION['IdUtilisateur']) AND $userinfo['IdUtilisateur'] == $_SESSION['IdUtilisateur'] AND $_SESSION['IdUtilisateur']!=0)) {
        Header('Location: connexion.php');
    }

    if(isset($_POST["newConv"])){
        $reqconv = $bdd->prepare('SELECT IdConversation, IdUtilisateur, IdUtilisateur_MEMBRE_2 FROM conversation WHERE (IdUtilisateur = ? AND IdUtilisateur_MEMBRE_2 = ?) OR (IdUtilisateur = ? AND IdUtilisateur_MEMBRE_2 = ?)');
        $reqconv->execute([$userinfo['IdUtilisateur'] , $_UserProfile['IdUtilisateur'] , $_UserProfile['IdUtilisateur'] , $userinfo['IdUtilisateur']]);
        $conv = $reqconv->rowCount();
        if($conv == 0) {
            $newConv = $bdd->prepare('INSERT INTO conversation (DateDernierMessage, IdUtilisateur, IdUtilisateur_MEMBRE_2) VALUES (NOW(), ?, ?)');
            $newConv->execute([$userinfo['IdUtilisateur'] , $_UserProfile['IdUtilisateur']]);
            Header('Location: messagerie.php');
        }else{
            $conversation = $reqconv->fetch();
            Header('Location: messagerie.php?conversation='.$conversation['IdConversation']);
        }
    }

?>


<!DOCTYPE html>
<html lang="<?= $_SESSION["Language"]; ?>" dir="<?php if($_SESSION["Language"] == "ar") echo "rtl" ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link
        href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Poppins:wght@400;700&family=Raleway&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/icon" href="favicon.ico" />
    <link rel="stylesheet" href="css/utils.css">
    <link rel="stylesheet" href="css/profile.css">
<?php if($_SESSION["Language"] == "ar"){?><link rel="stylesheet" href="css/rtl.css"><?php } ?>
    <title>Profil de <?= $_UserProfile["NomUtilisateur"]?> - Je viens d'ailleurs</title>
    <meta name="robots" content="noindex, nofollow, noodp, noarchive, nosnippet" />
    <meta name="google" content="noimageindex" />
</head>

<body>
    <header class="header">
        <div class="top-bar">
            <a href="index.php" class="top-logo">
                <img src="images/logo.svg" alt="Logo Je viens d'ailleurs" />
            </a>
            <div class="wrapper-menu">
                <div class="line-menu half start"></div>
                <div class="line-menu"></div>
                <div class="line-menu half end"></div>
            </div>
        </div>
        <div class="left-menu small-menu stick-to-left">
            <ul class="menu-links">
                <li><a href="index.php">
                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"
                            stroke-linejoin="round">
                            <path
                                d="M2.7 12.9l-0.7 0.6c-0.4 0.4-0.8 0.1-0.9 0l-0.9-1c-0.3-0.4-0.1-0.8 0.1-1l10.7-9.5c0.3-0.2 0.8-0.4 1.1-0.4 0.4 0 0.9 0.2 1.1 0.4l10.7 9.5c0.1 0.1 0.4 0.6 0.1 1 0 0-0.9 1-0.9 1 -0.1 0.1-0.5 0.4-0.9 0.1l-0.7-0.6 0 8.2c0 0.7-0.6 1.3-1.3 1.3l-16 0c-0.7 0-1.3-0.6-1.3-1.3l0-8.2Zm11.1-2.5l-3.6 0c-0.5 0-0.9 0.4-0.9 0.9l0 3.6c0 0.5 0.4 0.9 0.9 0.9l3.6 0c0.5 0 0.9-0.4 0.9-0.9l0-3.6c0-0.5-0.4-0.9-0.9-0.9Z"
                                fill="#a2a2a2" />
                        </svg>
                        <span><?php echo _("Accueil")?></span>
                    </a>
                </li>
                <li>
                    <a href="articles.php">
                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"
                            style="clip-rule:evenodd;fill-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2">
                            <path
                                d="M23 3.9l-18.3 0c-0.9 0-1.6 0.6-1.9 1.4l-1.8 0c-0.6 0-1 0.5-1 1l0 11.5c0 1.3 1 2.4 2.3 2.4l20.7 0c0.6 0 1-0.5 1-1l0-14.2c0-0.6-0.4-1-1-1Zm-18.4 14.2l17.4 0 0-12.2 -17.3 0 0 11.8c0 0.1 0 0.2 0 0.3Zm-2.6-0.3l0-10.5 0.7 0 0 10.5c0 0.2-0.1 0.3-0.3 0.3 -0.2 0-0.3-0.2-0.3-0.3Zm5.2-4.7l5.7 0c0.3 0 0.5-0.2 0.5-0.5l0-4.1c0-0.3-0.2-0.5-0.5-0.5l-5.7 0c-0.3 0-0.5 0.2-0.5 0.5l0 4.1c0 0.3 0.2 0.5 0.5 0.5Zm8-3l4.3 0c0.3 0 0.5-0.2 0.5-0.5l0-1c0-0.3-0.2-0.5-0.5-0.5l-4.3 0c-0.3 0-0.5 0.2-0.5 0.5l0 1c0 0.3 0.2 0.5 0.5 0.5Zm-3.5-0.3l-3.3 0 0 1.7 3.3 0 0-1.7Zm7.8 3.4l-4.3 0c-0.3 0-0.5-0.2-0.5-0.5l0-1c0-0.3 0.2-0.5 0.5-0.5l4.3 0c0.3 0 0.5 0.2 0.5 0.5l0 1c0 0.3-0.2 0.5-0.5 0.5Zm-12.3 3l5.7 0c0.3 0 0.5-0.2 0.5-0.5l0-1c0-0.3-0.2-0.5-0.5-0.5l-5.7 0c-0.3 0-0.5 0.2-0.5 0.5l0 1c0 0.3 0.2 0.5 0.5 0.5Zm12.3 0l-4.3 0c-0.3 0-0.5-0.2-0.5-0.5l0-1c0-0.3 0.2-0.5 0.5-0.5l4.3 0c0.3 0 0.5 0.2 0.5 0.5l0 1c0 0.3-0.2 0.5-0.5 0.5Z"
                                fill="#a2a2a2" /></svg>
                        <span><?php echo _("Articles")?></span>
                    </a>
                </li>
                <li>
                    <a href="annonces.php">
                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"
                            style="clip-rule:evenodd;fill-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2">
                            <path
                                d="M19.5 7.2l-1.8-1.8c-0.4-0.4-0.9-0.6-1.5-0.6l-8.5 0c-0.6 0-1.1 0.2-1.5 0.6l-1.8 1.8 -4.4 0 0 9.6 2.4 0c0.7 0 1.2-0.5 1.2-1.2l0.3 0 3.2 2.9c1.2 0.9 2.8 1 4 0.1 0.5 0.4 1 0.6 1.5 0.6 0.7 0 1.3-0.3 1.8-0.9 0.8 0.3 1.8 0.1 2.4-0.6l1-1.2c0.2-0.3 0.3-0.6 0.4-0.9l2.2 0c0 0.7 0.5 1.2 1.2 1.2l2.4 0 0-9.6 -4.5 0Zm-4 9.4l1-1.2c0.1-0.1 0.1-0.3 0-0.4l-4.1-3.3 -0.3 0.3c-1.5 1.4-3.3 0.4-3.8-0.2 -1-1.1-0.9-2.8 0.2-3.8l1.5-1.3 -2.1 0c-0.1 0-0.1 0.1-0.2 0.1l-2.3 2.3 -1.6 0 0 4.8 1 0 3.6 3.3c0.6 0.5 1.6 0.4 2.1-0.2l0.6-0.7 1.4 1.2c0.1 0.1 0.5 0.2 0.7-0.1l1.1-1.4 0.9 0.7c0.1 0.1 0.3 0.1 0.4 0Zm5-2.8l-2.6 0c-0.1-0.1-0.2-0.2-0.3-0.3l-3.9-3.1 0.5-0.4c0.2-0.2 0.3-0.6 0-0.9l-0.4-0.4c-0.2-0.2-0.6-0.3-0.8 0l-2.1 1.9c-0.4 0.3-1 0.4-1.3 0 -0.3-0.4-0.3-0.9 0-1.3l2.5-2.3c0.3-0.3 0.6-0.4 1-0.4l3.1 0c0.1 0 0.2 0 0.2 0.1l2.3 2.3 1.7 0 0 4.8Zm-18.6 1.8c-0.3 0-0.6-0.3-0.6-0.6 0-0.3 0.3-0.6 0.6-0.6 0.3 0 0.6 0.3 0.6 0.6 0 0.3-0.3 0.6-0.6 0.6Zm19.8-0.6c0 0.3 0.3 0.6 0.6 0.6 0.3 0 0.6-0.3 0.6-0.6 0-0.3-0.3-0.6-0.6-0.6 -0.3 0-0.6 0.3-0.6 0.6l0 0Z"
                                fill="#a2a2a2" /></svg>
                        <span><?php echo _("Annonces")?></span>
                    </a>
                </li>
                <li>
                    <a href="messagerie.php">
                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"
                            stroke-linejoin="round">
                            <path
                                d="M12 1.5c-6.6 0-12 4.4-12 9.8 0 2.3 1 4.5 2.7 6.1 -0.6 2.4-2.5 4.5-2.6 4.5 -0.1 0.1-0.1 0.3-0.1 0.4 0.1 0.1 0.2 0.2 0.3 0.2 3.1 0 5.4-1.5 6.6-2.4 1.5 0.6 3.2 0.9 5 0.9 6.6 0 12-4.4 12-9.7 0-5.4-5.4-9.7-12-9.7Z"
                                fill="#a2a2a2" /></svg>
                        <span><?php echo _("Messages")?></span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="top-buttons">
            <form class="search-bar" action="recherche.php" method="POST">
                <input type="search" placeholder="<?php echo _("Rechercher")?>" name="query1" />
                <label>
                    <svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                        <path fill="#f29559"
                            d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z">
                        </path>
                    </svg>
                    <input class="search-submit" type="submit">
                </label>
            </form>
            <?php 
                $reqmoderateur = $bdd->prepare('SELECT utilisateur.IdUtilisateur FROM utilisateur INNER JOIN moderateur ON utilisateur.IdUtilisateur = moderateur.IdUtilisateur WHERE utilisateur.IdUtilisateur = ? ');
                $reqmoderateur->execute(array($_SESSION['IdUtilisateur']));
                $ismoderateur = $reqmoderateur->fetch();
                if($ismoderateur != null){ ?>
                    <a href="poster-article.php" class="button button-red button-big top-button"><?php echo _("Poster un article")?></a>
                <?php } ?>
                
            <a href="poster-annonce.php" class="button button-accent button-big top-button"><?php echo _("Poster une annonce")?></a>
            <?php     
            if(isset($_SESSION['IdUtilisateur']) AND $userinfo['IdUtilisateur'] == $_SESSION['IdUtilisateur']) {

            ?>
            <a href="profil.php" class="top-button profile-img img-top-button">
                <img  src="utilisateur/avatars/<?php echo $userinfo['AvatarUtilisateur']; ?>" alt="Photo de profil"/>
            </a>
            <div class="avatar-hover-menu">
                <ul class="avatar-hover">
                    <li><a href="deconnexion.php" class="raleway"><?php echo _("Déconnexion")?></a></li>
                </ul>
            </div>
            <?php
            } else { ?>
                <a href="connexion.php" class="button button-accent button-big top-button"><?php echo _("Connexion") ?></a>
            <?php 
            }
            ?>
        </div>
        <div class="languages">
        <form method="POST" class="languages-form">
            <label>
                <img class="flag" src="images/flags/france.svg" alt="Langue française" />
                <input type="submit" name="french">
            </label>
            <label>
                <img class="flag" src="images/flags/arabic.svg" alt="Langue arabe" />
                <input type="submit" name="arabic">
            </label>
            <label>
                <img class="flag" src="images/flags/uk.svg" alt="Langue anglaise" />
                <input type="submit" name="english">
            </label>
        </form>
    </div>
</header>
    <main>
        <section class="profile-block round-block">
        <?php
        if($userexist == 0 AND $_UserProfile['IdUtilisateur']!= $userinfo['IdUtilisateur'])
        {
        ?>
        <h2><?php echo _("Utilisateur inexistant")?></h2>
        <?php
        }
        if($_UserProfile['IdUtilisateur']== $userinfo['IdUtilisateur'] || $userexist == 1){
        ?>
            <div class="profile">
                <div class="img-container">
                <?php
                                 if($_UserProfile['IdUtilisateur']== $userinfo['IdUtilisateur'])
                                {
                ?>
                    <form class="img" method="POST" enctype="multipart/form-data">
                        <label>
                            <div class="overlay">
                                <svg class="img-pen" aria-hidden="true" focusable="false" role="img"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                    <path class="edit-path" fill="#fff"
                                        d="M290.74 93.24l128.02 128.02-277.99 277.99-114.14 12.6C11.35 513.54-1.56 500.62.14 485.34l12.7-114.22 277.9-277.88zm207.2-19.06l-60.11-60.11c-18.75-18.75-49.16-18.75-67.91 0l-56.55 56.55 128.02 128.02 56.55-56.55c18.75-18.76 18.75-49.16 0-67.91z">
                                    </path>
                                </svg>
                                <input type="file" name="avatar" onchange="this.form.submit()">
                            </div>
                        </label>
                    </form>
                    <?php
                                }
                    ?>
                    <img class="profile-img" src="utilisateur/avatars/<?php echo $_UserProfile['AvatarUtilisateur']; ?>"
                        alt="Photo de profil" />
                </div>
                <div class="profile-info">
                    <div class="input-container">
                        <?php
	         if(isset($erreur)) {
	            echo '<font color="red">'.$erreur."</font>";
	         }
             ?>
               <?php if($ismoderateur!=NULL)
                 {
                ?>
                     <p class="user-pseudo"><?php echo _("Nombre de signalements")?> : <?php echo $nbsignalement ?> </p>
              <?php
                }
             ?>
                        <form method="POST" action="">
                            <input name="newnom" class="user-name merriweather" type="text"
                                value="<?php echo $_UserProfile['NomUtilisateur'];?>" readonly="readonly">
                                <?php
                                 if($_UserProfile['IdUtilisateur']== $userinfo['IdUtilisateur'])
                                {

                                ?>
                            <svg class="edit-pen" aria-hidden="true" focusable="false" role="img"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                <path class="edit-path" fill="#a2a2a2"
                                    d="M290.74 93.24l128.02 128.02-277.99 277.99-114.14 12.6C11.35 513.54-1.56 500.62.14 485.34l12.7-114.22 277.9-277.88zm207.2-19.06l-60.11-60.11c-18.75-18.75-49.16-18.75-67.91 0l-56.55 56.55 128.02 128.02 56.55-56.55c18.75-18.76 18.75-49.16 0-67.91z">
                                </path>
                            </svg>
                            <label>
                                <input type="submit" name="formnom" />
                                <svg class="validate" aria-hidden="true" focusable="false" role="img"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                    <path fill="#a2a2a2"
                                        d="M173.898 439.404l-166.4-166.4c-9.997-9.997-9.997-26.206 0-36.204l36.203-36.204c9.997-9.998 26.207-9.998 36.204 0L192 312.69 432.095 72.596c9.997-9.997 26.207-9.997 36.204 0l36.203 36.204c9.997 9.997 9.997 26.206 0 36.204l-294.4 294.401c-9.998 9.997-26.207 9.997-36.204-.001z">
                                    </path>
                                </svg>
                            </label>
                                <?php
                                }
                                ?>
                        </form>
                    </div>
                    <div class="input-container">
                        <form method="POST" action="">
                            <input name="newpseudo" class="user-pseudo raleway pseudo-input" type="text"
                                value="<?php echo $_UserProfile['PseudoUtilisateur'];?>" readonly="readonly">
                            <span class="pseudo-span"></span>
                            <?php
                                 if($_UserProfile['IdUtilisateur']== $userinfo['IdUtilisateur'])
                                {

                            ?>
                            <svg class="edit-pen" aria-hidden="true" focusable="false" role="img"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                <path class="edit-path" fill="#a2a2a2"
                                    d="M290.74 93.24l128.02 128.02-277.99 277.99-114.14 12.6C11.35 513.54-1.56 500.62.14 485.34l12.7-114.22 277.9-277.88zm207.2-19.06l-60.11-60.11c-18.75-18.75-49.16-18.75-67.91 0l-56.55 56.55 128.02 128.02 56.55-56.55c18.75-18.76 18.75-49.16 0-67.91z">
                                </path>
                            </svg>
                            <label>
                                <input type="submit" name="formpseudo" />
                                <svg class="validate" aria-hidden="true" focusable="false" role="img"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                    <path fill="#a2a2a2"
                                        d="M173.898 439.404l-166.4-166.4c-9.997-9.997-9.997-26.206 0-36.204l36.203-36.204c9.997-9.998 26.207-9.998 36.204 0L192 312.69 432.095 72.596c9.997-9.997 26.207-9.997 36.204 0l36.203 36.204c9.997 9.997 9.997 26.206 0 36.204l-294.4 294.401c-9.998 9.997-26.207 9.997-36.204-.001z">
                                    </path>
                                </svg>
                            </label>
                            <?php
                                }
                            ?>
                        </form>
                    </div>
                    <div class="input-container">
                        <form method="POST" action="">
                            <input name="newmail" class="user-pseudo raleway" type="email"
                                value="<?php echo $_UserProfile['MailUtilisateur'];?>" readonly="readonly">
                                <?php
                                 if($_UserProfile['IdUtilisateur']== $userinfo['IdUtilisateur'])
                                {

                                ?>
                            <svg class="edit-pen" aria-hidden="true" focusable="false" role="img"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                <path class="edit-path" fill="#a2a2a2"
                                    d="M290.74 93.24l128.02 128.02-277.99 277.99-114.14 12.6C11.35 513.54-1.56 500.62.14 485.34l12.7-114.22 277.9-277.88zm207.2-19.06l-60.11-60.11c-18.75-18.75-49.16-18.75-67.91 0l-56.55 56.55 128.02 128.02 56.55-56.55c18.75-18.76 18.75-49.16 0-67.91z">
                                </path>
                            </svg>
                            <label>
                                <input type="submit" name="formmail" />
                                <svg class="validate" aria-hidden="true" focusable="false" role="img"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                    <path fill="#a2a2a2"
                                        d="M173.898 439.404l-166.4-166.4c-9.997-9.997-9.997-26.206 0-36.204l36.203-36.204c9.997-9.998 26.207-9.998 36.204 0L192 312.69 432.095 72.596c9.997-9.997 26.207-9.997 36.204 0l36.203 36.204c9.997 9.997 9.997 26.206 0 36.204l-294.4 294.401c-9.998 9.997-26.207 9.997-36.204-.001z">
                                    </path>
                                </svg>
                            </label>
                            <?php
                                }
                            ?>
                        </form>
                    </div>
                    <?php
                        if($_UserProfile['IdUtilisateur']== $userinfo['IdUtilisateur'])
                        {

                    ?>
                    <div class="input-container">
                        <form class="passwords" method="POST" action="">
                            <input name="newmdp1" class="user-pseudo raleway" type="password"
                                placeholder="<?php echo _("Nouveau mot de passe")?>" readonly="readonly">
                            <input name="newmdp2" class="user-pseudo raleway mdp2" type="password"
                                placeholder="<?php echo _("Confirmez nouveau mot de passe")?>">
                            <svg class="edit-pen" aria-hidden="true" focusable="false" role="img"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                <path class="edit-path" fill="#a2a2a2"
                                    d="M290.74 93.24l128.02 128.02-277.99 277.99-114.14 12.6C11.35 513.54-1.56 500.62.14 485.34l12.7-114.22 277.9-277.88zm207.2-19.06l-60.11-60.11c-18.75-18.75-49.16-18.75-67.91 0l-56.55 56.55 128.02 128.02 56.55-56.55c18.75-18.76 18.75-49.16 0-67.91z">
                                </path>
                            </svg>
                            <label>
                                <input type="submit" name="formmdp" />
                                <svg class="validate" aria-hidden="true" focusable="false" role="img"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                    <path fill="#a2a2a2"
                                        d="M173.898 439.404l-166.4-166.4c-9.997-9.997-9.997-26.206 0-36.204l36.203-36.204c9.997-9.998 26.207-9.998 36.204 0L192 312.69 432.095 72.596c9.997-9.997 26.207-9.997 36.204 0l36.203 36.204c9.997 9.997 9.997 26.206 0 36.204l-294.4 294.401c-9.998 9.997-26.207 9.997-36.204-.001z">
                                    </path>
                                </svg>
                            </label>
                        </form>
                    </div>
                    <?php
                        }
                    ?>

                </div>
            </div>
            <div class="input-container">
                <form class="description-from" method="POST" action="">
                    <textarea name="newdescription" class="profile-desc raleway"
                        readonly="readonly"><?php echo  $_UserProfile['DescriptionUtilisateur'];?></textarea>
                        <?php
                            if($_UserProfile['IdUtilisateur']== $userinfo['IdUtilisateur']) {
                        ?>
                        <svg class="edit-pen" aria-hidden="true" focusable="false" role="img"
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                        <path class="edit-path" fill="#a2a2a2"
                            d="M290.74 93.24l128.02 128.02-277.99 277.99-114.14 12.6C11.35 513.54-1.56 500.62.14 485.34l12.7-114.22 277.9-277.88zm207.2-19.06l-60.11-60.11c-18.75-18.75-49.16-18.75-67.91 0l-56.55 56.55 128.02 128.02 56.55-56.55c18.75-18.76 18.75-49.16 0-67.91z">
                        </path>
                    </svg>
                    <label>
                        <input type="submit" name="formdescription" />
                        <svg class="validate" aria-hidden="true" focusable="false" role="img"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <path fill="#a2a2a2"
                                d="M173.898 439.404l-166.4-166.4c-9.997-9.997-9.997-26.206 0-36.204l36.203-36.204c9.997-9.998 26.207-9.998 36.204 0L192 312.69 432.095 72.596c9.997-9.997 26.207-9.997 36.204 0l36.203 36.204c9.997 9.997 9.997 26.206 0 36.204l-294.4 294.401c-9.998 9.997-26.207 9.997-36.204-.001z">
                            </path>
                        </svg>
                    </label>
                    <?php } ?>
                </form>
            </div>
            <?php
            if($_UserProfile['IdUtilisateur']!= $userinfo['IdUtilisateur']) { ?>
                <form method="POST">
                    <input type="submit" name="newConv" value="<?php echo _("Contacter")?>" class="button button-accent button-big contact-button" >
                </form>
            <?php } ?>
            <p class="signup-date raleway"><?php echo DateUSenFR($date)?></p>
            <?php if(isset($_SESSION['IdUtilisateur']) AND $userinfo['IdUtilisateur'] == $_SESSION['IdUtilisateur'] AND $_SESSION['IdUtilisateur']!=0) { ?>
                <div class="three-dots">
                    <ul class="three-dots-menu">
                        <?php if($_UserProfile['IdUtilisateur']!= $userinfo['IdUtilisateur']) { ?>
                            <li><a class="signal raleway"><?php echo _("Signaler l'utilisateur")?></a></li>
                        <?php } ?>
                        <?php if($_UserProfile['IdUtilisateur']== $userinfo['IdUtilisateur'] || $ismoderateur!=NULL) { ?>
                            <li><a class="delete raleway" href="#"><?php echo _("Supprimer le compte")?></a></li>
                            <?php if($_UserProfile['IdUtilisateur']== $userinfo['IdUtilisateur']) { ?>
                                <li><a href="deconnexion.php" class="raleway"><?php echo _("Déconnexion")?></a></li>
                            <?php } ?>
                        <?php } ?>
                    </ul>
                </div>
            <?php } ?>
            
        </section>
        <?php $nombreannonces = $nbannonces->fetch();
        if($nombreannonces!=null) { ?>
        <section class="offers-posted round-block">
            <h2 class="merriweather"><?php echo _("Annonces publiées")?></h2>
            <div class="posts-list">
                <?php while($an = $annonces->fetch()) { ?>
                    <div class="post-box post-<?= $an["IdAnnonce"] ?>">
                        <a href="annonce.php?id=<?= $an["IdAnnonce"] ?>">
                            <h2 class="post-title"><?= $an["TitreAnnonce"] ?></h2>
                            <p class="post-date"><?= DateUSenFR($an["DateAnnonce"]) ?></p>
                            <?php if($an["LieuAnnonce"]!=null) { ?>
                            <div class="post-place">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="27.999" viewBox="0 0 16 27.999">
                                    <path fill="white" fill-rule="evenodd"
                                        d="M8 0C3.58166504 0 0 3.5256958 0 7.875c0 4.3493042 3.58166504 7.875 8 7.875s8-3.5256958 8-7.875C16 3.5256958 12.41833496 0 8 0zM4.222229 7.875c0-2.05078125 1.69445801-3.71875 3.777771-3.71875.36779785 0 .66668701-.29418945.66668701-.65625S8.36779785 2.84375 8 2.84375c-2.81774902 0-5.11108398 2.256958-5.11108398 5.03125 0 .36206055.29888916.65625.666687.65625.36773682 0 .66662598-.29418945.66662598-.65625zm2 9.4576416v8.56896973l1.22332764 1.80578613c.26391602.3894043.84558106.3894043 1.10943604 0L9.777771 25.90161133V17.3326416C9.20056152 17.43762207 8.60778809 17.5 8 17.5c-.60778809 0-1.20056152-.06237793-1.777771-.1673584z" />
                                </svg>
                                <p><?= $an["LieuAnnonce"] ?></p>
                            </div>
                            <?php } ?>
                        </a>
                        <div class="info-wrapper">
                        <div class="categories">
                                <?php
                                $reqcategorie = $bdd->prepare('SELECT annonce.IdAnnonce, categorie.NomCategorie, categorie.CouleurHEXCategorie FROM categorie INNER JOIN appartient_a ON categorie.NomCategorie = appartient_a.NomCategorie INNER JOIN annonce ON appartient_a.IdAnnonce = annonce.IdAnnonce WHERE annonce.IdAnnonce = ?'); 
                                $reqcategorie->execute(array($an["IdAnnonce"]));
                                $i = 0;
                                while($catan = $reqcategorie->fetch()) { ?>
                                <style>
                                    a.<?= $catan["NomCategorie"] ?> {
                                        color: <?= $catan["CouleurHEXCategorie"] ?>;
                                    }
                                    <?php if($i==0) {
                                        ?>.post-<?= $catan["IdAnnonce"] ?> {
                                            background-color: <?= $catan["CouleurHEXCategorie"] ?>;
                                        }
                                        <?php $i++;
                                    }
                                    ?>
                                </style>
                                <a href="annonces.php?categorie=<?= $catan["NomCategorie"] ?>" class="category <?= $catan["NomCategorie"] ?>"><?= $catan["NomCategorie"] ?></a>
                                <?php } ?>
                            </div>
                            <?php
                            $reqprofil = $bdd->prepare('SELECT annonce.IdAnnonce, utilisateur.IdUtilisateur, utilisateur.NomUtilisateur, utilisateur.PseudoUtilisateur, utilisateur.AvatarUtilisateur FROM utilisateur INNER JOIN annonce ON utilisateur.IdUtilisateur = annonce.IdUtilisateur WHERE annonce.IdAnnonce = ?'); 
                            $reqprofil->execute(array($an["IdAnnonce"]));
                            $profilInfo = $reqprofil->fetch();
                            ?>
                            <a href="profil.php?id=<?= $profilInfo["IdUtilisateur"] ?>" class="profile">
                                <img class="profile-img" src="utilisateur/avatars/<?= $profilInfo["AvatarUtilisateur"] ?>" alt="Photo de profil" />
                                <div class="profile-info">
                                    <h3 class="user-name poppins"><?= $profilInfo["NomUtilisateur"] ?></h3>
                                    <p class="user-pseudo poppins">@<?= $profilInfo["PseudoUtilisateur"] ?></p>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </section>
        <?php } ?>
        <?php } ?>
        <footer class="bottom-bar">
            <p class="text-footer">© 2020 Je Viens d'Ailleurs. | <?php echo _("Tous droits réservés.") ?></p>
            <ul class="list-footer">
                <li class="footer-links raleway"> <a href="mentions-legales.php"><?php echo _("Mentions légales") ?></a></li>
                <li class="footer-links raleway">|</li>
                <li class="footer-links raleway"> <a href="politique-de-confidentialite.php"><?php echo _("Politique de confidentialité") ?></a></li>
                <li class="footer-links raleway">|</li>
                <li class="footer-links raleway"> <a href="qui-sommes-nous.php"><?php echo _("Qui sommes-nous ?") ?></a></li>
            </ul>
        </footer>
    </main>
    <div class="popup-container">
        <div class="popup-content">
            <h2 class="merriweather"><?php echo _("Supprimer le compte ?")?></h2>
            <div class="confirm-buttons">
                <a href="supprimer-compte.php?id=<?= $profilInfo["IdUtilisateur"] ?>"class="button button-red button-big confirm-yes"><?php echo _("Oui")?></a>
                <a class="button button-accent button-big confirm-no"><?php echo _("Non")?></a>
            </div>
        </div>
    </div>
    <div class="popup-container2">
        <div class="popup-content">
            <h2 class="merriweather"><?php echo _("Signaler l'utilisateur")?></h2>
            <div class="confirm-buttons">
                <form method="POST" action="">
                    <label>
                        <a class="button button-red button-big confirm-yes2"><?php echo _("Oui")?></a>
                        <input type="submit" class="hide-input" name="signal-prof">
                    <label>
                </form>
                <a class="button button-accent button-big confirm-no2"><?php echo _("Non")?></a>
            </div>
        </div>
    </div>
    <script src="js/menu.js"></script>
    <script src="js/menu-hover.js"></script>
    <script src="js/profile.js"></script>
    <script src="js/confirmation.js"></script>

</body>

</html>