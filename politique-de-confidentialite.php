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
        setlocale(LC_MESSAGES, $locale); /*or die("Langue non installée")*/
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
<?php if($_SESSION["Language"] == "ar"){?><link rel="stylesheet" href="css/rtl.css"><?php } ?>
    <title>Politique de confidentialité - Je viens d'ailleurs</title>
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
                    <a href="messagerie.php">
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
            <a href="poster-annonce.php" class="button button-accent button-big top-button"><?php echo _("Poster une annonce") ?></a>
            <?php     
            if(isset($_SESSION['IdUtilisateur']) AND $userinfo['IdUtilisateur'] == $_SESSION['IdUtilisateur']) {

            ?>
            <a href="profil.php" class="top-button profile-img img-top-button">
                <img src="utilisateur/avatars/<?php echo $userinfo['AvatarUtilisateur']; ?>" alt="Photo de profil"/>
            </a>
            <div class="avatar-hover-menu">
                <ul class="avatar-hover">
                    <li><a href="deconnexion.php" class="raleway"><?php echo _("Déconnexion")?></a></li>
                </ul>
            </div>
            <?php
            } else{ ?>
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
        <section class="mentions">
            <div class="text">
                <h1 class="merriweather accent "><?php echo _("Politique de confidentialité") ?></h1>
                <h2 class="merriweather" ><?php echo _("Utilisation des données personnelles collectées") ?></h2>
                <h3 class="merriweather policy-title" ><?php echo _("Contribution") ?></h3> 
                <p class="policy-text"><?php echo _("Si vous vous connectez à notre site web pour contribuer au contenu, vos données de connexion, telle que votre pseudo, votre adresse IP et les informations de l’agent utilisateur de votre navigateur sont collectés pour nous aider à la détection des connexions indésirables. Votre mot de passe de connexion au site est stocké sur nos serveurs sous forme cryptée. Nous n'y avons pas accès en clair.") ?></p> 
                <h3 class="merriweather policy-title" ><?php echo _("Média") ?></h3> 
                <p class="policy-text"><?php echo _("Si vous êtes un utilisateur ou une utilisatrice enregistré·e et que vous téléversez des images sur le site web, nous vous conseillons d’éviter de téléverser des images contenant des données EXIF de coordonnées GPS. Les visiteurs de votre site web peuvent télécharger et extraire des données de localisation depuis ces images.") ?></p>
                <h3 class="merriweather policy-title" ><?php echo _("Contenu embarqué depuis d’autres sites") ?></h3> 
                <p class="policy-text"><?php echo _("Les articles de ce site peuvent inclure des contenus intégrés (par exemple des vidéos, images, articles…). Le contenu intégré depuis d’autres sites se comporte de la même manière que si le visiteur se rendait sur cet autre site. Ces sites web pourraient collecter des données sur vous, utiliser des cookies, embarquer des outils de suivis tiers, suivre vos interactions avec ces contenus embarqués si vous disposez d’un compte connecté sur leur site web.") ?></p>
                <h3 class="merriweather policy-title" ><?php echo _("Durée de stockage de vos données") ?></h3> 
                <p class="policy-text"><?php echo _("Si vous publiez des contenus sur ce site, ces contenus et leurs métadonnées sont conservés indéfiniment. Pour les utilisateurs et utilisatrices qui s’enregistrent sur notre site, nous stockons également les données personnelles indiquées dans leur profil. Tous les utilisateurs et utilisatrices peuvent voir, modifier ou supprimer leurs informations personnelles à tout moment (pseudonyme, e-mail, mini-biographie, avatar). Les gestionnaires du site peuvent aussi voir et modifier ces informations.") ?></p>
                </div>
        </section>
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
    <script src="js/menu.js"></script>
    <script src="js/menu-hover.js"></script>
</body>
</html>