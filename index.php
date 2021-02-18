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
        putenv('LANG='.$locale);
        setlocale(LC_MESSAGES, $locale);
        bindtextdomain("messages", "./locale");
    }
    textdomain("messages");  
    
    $articles = $bdd->query('SELECT * FROM article WHERE ALaUneArticle="1" ORDER BY DateArticle DESC LIMIT 4');
	while($ar = $articles->fetch()) { 
	    if(isset($_POST['unpin-'.$ar["IdArticle"]])) {
            $insertpin = $bdd->prepare('UPDATE article SET ALaUneArticle = 0 WHERE IdArticle = ?');
            $insertpin->execute(array($ar["IdArticle"]));
        }
	}
    $articles = $bdd->query('SELECT * FROM article WHERE ALaUneArticle="1" ORDER BY DateArticle DESC LIMIT 4');
    $annonces = $bdd->query('SELECT * FROM annonce ORDER BY DateAnnonce DESC LIMIT 4');
    $profils = $bdd->query('SELECT * FROM utilisateur ORDER BY IdUtilisateur DESC');

function DateUSenFR($DateHeureEnUS) {
    $dateHeure = explode(' ' , $DateHeureEnUS);
    $tabDate = explode('-' , $dateHeure[0]);
    $dateenFR = $tabDate[2].'/'.$tabDate[1].'/'.$tabDate[0];
    return $dateenFR;
}

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
<?php if($_SESSION["Language"] == "ar"){?><link rel="stylesheet" href="css/rtl.css"><?php } ?>

    <link rel="stylesheet" href="css/utils.css">
    <link rel="stylesheet" href="css/index.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <title>Je viens d'ailleurs - La plateforme d'aide aux réfugiés dans l'ouest</title>
    <meta name="title" content="Je viens d'ailleurs - La plateforme d'aide aux réfugiés dans l'ouest">
    <meta name="description" content="“Je viens d’ailleurs”, est une plateforme d’aide aux réfugiés pour l’intégration dans la société française. Nous avons conçu un produit permettant aux réfugiés d’entrer en relation avec des français, des associations ou même d’autres réfugiés prêts à aider.">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="jeviensdailleurs.fr/index.php">
    <meta property="og:title" content="Je viens d'ailleurs - La plateforme d'aide aux réfugiés dans l'ouest">
    <meta property="og:description" content="Je viens d’ailleurs”, est une plateforme d’aide aux réfugiés pour l’intégration dans la société française. Nous avons conçu un produit permettant aux réfugiés d’entrer en relation avec des français, des associations ou même d’autres réfugiés prêts à aider.">
    <meta property="og:image" content="/images/logo-metadata.svg">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="jeviensdailleurs.fr/index.php">
    <meta property="twitter:title" content="Je viens d'ailleurs - La plateforme d'aide aux réfugiés dans l'ouest">
    <meta property="twitter:description" content="Je viens d’ailleurs”, est une plateforme d’aide aux réfugiés pour l’intégration dans la société française. Nous avons conçu un produit permettant aux réfugiés d’entrer en relation avec des français, des associations ou même d’autres réfugiés prêts à aider.">
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
    <div class="left-menu">

        <?php if($ismoderateur != null){ ?>
        <a href="poster-article.php"
            class="button button-red button-big btn-article"><?php  echo _("Poster un article")?></a>
        <?php } ?>
        <a href="poster-annonce.php" class="button button-accent button-big"><?php  echo _("Poster une annonce")?></a>
        <ul class="menu-links">
            <li><a href="index.php" class="current">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"
                        stroke-linejoin="round">
                        <path class="current"
                            d="M2.7 12.9l-0.7 0.6c-0.4 0.4-0.8 0.1-0.9 0l-0.9-1c-0.3-0.4-0.1-0.8 0.1-1l10.7-9.5c0.3-0.2 0.8-0.4 1.1-0.4 0.4 0 0.9 0.2 1.1 0.4l10.7 9.5c0.1 0.1 0.4 0.6 0.1 1 0 0-0.9 1-0.9 1 -0.1 0.1-0.5 0.4-0.9 0.1l-0.7-0.6 0 8.2c0 0.7-0.6 1.3-1.3 1.3l-16 0c-0.7 0-1.3-0.6-1.3-1.3l0-8.2Zm11.1-2.5l-3.6 0c-0.5 0-0.9 0.4-0.9 0.9l0 3.6c0 0.5 0.4 0.9 0.9 0.9l3.6 0c0.5 0 0.9-0.4 0.9-0.9l0-3.6c0-0.5-0.4-0.9-0.9-0.9Z"
                            fill="#a2a2a2" />
                    </svg>
                    <?php  echo _("Accueil");?>
                </a>
            </li>
            <li>
                <a href="articles.php">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"
                        style="clip-rule:evenodd;fill-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2">
                        <path
                            d="M23 3.9l-18.3 0c-0.9 0-1.6 0.6-1.9 1.4l-1.8 0c-0.6 0-1 0.5-1 1l0 11.5c0 1.3 1 2.4 2.3 2.4l20.7 0c0.6 0 1-0.5 1-1l0-14.2c0-0.6-0.4-1-1-1Zm-18.4 14.2l17.4 0 0-12.2 -17.3 0 0 11.8c0 0.1 0 0.2 0 0.3Zm-2.6-0.3l0-10.5 0.7 0 0 10.5c0 0.2-0.1 0.3-0.3 0.3 -0.2 0-0.3-0.2-0.3-0.3Zm5.2-4.7l5.7 0c0.3 0 0.5-0.2 0.5-0.5l0-4.1c0-0.3-0.2-0.5-0.5-0.5l-5.7 0c-0.3 0-0.5 0.2-0.5 0.5l0 4.1c0 0.3 0.2 0.5 0.5 0.5Zm8-3l4.3 0c0.3 0 0.5-0.2 0.5-0.5l0-1c0-0.3-0.2-0.5-0.5-0.5l-4.3 0c-0.3 0-0.5 0.2-0.5 0.5l0 1c0 0.3 0.2 0.5 0.5 0.5Zm-3.5-0.3l-3.3 0 0 1.7 3.3 0 0-1.7Zm7.8 3.4l-4.3 0c-0.3 0-0.5-0.2-0.5-0.5l0-1c0-0.3 0.2-0.5 0.5-0.5l4.3 0c0.3 0 0.5 0.2 0.5 0.5l0 1c0 0.3-0.2 0.5-0.5 0.5Zm-12.3 3l5.7 0c0.3 0 0.5-0.2 0.5-0.5l0-1c0-0.3-0.2-0.5-0.5-0.5l-5.7 0c-0.3 0-0.5 0.2-0.5 0.5l0 1c0 0.3 0.2 0.5 0.5 0.5Zm12.3 0l-4.3 0c-0.3 0-0.5-0.2-0.5-0.5l0-1c0-0.3 0.2-0.5 0.5-0.5l4.3 0c0.3 0 0.5 0.2 0.5 0.5l0 1c0 0.3-0.2 0.5-0.5 0.5Z"
                            fill="#a2a2a2" /></svg>
                    <?php  echo _("Articles")?>
                </a>
            </li>
            <li>
                <a href="annonces.php">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"
                        style="clip-rule:evenodd;fill-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2">
                        <path
                            d="M19.5 7.2l-1.8-1.8c-0.4-0.4-0.9-0.6-1.5-0.6l-8.5 0c-0.6 0-1.1 0.2-1.5 0.6l-1.8 1.8 -4.4 0 0 9.6 2.4 0c0.7 0 1.2-0.5 1.2-1.2l0.3 0 3.2 2.9c1.2 0.9 2.8 1 4 0.1 0.5 0.4 1 0.6 1.5 0.6 0.7 0 1.3-0.3 1.8-0.9 0.8 0.3 1.8 0.1 2.4-0.6l1-1.2c0.2-0.3 0.3-0.6 0.4-0.9l2.2 0c0 0.7 0.5 1.2 1.2 1.2l2.4 0 0-9.6 -4.5 0Zm-4 9.4l1-1.2c0.1-0.1 0.1-0.3 0-0.4l-4.1-3.3 -0.3 0.3c-1.5 1.4-3.3 0.4-3.8-0.2 -1-1.1-0.9-2.8 0.2-3.8l1.5-1.3 -2.1 0c-0.1 0-0.1 0.1-0.2 0.1l-2.3 2.3 -1.6 0 0 4.8 1 0 3.6 3.3c0.6 0.5 1.6 0.4 2.1-0.2l0.6-0.7 1.4 1.2c0.1 0.1 0.5 0.2 0.7-0.1l1.1-1.4 0.9 0.7c0.1 0.1 0.3 0.1 0.4 0Zm5-2.8l-2.6 0c-0.1-0.1-0.2-0.2-0.3-0.3l-3.9-3.1 0.5-0.4c0.2-0.2 0.3-0.6 0-0.9l-0.4-0.4c-0.2-0.2-0.6-0.3-0.8 0l-2.1 1.9c-0.4 0.3-1 0.4-1.3 0 -0.3-0.4-0.3-0.9 0-1.3l2.5-2.3c0.3-0.3 0.6-0.4 1-0.4l3.1 0c0.1 0 0.2 0 0.2 0.1l2.3 2.3 1.7 0 0 4.8Zm-18.6 1.8c-0.3 0-0.6-0.3-0.6-0.6 0-0.3 0.3-0.6 0.6-0.6 0.3 0 0.6 0.3 0.6 0.6 0 0.3-0.3 0.6-0.6 0.6Zm19.8-0.6c0 0.3 0.3 0.6 0.6 0.6 0.3 0 0.6-0.3 0.6-0.6 0-0.3-0.3-0.6-0.6-0.6 -0.3 0-0.6 0.3-0.6 0.6l0 0Z"
                            fill="#a2a2a2" /></svg>
                    <?php  echo _("Annonces")?>
                </a>
            </li>
            <li>
                <a href="messagerie.php">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"
                        stroke-linejoin="round">
                        <path
                            d="M12 1.5c-6.6 0-12 4.4-12 9.8 0 2.3 1 4.5 2.7 6.1 -0.6 2.4-2.5 4.5-2.6 4.5 -0.1 0.1-0.1 0.3-0.1 0.4 0.1 0.1 0.2 0.2 0.3 0.2 3.1 0 5.4-1.5 6.6-2.4 1.5 0.6 3.2 0.9 5 0.9 6.6 0 12-4.4 12-9.7 0-5.4-5.4-9.7-12-9.7Z"
                            fill="#a2a2a2" /></svg>
                    <?php  echo _("Messages")?>
                </a>
            </li>
        </ul>
        <?php 
                if(empty(isset($_SESSION['IdUtilisateur'])) || $_SESSION['IdUtilisateur']==0) {
            ?>
        <a href="connexion.php" class="button button-accent button-big"><?php echo _("Connexion") ?></a>
        <p class="new-account"><?php  echo _("ou")?> <a href="inscription.php"> <?php echo _("créer un compte") ?></a>
        </p>
        <?php 
                }
            ?>
        <?php     
            if(isset($_SESSION['IdUtilisateur']) AND $userinfo['IdUtilisateur'] == $_SESSION['IdUtilisateur'] AND $_SESSION['IdUtilisateur']!=0) {

            ?>

        <a class="profile" href="profil.php">
            <?php
                if(!empty($userinfo['AvatarUtilisateur']))
                {
                    ?>
            <img class="profile-img" src="utilisateur/avatars/<?php echo $userinfo['AvatarUtilisateur']; ?>"
                alt="Photo de profil" />
            <?php
                }
             ?>
            <div class="profile-info">
                <h3 class="user-name poppins"><?php echo $userinfo['NomUtilisateur'];?></h3>
                <p class="user-pseudo poppins">@<?php echo $userinfo['PseudoUtilisateur'];?></p>
            </div>
        </a>
        <a href="deconnexion.php" class="button button-red button-big"><?php echo _("Déconnexion") ?></a>

        <?php   
	                }
            ?>

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
            }?>
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
        <header class="landing-header">
            <div class="left-landing">
                <?php     
            if(isset($_SESSION['IdUtilisateur']) AND $userinfo['IdUtilisateur'] == $_SESSION['IdUtilisateur']) {
            ?>
                <h3 class="raleway hello"><a href="profil.php"><?php echo _("Bonjour"); ?><span class="accent">
                            <?php echo $userinfo['NomUtilisateur'];?></span></a>,
                </h3>
                <?php   
	                }
            ?>
                <h1><?php echo _("Bienvenue sur") ?> <span class="accent"><strong> Je viens d'ailleurs</strong></span>,
                </h1>
                <p class="raleway"><?php echo _("La plateforme d'aide aux réfugiés dans l'ouest") ?></p>
                
                <img class="header-illustration" src="images/home-header.svg"
                    alt="Illustration présentant la plateforme" />
            </div>
            <div class="right-landing">
                <div class="landing-info info-1">
                    <svg viewBox="0 0 500 500" version="1.1" xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve"
                        style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;">
                        <path fill="#a2a2a2"
                            d="M75,225c27.578,0 50,-22.422 50,-50c0,-27.578 -22.422,-50 -50,-50c-27.578,0 -50,22.422 -50,50c0,27.578 22.422,50 50,50Zm350,0c27.578,0 50,-22.422 50,-50c0,-27.578 -22.422,-50 -50,-50c-27.578,0 -50,22.422 -50,50c0,27.578 22.422,50 50,50Zm25,25l-50,0c-13.75,0 -26.172,5.547 -35.234,14.531c31.484,17.266 53.828,48.438 58.671,85.469l51.563,0c13.828,0 25,-11.172 25,-25l0,-25c0,-27.578 -22.422,-50 -50,-50Zm-200,0c48.359,0 87.5,-39.141 87.5,-87.5c0,-48.359 -39.141,-87.5 -87.5,-87.5c-48.359,0 -87.5,39.141 -87.5,87.5c0,48.359 39.141,87.5 87.5,87.5Zm60,25l-6.484,0c-16.25,7.813 -34.297,12.5 -53.516,12.5c-19.219,0 -37.188,-4.688 -53.516,-12.5l-6.484,0c-49.688,0 -90,40.313 -90,90l0,22.5c0,20.703 16.797,37.5 37.5,37.5l225,0c20.703,0 37.5,-16.797 37.5,-37.5l0,-22.5c0,-49.688 -40.313,-90 -90,-90Zm-174.766,-10.469c-9.062,-8.984 -21.484,-14.531 -35.234,-14.531l-50,0c-27.578,0 -50,22.422 -50,50l0,25c0,13.828 11.172,25 25,25l51.484,0c4.922,-37.031 27.266,-68.203 58.75,-85.469Z" />
                    </svg>
                    <p class=" raleway"><?php echo _("Sur") ?> <strong> Je viens d'ailleurs </strong>
                        <?php echo _("vous pouvez trouver de l'aide grâce à notre communauté grandissante.")?></p>
                </div>
                <div class="landing-info info-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                        style="clip-rule:evenodd;fill-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2">
                        <path
                            d="M19.5 7.2l-1.8-1.8c-0.4-0.4-0.9-0.6-1.5-0.6l-8.5 0c-0.6 0-1.1 0.2-1.5 0.6l-1.8 1.8 -4.4 0 0 9.6 2.4 0c0.7 0 1.2-0.5 1.2-1.2l0.3 0 3.2 2.9c1.2 0.9 2.8 1 4 0.1 0.5 0.4 1 0.6 1.5 0.6 0.7 0 1.3-0.3 1.8-0.9 0.8 0.3 1.8 0.1 2.4-0.6l1-1.2c0.2-0.3 0.3-0.6 0.4-0.9l2.2 0c0 0.7 0.5 1.2 1.2 1.2l2.4 0 0-9.6 -4.5 0Zm-4 9.4l1-1.2c0.1-0.1 0.1-0.3 0-0.4l-4.1-3.3 -0.3 0.3c-1.5 1.4-3.3 0.4-3.8-0.2 -1-1.1-0.9-2.8 0.2-3.8l1.5-1.3 -2.1 0c-0.1 0-0.1 0.1-0.2 0.1l-2.3 2.3 -1.6 0 0 4.8 1 0 3.6 3.3c0.6 0.5 1.6 0.4 2.1-0.2l0.6-0.7 1.4 1.2c0.1 0.1 0.5 0.2 0.7-0.1l1.1-1.4 0.9 0.7c0.1 0.1 0.3 0.1 0.4 0Zm5-2.8l-2.6 0c-0.1-0.1-0.2-0.2-0.3-0.3l-3.9-3.1 0.5-0.4c0.2-0.2 0.3-0.6 0-0.9l-0.4-0.4c-0.2-0.2-0.6-0.3-0.8 0l-2.1 1.9c-0.4 0.3-1 0.4-1.3 0 -0.3-0.4-0.3-0.9 0-1.3l2.5-2.3c0.3-0.3 0.6-0.4 1-0.4l3.1 0c0.1 0 0.2 0 0.2 0.1l2.3 2.3 1.7 0 0 4.8Zm-18.6 1.8c-0.3 0-0.6-0.3-0.6-0.6 0-0.3 0.3-0.6 0.6-0.6 0.3 0 0.6 0.3 0.6 0.6 0 0.3-0.3 0.6-0.6 0.6Zm19.8-0.6c0 0.3 0.3 0.6 0.6 0.6 0.3 0 0.6-0.3 0.6-0.6 0-0.3-0.3-0.6-0.6-0.6 -0.3 0-0.6 0.3-0.6 0.6l0 0Z"
                            fill="#a2a2a2" /></svg>
                    <p class="raleway">
                        <?php echo _("N'importe qui peut aider à son échelle. N'importe qui peut se faire aider. Voilà ce qui nous caractérise.") ?>
                    </p>
                </div>
                <a href="inscription.php" class="landing-info info-2">
                    <svg viewBox="0 0 500 500" version="1.1" xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve"
                        style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;">
                        <path fill="#a2a2a2"
                            d="M380.938,403.834l-68.743,0c-5.401,0 -9.82,-4.419 -9.82,-9.82l0,-32.735c0,-5.401 4.419,-9.82 9.82,-9.82l68.743,0c14.484,0 26.187,-11.702 26.187,-26.187l0,-157.125c0,-14.485 -11.703,-26.188 -26.187,-26.188l-68.743,0c-5.401,0 -9.82,-4.419 -9.82,-9.82l0,-32.735c0,-5.401 4.419,-9.82 9.82,-9.82l68.743,0c43.373,0 78.562,35.19 78.562,78.563l0,157.125c0,43.373 -35.189,78.562 -78.562,78.562Zm-38.463,-164.49l-137.485,-137.484c-12.275,-12.276 -33.552,-3.683 -33.552,13.912l0,78.562l-111.297,0c-10.885,0 -19.641,8.757 -19.641,19.641l0,78.562c0,10.884 8.756,19.641 19.641,19.641l111.297,0l0,78.562c0,17.595 21.277,26.188 33.552,13.913l137.485,-137.485c7.61,-7.692 7.61,-20.131 0,-27.824Z" />
                    </svg>
                    <p class="raleway">
                        <?php echo _("Inscrivez vous dès maintenant pour poster une annonce et découvrir la communauté.")?>
                    </p>
                </a>
            </div>

        </header>
        <section class="articles">
            <header>
                <h2 class="merriweather"><?php echo _("À la une cette")?><span class="accent"> <?php echo _("semaine")?></span>...</h2>
                <a href="articles.php"
                    class="button button-accent button-medium"><?php echo _("Tous les articles")?></a>
            </header>
            <div class="posts-list articles-list">
                <?php while($ar = $articles->fetch()) { ?>
                <div class="post-box post-<?= $ar["IdArticle"] ?>">
                    <a href="article.php?id=<?= $ar["IdArticle"] ?>">
                        <h2 class="post-title"><?= $ar["NomArticle"] ?></h2>
                        <p class="post-date"><?= DateUSenFR($ar["DateArticle"]) ?></p>
                        <img class="post-img" src="utilisateur/ImgArticle/<?= $ar["ImageArticle"] ?>?>"
                            alt="Illustration de la publication" />
                    </a>
                    <div class="info-wrapper">
                        <div class="categories">
                            <?php
                            $reqcategorie = $bdd->prepare('SELECT article.IdArticle, categorie.NomCategorie, categorie.CouleurHEXCategorie FROM categorie INNER JOIN est_de_categorie ON categorie.NomCategorie = est_de_categorie.NomCategorie INNER JOIN article ON est_de_categorie.IdArticle = article.IdArticle WHERE article.IdArticle = ?'); 
                            $reqcategorie->execute(array($ar["IdArticle"]));
                            $i = 0;
                            while($catar = $reqcategorie->fetch()) { ?>
                            <style>
                                a.<?=$catar["NomCategorie"] ?> {
                                    color: <?=$catar["CouleurHEXCategorie"] ?>;
                                }

                                <?php if($i==0) {
                                    ?>.post-<?=$catar["IdArticle"] ?> {
                                        background-color: <?=$catar["CouleurHEXCategorie"] ?>;
                                    }

                                    <?php $i++;
                                }

                                ?>
                            </style>
                            <a href="articles.php?categorie=<?= $catar["NomCategorie"] ?>" class="category <?= $catar["NomCategorie"] ?>"><?= $catar["NomCategorie"] ?></a>
                            <?php } ?>
                        </div>
                    </div>
                    <?php if($ismoderateur != null){ ?>
                    <form method="POST" class="pin-article">
                        <label>
                            <svg width="100%" height="100%" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"
                                xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve"
                                style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;">
                                <path fill="#fff"
                                    d="M342.218,72.91c-13.59,-23.008 -10.5,-53.141 9.269,-72.91l160.513,160.513c-19.769,19.769 -49.901,22.859 -72.91,9.27l0,0l-115.397,148.906l4.459,4.459c35.147,35.147 35.147,92.132 0,127.279l-122.729,-122.729l-184.254,184.253l-21.21,-21.21l184.254,-184.253l-122.64,-122.64c35.147,-35.147 92.132,-35.147 127.279,0l4.459,4.459l148.906,-115.397l0.001,0Z"
                                    style="fill-rule:nonzero;" />
                            </svg>
                            <input type="submit" name="unpin-<?= $ar["IdArticle"] ?>">
                        </label>
                    </form>
                    <?php } ?>
                </div>
                <?php } ?>
            </div>
        </section>
        <section class="offers">
            <header>
                <h2 class="merriweather"><?php echo _("Les dernières")?><span class="accent">
                        <?php echo _("annonces")?></span>...</h2>
                <a href="annonces.php"
                    class="button button-accent button-medium"><?php echo _("Toutes les annonces")?></a>
            </header>
            <div class="posts-list articles-list">
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
                                a.<?=$catan["NomCategorie"] ?> {
                                    color: <?=$catan["CouleurHEXCategorie"] ?>;
                                }

                                <?php if($i==0) {
                                    ?>.post-<?=$catan["IdAnnonce"] ?> {
                                        background-color: <?=$catan["CouleurHEXCategorie"] ?>;
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
                            <img class="profile-img" src="utilisateur/avatars/<?= $profilInfo["AvatarUtilisateur"] ?>"
                                alt="Photo de profil" />
                            <div class="profile-info">
                                <h3 class="user-name poppins"><?= $profilInfo["NomUtilisateur"] ?></h3>
                                <p class="user-pseudo poppins">@<?= $profilInfo["PseudoUtilisateur"] ?></p>
                            </div>
                        </a>
                    </div>
                </div>
                <?php } ?>
            </div>
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
</body>
</html>