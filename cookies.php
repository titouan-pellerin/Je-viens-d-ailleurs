<?php

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
        $locale = "ar_SY.UTF-8";
}else{
    $locale = "";
}


if (defined('LC_MESSAGES')) {
    setlocale(LC_MESSAGES, $locale);
    bindtextdomain("messages", "./locale");
}
textdomain("messages");  


if(isset($_SESSION['IdUtilisateur']) AND $_SESSION['IdUtilisateur'] > 0) {
    $getid = intval($_SESSION['IdUtilisateur']);
    $requser = $bdd->prepare('SELECT * FROM utilisateur WHERE IdUtilisateur = ?');
    $requser->execute(array($getid));
    $userinfo = $requser->fetch();

    $reqmoderateur = $bdd->prepare('SELECT utilisateur.IdUtilisateur FROM utilisateur INNER JOIN moderateur ON utilisateur.IdUtilisateur = moderateur.IdUtilisateur WHERE utilisateur.IdUtilisateur = ? ');
    $reqmoderateur->execute(array($_SESSION['IdUtilisateur']));
    $ismoderateur = $reqmoderateur->fetch();
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
    <link rel="stylesheet" href="css/mention.css">
    <title>Je viens d'ailleurs</title>
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
                        <span><?php echo _("Accueil") ?></span>
                    </a>
                </li>
                <li>
                    <a href="articles.php">
                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"
                            style="clip-rule:evenodd;fill-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2">
                            <path 
                                d="M23 3.9l-18.3 0c-0.9 0-1.6 0.6-1.9 1.4l-1.8 0c-0.6 0-1 0.5-1 1l0 11.5c0 1.3 1 2.4 2.3 2.4l20.7 0c0.6 0 1-0.5 1-1l0-14.2c0-0.6-0.4-1-1-1Zm-18.4 14.2l17.4 0 0-12.2 -17.3 0 0 11.8c0 0.1 0 0.2 0 0.3Zm-2.6-0.3l0-10.5 0.7 0 0 10.5c0 0.2-0.1 0.3-0.3 0.3 -0.2 0-0.3-0.2-0.3-0.3Zm5.2-4.7l5.7 0c0.3 0 0.5-0.2 0.5-0.5l0-4.1c0-0.3-0.2-0.5-0.5-0.5l-5.7 0c-0.3 0-0.5 0.2-0.5 0.5l0 4.1c0 0.3 0.2 0.5 0.5 0.5Zm8-3l4.3 0c0.3 0 0.5-0.2 0.5-0.5l0-1c0-0.3-0.2-0.5-0.5-0.5l-4.3 0c-0.3 0-0.5 0.2-0.5 0.5l0 1c0 0.3 0.2 0.5 0.5 0.5Zm-3.5-0.3l-3.3 0 0 1.7 3.3 0 0-1.7Zm7.8 3.4l-4.3 0c-0.3 0-0.5-0.2-0.5-0.5l0-1c0-0.3 0.2-0.5 0.5-0.5l4.3 0c0.3 0 0.5 0.2 0.5 0.5l0 1c0 0.3-0.2 0.5-0.5 0.5Zm-12.3 3l5.7 0c0.3 0 0.5-0.2 0.5-0.5l0-1c0-0.3-0.2-0.5-0.5-0.5l-5.7 0c-0.3 0-0.5 0.2-0.5 0.5l0 1c0 0.3 0.2 0.5 0.5 0.5Zm12.3 0l-4.3 0c-0.3 0-0.5-0.2-0.5-0.5l0-1c0-0.3 0.2-0.5 0.5-0.5l4.3 0c0.3 0 0.5 0.2 0.5 0.5l0 1c0 0.3-0.2 0.5-0.5 0.5Z"
                                fill="#a2a2a2" /></svg>
                        <span><?php echo _("Articles") ?></span>
                    </a>
                </li>
                <li>
                    <a href="annonces.php">
                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"
                            style="clip-rule:evenodd;fill-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2">
                            <path
                                d="M19.5 7.2l-1.8-1.8c-0.4-0.4-0.9-0.6-1.5-0.6l-8.5 0c-0.6 0-1.1 0.2-1.5 0.6l-1.8 1.8 -4.4 0 0 9.6 2.4 0c0.7 0 1.2-0.5 1.2-1.2l0.3 0 3.2 2.9c1.2 0.9 2.8 1 4 0.1 0.5 0.4 1 0.6 1.5 0.6 0.7 0 1.3-0.3 1.8-0.9 0.8 0.3 1.8 0.1 2.4-0.6l1-1.2c0.2-0.3 0.3-0.6 0.4-0.9l2.2 0c0 0.7 0.5 1.2 1.2 1.2l2.4 0 0-9.6 -4.5 0Zm-4 9.4l1-1.2c0.1-0.1 0.1-0.3 0-0.4l-4.1-3.3 -0.3 0.3c-1.5 1.4-3.3 0.4-3.8-0.2 -1-1.1-0.9-2.8 0.2-3.8l1.5-1.3 -2.1 0c-0.1 0-0.1 0.1-0.2 0.1l-2.3 2.3 -1.6 0 0 4.8 1 0 3.6 3.3c0.6 0.5 1.6 0.4 2.1-0.2l0.6-0.7 1.4 1.2c0.1 0.1 0.5 0.2 0.7-0.1l1.1-1.4 0.9 0.7c0.1 0.1 0.3 0.1 0.4 0Zm5-2.8l-2.6 0c-0.1-0.1-0.2-0.2-0.3-0.3l-3.9-3.1 0.5-0.4c0.2-0.2 0.3-0.6 0-0.9l-0.4-0.4c-0.2-0.2-0.6-0.3-0.8 0l-2.1 1.9c-0.4 0.3-1 0.4-1.3 0 -0.3-0.4-0.3-0.9 0-1.3l2.5-2.3c0.3-0.3 0.6-0.4 1-0.4l3.1 0c0.1 0 0.2 0 0.2 0.1l2.3 2.3 1.7 0 0 4.8Zm-18.6 1.8c-0.3 0-0.6-0.3-0.6-0.6 0-0.3 0.3-0.6 0.6-0.6 0.3 0 0.6 0.3 0.6 0.6 0 0.3-0.3 0.6-0.6 0.6Zm19.8-0.6c0 0.3 0.3 0.6 0.6 0.6 0.3 0 0.6-0.3 0.6-0.6 0-0.3-0.3-0.6-0.6-0.6 -0.3 0-0.6 0.3-0.6 0.6l0 0Z"
                                fill="#a2a2a2" /></svg>
                        <span><?php echo _("Annonces") ?></span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"
                            stroke-linejoin="round">
                            <path
                                d="M12 1.5c-6.6 0-12 4.4-12 9.8 0 2.3 1 4.5 2.7 6.1 -0.6 2.4-2.5 4.5-2.6 4.5 -0.1 0.1-0.1 0.3-0.1 0.4 0.1 0.1 0.2 0.2 0.3 0.2 3.1 0 5.4-1.5 6.6-2.4 1.5 0.6 3.2 0.9 5 0.9 6.6 0 12-4.4 12-9.7 0-5.4-5.4-9.7-12-9.7Z"
                                fill="#a2a2a2" /></svg>
                        <span><?php echo _("Messages") ?></span>
                    </a>
                </li>
            </ul>

        </div>
        <div class="top-buttons">
            <div class="search-bar">
                <input type="search" placeholder="<?php echo _("Rechercher") ?>" />
                <svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                    <path fill="#f29559"
                        d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z">
                    </path>
                </svg>
            </div>
            <?php 
                if($ismoderateur != null){ ?>
                    <a href="poster-article.php" class="button button-red button-big top-button"><?php echo _("Poster un article") ?></a>
                <?php } ?>
            <a href="poster-annonce.php" class="button button-accent button-big top-button"><?php echo _("Poster une annonce") ?></a>
            <?php     
            if(isset($_SESSION['IdUtilisateur']) AND $userinfo['IdUtilisateur'] == $_SESSION['IdUtilisateur']) {

            ?>
            <a href="profil.php" class="top-button profile-img">
                <img src="utilisateur/avatars/<?php echo $userinfo['AvatarUtilisateur']; ?>" alt="Photo de profil"/>
            </a>
            <?php
            } else{ ?>
                <a href="connexion.php" class="button button-accent button-big top-button"><?php echo _("Connexion") ?></a>
            <?php 
            }
            ?>
        </div>
        <div class="languages">
            <img class="flag" src="images/flags/france.svg" alt="Langue française" />
            <img class="flag" src="images/flags/arabic.svg" alt="Langue arabe" />
            <img class="flag" src="images/flags/uk.svg" alt="Langue anglaise" />
        </div>
    </header>
    <main>
        
            <div class="text mentions">
                <h1 class="merriweather accent "><?php echo _("Mentions légales") ?></h1>
                <h2 class="merriweather" ><?php echo _("Site édité par") ?></h2> 
                <p>"<strong>Je viens d'ailleurs</strong>", 52 Rue des Docteurs Calmette et Guérin, 53000 Laval"</p> 
                <h2 class="merriweather" ><?php echo _("Directeur de la publication") ?></h2> 
                <p>IUT LAVAL</p>
                <h2 class="merriweather" ><?php echo _("Droit d’accès") ?></h2> 
                <p><?php echo _("En application de la loi Informatique et liberté, vous disposez d’un droit d’accès, de rectification, de modification et de suppression concernant des données qui vous concernent personnellement. Ce droit peut être exercé par voie électronique ou par courrier postal, daté et signé, accompagné d'une copie d’un titre d’identité.") ?></p>
                <h2 class="merriweather" ><?php echo _("Politique de confidentialité") ?></h2> 
                <p><?php echo _("Les informations personnelles collectées ne sont en aucun cas confiées à des tiers.") ?></p>
                <h2 class="merriweather " ><?php echo _("Propriété intellectuelle") ?></h2> 
                <p><?php echo _("Tout le contenu de notre site, incluant, de façon non limitative, les graphismes, images, textes, vidéos, animations, sons, logos et icônes, sont la propriété exclusive de l'éditeur du site, à l’exception des marques, logos ou contenus appartenant à d’autres organisations.") ?></p>
                <h2 class="merriweather" ><?php echo _("Hébergeur") ?></h2> 
                <p><strong>Je viens d'ailleurs</strong> <?php echo _("est hébergé par l'IUT. 52 Rue des Docteurs Calmette et Guérin, 53000 Laval – 53000 Laval – France Téléphone : 07 81 84 18 63 (France)") ?></p>
            </div>
       
    </main>
    <footer class="bottom-bar">
        <p class="text-footer">© 2020 Je Viens d'Ailleurs. | <?php echo _("Tous droits réservés.") ?></p>
        <ul class="list-footer">
            <li class="footer-links raleway"> <a href="mentions-legales.php"><?php echo _("Mentions légales") ?></li>
            <li class="footer-links raleway">|</li>
            <li class="footer-links raleway"> <a href="politique-de-confidentialite.php"><?php echo _("Politique de confidentialité") ?></li>
            <li class="footer-links raleway">|</li>
            <li class="footer-links raleway"> <a href="#"><?php echo _("Politique d'utilisation des cookies") ?></li>
        </ul>
    </footer>
    <script src="js/menu.js"></script>
    <script src="js/menu-hover.js"></script>
</body>