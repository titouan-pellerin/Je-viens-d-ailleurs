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
  

    $categories = $bdd->query('SELECT * FROM categorie ORDER BY NomCategorie');
    if(isset($_SESSION['IdUtilisateur']) AND $_SESSION['IdUtilisateur'] > 0) {
        $getid = intval($_SESSION['IdUtilisateur']);
        $requser = $bdd->prepare('SELECT * FROM utilisateur WHERE IdUtilisateur = ?');
        $requser->execute(array($getid));
        $userinfo = $requser->fetch();

        $reqmoderateur = $bdd->prepare('SELECT utilisateur.IdUtilisateur FROM utilisateur INNER JOIN moderateur ON utilisateur.IdUtilisateur = moderateur.IdUtilisateur WHERE utilisateur.IdUtilisateur = ? ');
        $reqmoderateur->execute(array($_SESSION['IdUtilisateur']));
        $ismoderateur = $reqmoderateur->fetch();
    }
    if(isset($_SESSION['IdUtilisateur']) AND $userinfo['IdUtilisateur'] == $_SESSION['IdUtilisateur'] AND $_SESSION['IdUtilisateur']!=0) {

	if(isset($_POST['formconnexion'])) {
	   $titre = htmlspecialchars($_POST['titre']);
	   $soustitre = htmlspecialchars($_POST['soustitre']);
	   $textannonce = htmlspecialchars($_POST['textannonce']);
	   $ville = htmlspecialchars($_POST['ville']);
       $titresection =htmlspecialchars($_POST['TitreSectionAnnonce']);
       $descriptionsection = htmlspecialchars($_POST['DescriptionSectionAnnonce']);
       $cours = htmlspecialchars($_POST['Cours']);
       $demarches = htmlspecialchars($_POST['Démarches']);
       $logement = htmlspecialchars($_POST['Logement']);
       $loisirs = htmlspecialchars($_POST['Loisirs']);
       $sante = htmlspecialchars($_POST['Santé']);
       $travail = htmlspecialchars($_POST['Travail']);
       $IdUtilisateur = $_SESSION['IdUtilisateur'];
       $dossier = 'utilisateur/';
       $fichier = basename($_FILES['upload']['name']);
       $taille_maxi = 10485760;
       $taille = filesize($_FILES['upload']['tmp_name']);
       $extensions = array('.png', '.gif', '.jpg', '.jpeg', '.pdf', '.doc', '.odt');
       $extension = strrchr($_FILES['upload']['name'], '.');
	   if(!empty($_POST['titre']) AND !empty($_POST['soustitre']) AND !empty($_POST['textannonce'])) {
        $titrelength = strlen($titre);
		if($titrelength <= 64) {
            $soustitrelength = strlen($soustitre);
            if($soustitrelength <= 128) {
                $titresectionlength= strlen($titresection);
                if($titresectionlength <= 64) {
                    $descriptionsectionlength=strlen($descriptionsection);
                    if($descriptionsectionlength <= 128) {
                        $villelength=strlen($ville);
                        if($villelength <= 64) {
	                  $insertmbr = $bdd->prepare('INSERT INTO annonce (TitreAnnonce, SousTitreAnnonce, TexteAnnonce, LieuAnnonce, TitreSectionAnnonce, DescriptionSectionAnnonce, IdUtilisateur, DateAnnonce) VALUES(?, ?, ?, ?, ?, ?, ?, now())');
		              $insertmbr->execute(array($titre, $soustitre, $textannonce, $ville, $titresection, $descriptionsection, $IdUtilisateur));
                      
                      $annonces = $bdd->query('SELECT * FROM annonce ORDER BY DateAnnonce DESC LIMIT 1');
                      $infoannonce = $annonces->fetch();
                      $idannonce = $infoannonce['IdAnnonce'];
                      if(!empty($cours)){
                        $insertmbr = $bdd->prepare('INSERT INTO appartient_a (IdAnnonce, NomCategorie) VALUES(?, ?)');
                        $insertmbr->execute(array($idannonce, "Cours"));
                         }
                        if (!empty($demarches)){
                            $insertmbr = $bdd->prepare('INSERT INTO appartient_a (IdAnnonce, NomCategorie) VALUES(?, ?)');
                            $insertmbr->execute(array($idannonce, "Démarches"));
                         }
                          if (!empty($logement)){
                            $insertmbr = $bdd->prepare('INSERT INTO appartient_a (IdAnnonce, NomCategorie) VALUES(?, ?)');
                            $insertmbr->execute(array($idannonce, "Logement"));
                         }
                         if (!empty($loisirs)){
                            $insertmbr = $bdd->prepare('INSERT INTO appartient_a (IdAnnonce, NomCategorie) VALUES(?, ?)');
                            $insertmbr->execute(array($idannonce, "Loisirs"));
                         }
                         if (!empty($sante)){
                            $insertmbr = $bdd->prepare('INSERT INTO appartient_a (IdAnnonce, NomCategorie) VALUES(?, ?)');
                            $insertmbr->execute(array($idannonce, "Santé"));
                         }
                          if (!empty($travail)){
                            $insertmbr = $bdd->prepare('INSERT INTO appartient_a (IdAnnonce, NomCategorie) VALUES(?, ?)');
                            $insertmbr->execute(array($idannonce, "Travail"));
                        }

                         for ($i=0 ; $i<3 ;  $i++ ){
                            if(isset($_FILES['files']) AND !empty($_FILES['files']['name'][$i])) {
                                $tailleMax = 2097152;
                                $extensionsValides = array('jpg', 'jpeg', 'gif', 'png', 'pdf', 'doc', 'odt', 'docx');
                                if($_FILES['files']['size'][$i] <= $tailleMax) {
                                   $extensionUpload = strtolower(substr(strrchr($_FILES['files']['name'][$i], '.'), 1));
                                   if(in_array($extensionUpload, $extensionsValides)) {;
                                      $chemin = "utilisateur/upload/".$idannonce."_".$_FILES['files']['name'][$i];
                                      $resultat = move_uploaded_file($_FILES['files']['tmp_name'][$i], $chemin);
                                      
                                      if($resultat) {
                                        if($i==0){
                                        $updateavatar = $bdd->prepare('UPDATE annonce SET FichierAnnonce1 = :fichier WHERE IdAnnonce = :id');
                                        $updateavatar->execute(array(
                                        'fichier' => $idannonce."_".$_FILES['files']['name'][$i],
                                         'id' => $idannonce
                                               ));
                                            }
                                        if($i==1){
                                            $updateavatar = $bdd->prepare('UPDATE annonce SET FichierAnnonce2 = :fichier WHERE IdAnnonce = :id');
                                             $updateavatar->execute(array(
                                            'fichier' => $idannonce."_".$_FILES['files']['name'][$i],
                                              'id' => $idannonce
                                               ));
                                        }
                                        if($i==2){
                                            $updateavatar = $bdd->prepare('UPDATE annonce SET FichierAnnonce3 = :fichier WHERE IdAnnonce = :id');
                                            $updateavatar->execute(array(
                                           'fichier' => $idannonce."_".$_FILES['files']['name'][$i],
                                             'id' => $idannonce
                                              ));
                                            }
                                          //header('Location: profil.php');
                                      } else {
                                         $erreur = _("Erreur durant l'importation de votre fichier");
                                      }
                                   } else {
                                      $erreur = $i. _("Votre fichier doit être au format 'jpg', 'jpeg', 'gif', 'png', 'pdf', 'doc', 'odt' ou 'docx'");
                                   }
                                } else {
                                   $erreur = _("Votre fichier ne doit pas dépasser 2Mo");
                                }
                             }
                         }
                         if(isset($_POST['url1'])){
                            $url1=htmlspecialchars($_POST['url1']);
                            $insertnom = $bdd->prepare("UPDATE annonce SET LienAnnonce1 = ? WHERE IdAnnonce = ?");
                            $insertnom->execute(array($url1, $idannonce));
                         }
                         if(isset($_POST['url2'])){
                            $url2=htmlspecialchars($_POST['url2']);
                            $insertnom = $bdd->prepare("UPDATE annonce SET LienAnnonce2 = ? WHERE IdAnnonce = ?");
                            $insertnom->execute(array($url2, $idannonce));

                        }
                        if(isset($_POST['url3'])){
                            $url3=htmlspecialchars($_POST['url3']);
                            $insertnom = $bdd->prepare("UPDATE annonce SET LienAnnonce3 = ? WHERE IdAnnonce = ?");
                            $insertnom->execute(array($url3, $idannonce));
                        }
                         
                        header('Location: annonce.php?id='.$idannonce);
                        //$erreur = "L'annonce a bien été postée !";
                        }else{
                            $erreur =  _("Le nom de la ville ne peut pas dépasser 64 caractères");  
                        }
                    }else{
                        $erreur = _("La description de la section ne doit pas dépasser 128 caractères");

                    }
                }else{
                    $erreur = _("Le titre de la section ne doit pas dépasser 64 caractères");
                }	
            }else {
                $erreur = _("Le sous-titre ne doit pas dépasser 128 caractères");
            }
        }else{
            $erreur = _("Le titre ne doit pas dépasser 64 caractères");
        }
	   } else {
	      $erreur = _("Tous les champs doivent être complétés !");
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
    <link rel="stylesheet" href="css/forms.css">
<?php if($_SESSION["Language"] == "ar"){?><link rel="stylesheet" href="css/rtl.css"><?php } ?>
    <title>Poster une annonce - Je viens d'ailleurs</title>
    <meta name="title" content="Poster une annonce - Je viens d'ailleurs">
    <meta name="description" content="Vous pouvez donner des cours ou vous recherchez de l'aide ? Postez ici une annonce sur le site Je viens d'ailleurs.">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="jeviensdailleurs.fr/poster-annonce.php">
    <meta property="og:title" content="Poster une annonce - Je viens d'ailleurs">
    <meta property="og:description" content="Vous pouvez donner des cours ou vous recherchez de l'aide ? Postez ici une annonce sur le site Je viens d'ailleurs.">
    <meta property="og:image" content="/images/logo-metadata.svg">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="jeviensdailleurs.fr/poster-annonce.php">
    <meta property="twitter:title" content="Poster une annonce - Je viens d'ailleurs">
    <meta property="twitter:description" content="Vous pouvez donner des cours ou vous recherchez de l'aide ? Postez ici une annonce sur le site Je viens d'ailleurs.">
    <meta property="twitter:image" content="/images/logo-metadata.svg">

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
                if($ismoderateur != null){ ?>
                    <a href="poster-article.php" class="button button-red button-big top-button"><?php echo _("Poster un article") ?></a>
                <?php } ?>
            <a href="profil.php" class="top-button profile-img img-top-button">
                <img src="utilisateur/avatars/<?php echo $userinfo['AvatarUtilisateur']; ?>" alt="Photo de profil" />
            </a>
            <div class="avatar-hover-menu">
                <ul class="avatar-hover">
                    <li><a href="deconnexion.php" class="raleway"><?php echo _("Déconnexion")?></a></li>
                </ul>
            </div>
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
    <form method="POST" action="" enctype="multipart/form-data">
        <main>
            <h1 class="merriweather accent"><?php echo _("Poster une nouvelle annonce")?></h1>
            <div class="publie-list">
                <div class="haut">
                    <div class="inputs-box left-box">
                        <input class="form-inputs title-inputs bigTitle" required maxlength="40" name="titre" type="text"
                            placeholder="<?php echo _("Saisissez un titre pour votre annonce")?>*"
                            value="<?php if(isset($titre)) { echo $titre; } ?>">
                        <input class="form-inputs title-inputs bigSubtitle" required name="soustitre" maxlength="100" type="text"
                            placeholder="<?php echo _("Saisissez un sous-titre")?>*"
                            value="<?php if(isset($soustitre)) { echo $soustitre; } ?>">
                        <textarea class="form-inputs postText" name="textannonce" required
                            placeholder="<?php echo _("Écrivez ici le texte de votre annonce")?>*" rows="8"
                            value="<?php if(isset($textannonce)) { echo $textannonce; } ?>"></textarea>
                    </div>
                    <div class="right-box">
                        <div class="inputs-box raleway categories">
                            <input type="submit" name="formconnexion" value="<?php echo _("Publier")?>"
                                class="publish button-accent button button-big top-publish responsiv" />
                            <h1 class="merriweather box-title"><?php echo _("Catégories")?>*</h1>
                            <ul class="categories-list">
                                <?php while($cat = $categories->fetch()) { ?>
                                <style>
                                    label.<?=$cat["NomCategorie"] ?> {
                                        color: <?=$cat["CouleurHEXCategorie"] ?>;
                                    }

                                    input[type="checkbox"]:checked.<?=$cat["NomCategorie"] ?>+li {
                                        color: white;
                                        background-color: <?=$cat["CouleurHEXCategorie"] ?>;
                                    }
                                </style>
                                <label class="<?=$cat["NomCategorie"] ?>">
                                    <input class="<?=$cat["NomCategorie"] ?> inputcat catchecked"
                                        name="<?=$cat["NomCategorie"]?>" type="checkbox"
                                        value="<?=$cat["NomCategorie"] ?>" />
                                    <li class="category"> <?php echo $cat["NomCategorie"] ?> </li>
                                </label>
                                <?php } ?>
                            </ul>
                            <span class="error-cat error raleway"></span>
                        </div>
                        <div class="localisation inputs-box">
                            <h1 class="merriweather box-title"><?php echo _("Localisation")?></h1>
                            <p class="raleway"><?php echo _("Ajouter un lieu à votre annonce")?></p>
                            <input class="form-inputs city-input" maxlenght="20" name="ville" type="text"
                                placeholder="<?php echo _("Ajouter une ville")?>" value="<?php if(isset($ville)) { echo $ville; } ?>">
                        </div>
                    </div>
                </div>
                <div class="inputs-box bottom-box">
                    <div class="left-bottom-container">
                        <h1 class="merriweather box-title"><?php echo _("Liens ou fichiers externes")?></h1>
                        <div class="left-bottom-box">
                            <input class="form-inputs title-inputs" maxlength="32" type="text"
                                placeholder="<?php echo _("Titre de la section")?>" name="TitreSectionAnnonce">
                            <input class="form-inputs" type="text" maxlenght="100"
                                placeholder="<?php echo _("Description de la section")?>" name="DescriptionSectionAnnonce">
                        </div>
                    </div>
                    <div class="right-bottom-box">
                        <div class="files-container" onload="updateSize();">
                            <label class="files-input">
                                <svg width="100%" height="80px" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg"
                                    xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve"
                                    style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;">
                                    <path fill="rgb(162,162,162)"
                                        d="M43.246,466.142C-15.184,405.853 -14.095,308.631 44.632,248.561L254.392,34C298.708,-11.332 370.743,-11.336 415.063,34C458.953,78.894 459.006,151.329 415.063,196.276L232.214,383.128C202.359,413.665 153.581,413.239 124.232,382.13C95.957,352.16 96.864,304.657 125.684,275.177L269.427,128.342C275.609,122.028 285.739,121.92 292.053,128.101L314.914,150.48C321.229,156.662 321.336,166.792 315.155,173.106L171.427,319.927C166.495,324.972 166.191,333.355 170.779,338.219C175.151,342.853 182.024,342.93 186.467,338.384L369.316,151.533C388.929,131.471 388.929,98.808 369.305,78.735C350.116,59.108 319.348,59.098 300.151,78.735L90.39,293.295C55.627,328.855 55.091,386.415 89.199,421.608C123.209,456.701 178.184,456.745 212.257,421.894L384.317,245.895C390.494,239.576 400.624,239.462 406.943,245.639L429.82,268.003C436.139,274.18 436.254,284.31 430.076,290.629L258.016,466.627C198.44,527.565 102.073,526.843 43.246,466.142Z"
                                        style="fill-rule:nonzero;stroke:rgb(162,162,162);stroke-width:1px;" />
                                </svg>
                                <p class="files-info">
                                    <input id="uploadInput" type="file" name="files[]" onchange="updateSize(this.files)"
                                        multiple><?php echo _("Ajouter des fichiers")?>
                                    </br>
                                    <?php echo _("Fichiers sélectionnés")?> : <span id="fileNum">0</span>/3; <?php echo _("Taille totale")?> :
                                    <span id="fileSize">0</span>
                                </p>
                            </label>
                        </div>
                        <div class="url-container raleway">
                            <label for="url1"><?php echo _("Lien externe n°1")?></label>
                            <input class="form-inputs raleway" type="url" name="url1" id="url1"
                                placeholder="https://exemple.fr">
                            <label for="url2"><?php echo _("Lien externe n°2")?></label>
                            <input class="form-inputs raleway" type="url" name="url2" id="url2"
                                placeholder="https://exemple.fr">
                            <label for="url3"><?php echo _("Lien externe n°3")?></label>
                            <input class="form-inputs raleway" type="url" name="url3" id="url3"
                                placeholder="https://exemple.fr">
                        </div>
                    </div>
                </div>
                <input type="submit" name="formconnexion" value="<?php echo _("Publier")?>"
                    class="publish button-accent button button-big bottom-publish responsiv" />
                
            </div>
        </main>
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
    </form>
    <script src="js/menu.js"></script>
    <script src="js/menu-hover.js"></script>
    <script src="js/new-post.js"></script>
    <script>
        function updateSize(files) {
            if (files.length > 3) {
                alert("3 fichiers maximum");
                return false;
            } else {
                var nBytes = 0,
                    oFiles = document.getElementById("uploadInput").files,
                    nFiles = oFiles.length;
                for (var nFileId = 0; nFileId < nFiles; nFileId++) {
                    nBytes += oFiles[nFileId].size;
                }
                var sOutput = nBytes + " bytes";
                // partie de code facultative pour l'approximation des multiples
                for (var aMultiples = ["KiB", "MiB", "GiB", "TiB", "PiB", "EiB", "ZiB", "YiB"], nMultiple = 0, nApprox =
                        nBytes / 1024; nApprox > 1; nApprox /= 1024, nMultiple++) {
                    sOutput = nApprox.toFixed(3) + " " + aMultiples[nMultiple];
                }
                // fin de la partie de code facultative
                document.getElementById("fileNum").innerHTML = nFiles;
                document.getElementById("fileSize").innerHTML = sOutput;
            }
        }
    </script>

</body>

</html>
<?php
    }else{
        header("Location: connexion.php");
    }
?>