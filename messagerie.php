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


function DateUSenFR($DateHeureEnUS) {
    $dateHeure = explode(' ' , $DateHeureEnUS);
    $tabDate = explode('-' , $dateHeure[0]);
    $dateenFR = $tabDate[2].'/'.$tabDate[1];
    return $dateenFR;
}

function DateUSenHeure($DateHeureEnUS) {
    $dateHeure = explode(' ' , $DateHeureEnUS);
    $tabDate = explode(':' , $dateHeure[1]);
    $dateenFR = $tabDate[0].'h'.$tabDate[1];
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

if(!(isset($_SESSION['IdUtilisateur']) AND $userinfo['IdUtilisateur'] == $_SESSION['IdUtilisateur'] AND $_SESSION['IdUtilisateur']!=0)) {
    Header('Location: connexion.php');
}

if(isset($_GET["conversation"])){
    $conversationId = $_GET["conversation"];
}else{
    $lastConvIdReq = $bdd->prepare('SELECT IdConversation FROM conversation WHERE IdUtilisateur = ? OR IdUtilisateur_MEMBRE_2 = ? ORDER BY DateDernierMessage DESC LIMIT 1');
    $lastConvIdReq->execute([$userinfo['IdUtilisateur'],$userinfo['IdUtilisateur']]);
    $lastConvId = $lastConvIdReq->fetch();
    $conversationId = $lastConvId["IdConversation"];
    if($conversationId != null){
        header('Location: messagerie.php?conversation='.$conversationId);
    }
}

$conversation = $bdd->prepare('SELECT * FROM conversation WHERE IdConversation = ?');
$conversation->execute(array($conversationId));
$conversationInfo = $conversation->fetch();


if($conversationInfo["IdUtilisateur"] == $userinfo['IdUtilisateur'])
    $idUtilisateur2 = $conversationInfo["IdUtilisateur_MEMBRE_2"];
else
    $idUtilisateur2 = $conversationInfo["IdUtilisateur"];

$utilisateurDeuxReq = $bdd->prepare('SELECT IdUtilisateur, NomUtilisateur, PseudoUtilisateur, AvatarUtilisateur FROM utilisateur WHERE IdUtilisateur = ?');
$utilisateurDeuxReq->execute([$idUtilisateur2]);
$infoConvUserDeux = $utilisateurDeuxReq->fetch();

if(isset($_POST["newMsg"])){
    if($_POST["newMsg"] != null){
        $newMsg = trim($_POST["newMsg"]);

        $dateMsgReq = $bdd->prepare('UPDATE conversation SET DateDernierMessage = now() WHERE IdConversation = ?');
        $dateMsgReq->execute([$conversationId]);
        $newmsgReq = $bdd->prepare('INSERT INTO messages (ContenuMessage, DateMessage, IdConversation, IdUtilisateur) VALUES(?, now(), ?, ?)');
        $newmsgReq->execute([$newMsg, $conversationId, $userinfo["IdUtilisateur"]]);
        unset($_POST["newMsg"]);
        unset($_POST["formNewMsg"]);

        $messagesReq = $bdd->prepare('SELECT * FROM messages WHERE IdConversation = ?');
        $messagesReq->execute(array($conversationId));
        while($message = $messagesReq->fetch()){
            if($message["IdUtilisateur"] == $infoConvUserDeux["IdUtilisateur"])
                $classMsg = "other";
            else
                $classMsg = "self";

            echo '<li class="'.$classMsg.'">
            <div class="msg">
                <p>'.$message["ContenuMessage"].'</p>';
                if($classMsg == "self"){
                    echo '<form method="POST" class="delete-msg">
                        <label>
                            <svg width="20px" height="20px" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 650" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;">
                                <path fill="#a2a2a2" d="M416,266l0,336c0,26.332 -21.668,48 -48,48l-288,0c-26.332,0 -48,-21.668 -48,-48l0,-336l384,0Zm-112,80c0,-8.777 7.223,-16 16,-16c8.777,0 16,7.223 16,16l0,224c0,8.777 -7.223,16 -16,16c-8.777,0 -16,-7.223 -16,-16l0,-224Zm-96,0c0,-8.777 7.223,-16 16,-16c8.777,0 16,7.223 16,16l0,224c0,8.777 -7.223,16 -16,16c-8.777,0 -16,-7.223 -16,-16l0,-224Zm-96,0c0,-8.777 7.223,-16 16,-16c8.777,0 16,7.223 16,16l0,224c0,8.777 -7.223,16 -16,16c-8.777,0 -16,-7.223 -16,-16l0,-224Z"/>
                                <path fill="#a2a2a2" class="top-bin" d="M432,170l-120,0l-9.4,-18.7c-4.055,-8.142 -12.404,-13.307 -21.5,-13.3l-114.3,0c-9.078,-0.035 -17.412,5.145 -21.4,13.3l-9.4,18.7l-120,0c-8.777,0 -16,7.223 -16,16l0,32c0,8.777 7.223,16 16,16l416,0c8.777,0 16,-7.223 16,-16l0,-32c0,-8.777 -7.223,-16 -16,-16Z"/>
                            </svg>
                            <input type="hidden" name="deleteMsgId" value="'.$message["IdMessage"].'">
                            <input class="delete-msg-input" type="submit" name="deleteMsg">
                    </label>
                    </form>';
                }
            echo '</div></li>';
        }
    }
exit();
}

if(isset($_POST["deleteMsg"])){
    $idMsgSup = $_POST["deleteMsgId"];
    $supMsgReq = $bdd->prepare('DELETE FROM messages WHERE IdMessage = ?');
    $supMsgReq->execute([$idMsgSup]);
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
    <link rel="stylesheet" href="css/messagerie.css">
    
<?php if($_SESSION["Language"] == "ar"){?><link rel="stylesheet" href="css/rtl.css"><?php } ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <title>Messagerie - Je viens d'ailleurs</title>
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
                    <a href="messagerie.php" class="current">
                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"
                            stroke-linejoin="round">
                            <path class="current"
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
        <?php
             $conversations = $bdd->prepare('SELECT * FROM conversation WHERE IdUtilisateur = ? OR IdUtilisateur_MEMBRE_2 = ? ORDER BY DateDernierMessage DESC');
             $conversations->execute([$userinfo['IdUtilisateur'], $userinfo['IdUtilisateur']]);
             if($conversations->rowCount() > 0){ ?>
                <div class="all-msg"> 
                        <div class="contact-container">  
                        <?php while($conv = $conversations->fetch()){
                            if($conv["IdUtilisateur"] == $userinfo['IdUtilisateur'])
                                $idUtilisateur2 = $conv["IdUtilisateur_MEMBRE_2"];
                            else
                                $idUtilisateur2 = $conv["IdUtilisateur"];

                            $utilisateurdeuxreq = $bdd->prepare('SELECT IdUtilisateur, NomUtilisateur, PseudoUtilisateur, AvatarUtilisateur FROM utilisateur WHERE IdUtilisateur = ?');
                            $utilisateurdeuxreq->execute(array($idUtilisateur2));
                            $utilisateurDeuxInfo = $utilisateurdeuxreq->fetch();
                        ?>
                            <a href="messagerie.php?conversation=<?php echo $conv["IdConversation"];?>" class="contact <?php if($conversationId == $conv["IdConversation"]){ echo 'contact-select'; } ?>">           
                                <img class="profile-img img-contact" src="utilisateur/avatars/<?php echo $utilisateurDeuxInfo["AvatarUtilisateur"]; ?>"
                                    alt="Photo de profil" />
                                <div class="profile-info">
                                    <h3 class="user-name raleway"><?php echo $utilisateurDeuxInfo["NomUtilisateur"]?></h3>
                                    <p class="user-pseudo poppins">@<?php echo $utilisateurDeuxInfo["PseudoUtilisateur"];?></p>
                                    <p class="contact-date raleway"><?= DateUSenFR($conv["DateDernierMessage"]) ?> à <?= DateUSenHeure($conv["DateDernierMessage"]) ?></p>
                                </div>
                            </a> 
                        <?php } ?>
                        </div>        
                   
                <?php if($userinfo['IdUtilisateur'] == $conversationInfo["IdUtilisateur"] || $userinfo['IdUtilisateur'] == $conversationInfo["IdUtilisateur_MEMBRE_2"]){?>
                <div class="messagerie">
                    <a href="profil.php?id=<?= $infoConvUserDeux["IdUtilisateur"]?>" class="top-messagerie">
                        <img class="profile-img" src="utilisateur/avatars/<?php echo $infoConvUserDeux["AvatarUtilisateur"]; ?>"
                        alt="Photo de profil" />
                        <div class="profile-info">
                            <h2 class="name raleway"><?php echo $infoConvUserDeux["NomUtilisateur"];?></h2>
                            <p class="user-pseudo poppins">@<?php echo $infoConvUserDeux["PseudoUtilisateur"];?></p>
                        </div>
                    </a>
                    <ul class="chat">
                    <?php 
                        $messagesReq = $bdd->prepare('SELECT * FROM messages WHERE IdConversation = ?');
                        $messagesReq->execute(array($conversationId));
                        while($message = $messagesReq->fetch()){
                            if($message["IdUtilisateur"] == $infoConvUserDeux["IdUtilisateur"])
                                $classMsg = "other";
                            else
                                $classMsg = "self";
                    ?>
                        <li class="<?= $classMsg ?>">
                            <div class="msg">
                                <p><?php echo $message["ContenuMessage"]; ?></p>
                                <?php if($classMsg == "self"){ ?>
                                    <form method="POST" class="delete-msg">
                                        <label>
                                            <svg width="20px" height="20px" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 650" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;">
                                                <path fill="#a2a2a2" d="M416,266l0,336c0,26.332 -21.668,48 -48,48l-288,0c-26.332,0 -48,-21.668 -48,-48l0,-336l384,0Zm-112,80c0,-8.777 7.223,-16 16,-16c8.777,0 16,7.223 16,16l0,224c0,8.777 -7.223,16 -16,16c-8.777,0 -16,-7.223 -16,-16l0,-224Zm-96,0c0,-8.777 7.223,-16 16,-16c8.777,0 16,7.223 16,16l0,224c0,8.777 -7.223,16 -16,16c-8.777,0 -16,-7.223 -16,-16l0,-224Zm-96,0c0,-8.777 7.223,-16 16,-16c8.777,0 16,7.223 16,16l0,224c0,8.777 -7.223,16 -16,16c-8.777,0 -16,-7.223 -16,-16l0,-224Z"/>
                                                <path fill="#a2a2a2" class="top-bin" d="M432,170l-120,0l-9.4,-18.7c-4.055,-8.142 -12.404,-13.307 -21.5,-13.3l-114.3,0c-9.078,-0.035 -17.412,5.145 -21.4,13.3l-9.4,18.7l-120,0c-8.777,0 -16,7.223 -16,16l0,32c0,8.777 7.223,16 16,16l416,0c8.777,0 16,-7.223 16,-16l0,-32c0,-8.777 -7.223,-16 -16,-16Z"/>
                                            </svg>                                           
                                            <input type="hidden" name="deleteMsgId" value="<?= $message["IdMessage"] ?>">
                                            <input class="delete-msg-input" type="submit" name="deleteMsg">
                                    </label>
                                    </form>
                                <?php } ?>
                            </div>
                        </li>
                    <?php } ?>
                    </ul>
                    <form autocomplete="off" class="typezone">
                        <input class="form-inputs" type="text" maxlenght="500" required placeholder="<?php echo _("Saisissez votre message")?>" name="newMsg">
                        <input type="submit" name="formNewMsg" value="<?php echo _("Envoyer")?>"
                                class="button-accent button button-big bottom-publish responsiv" >
                    </form>
                </div>
                <?php } else{ ?>
                    <div class="messagerie conv-error">
                        <img class="img-error" src="images/message-error.svg" alt="Illustration erreur messagerie" />
                        <p class="raleway msg-error">Vous ne participez pas à cette conversation</p>
                    </div>
                <?php } ?>
        </div>
        <?php } else{ ?>
            <div class="all-msg conv-error">
                <img class="img-error" src="images/message-error.svg" alt="Illustration erreur messagerie" />
                <p class="raleway msg-error">Pas de conversation</p>
                <p class="raleway msg-error">Visitez un profil ou une annonce pour démarrer une conversation</p>
            </div>
        <?php } ?>
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
    <script src="js/send-msg.js"></script>

</body>
</html>