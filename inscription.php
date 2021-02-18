<?php
    session_start();

    require_once('database.php');

    $reqUserTest = $bdd->prepare('SELECT IdUtilisateur FROM utilisateur WHERE IdUtilisateur = ?');
    $reqUserTest->execute([$_SESSION["IdUtilisateur"]]);
    if(($reqUserTest->rowCount() == 0 && isset($_SESSION["IdUtilisateur"]))){
        session_destroy();
        header('Location: index.php');
    }

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

    
if(!isset($_SESSION['IdUtilisateur'])){
	if(isset($_POST['forminscription'])) {
	   $nom = htmlspecialchars($_POST['nom']);
	   $pseudo = htmlspecialchars($_POST['pseudo']);
	   $mail = htmlspecialchars($_POST['mail']);
	   $mdp = sha1($_POST['mdp']);
       $mdp2 = sha1($_POST['mdp2']);
	   if(!empty($_POST['nom']) AND !empty($_POST['pseudo']) AND !empty($_POST['mail']) AND !empty($_POST['mdp']) AND !empty($_POST['mdp2'])) {
	      $pseudolength = strlen($pseudo);
	      if($pseudolength <= 255) {
			  $nomlength = strlen($nom);
			  if($nomlength <= 255) {
	            if(filter_var($mail, FILTER_VALIDATE_EMAIL)) {
	               $reqmail = $bdd->prepare('SELECT * FROM utilisateur WHERE MailUtilisateur = ?');
	               $reqmail->execute(array($mail));
                   $mailexist = $reqmail->rowCount();
                   $reqpseudo = $bdd->prepare('SELECT * FROM utilisateur WHERE PseudoUtilisateur = ?');
	               $reqpseudo->execute(array($pseudo));
	               $pseudoexist = $reqpseudo->rowCount();
	               if($mailexist == 0) {
                       if($pseudoexist == 0) {
					   $mdplength = strlen($_POST['mdp']);
					  if($mdplength >= 8){
	                  if($mdp == $mdp2) {
						if(!empty($_POST['switchAsso'])){
						 $insertmbr = $bdd->prepare('INSERT INTO utilisateur (NomUtilisateur, PseudoUtilisateur, MailUtilisateur, MdpUtilisateur, DateInscriptionUtilisateur, AssociationUtilisateur, AvatarUtilisateur) VALUES(?, ?, ?, ?, now(), 1,"0_A.jpg")');
						 $insertmbr->execute(array($nom, $pseudo, $mail, $mdp));
                         $erreur = _("Votre compte associatif a bien été créé !");
                         $_SESSION['IdUtilisateur'] = 0;	
                         header("Location: connexion.php");
                         
						}else{
	                     $insertmbr = $bdd->prepare('INSERT INTO utilisateur (NomUtilisateur, PseudoUtilisateur, MailUtilisateur, MdpUtilisateur, DateInscriptionUtilisateur, AvatarUtilisateur) VALUES(?, ?, ?, ?, now(), "0_U.jpg")');
	                     $insertmbr->execute(array($nom, $pseudo, $mail, $mdp));
                         $erreur = _("Votre compte a bien été créé !");
                         $_SESSION['IdUtilisateur'] = 0;
                         header("Location: connexion.php");
                         
						}
					
	                  } else {
	                     $erreur = _("Vos mots de passe ne correspondent pas !");
					  }
					}else{
						$erreur = _("Mot de passe trop court ! (8 caractères minimum)");
                    }
                }else {
                    $erreur = _("Pseudo déjà utilisé !");
                }
	               } else {
	                  $erreur = _("Adresse mail déjà utilisée !");
                   }

				
	            } else {
	               $erreur = _("Votre adresse mail n'est pas valide !");
	            }
	      } else {
			$erreur = _("Votre nom ne peut pas dépasser 255 caractères !");
		  }
		}else{
			$erreur = _("Votre pseudo ne doit pas dépasser 255 caractères !");
		}
	   } else {
	      $erreur = _("Tous les champs doivent être complétés !");
	   }
    }
}else{
    header("Location: index.php");
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
    <link rel="stylesheet" href="css/signin-signup.css">
<?php if($_SESSION["Language"] == "ar"){?><link rel="stylesheet" href="css/rtl.css"><?php } ?>
    <title>Inscription - Je viens d'ailleurs</title>
    <meta name="title" content="Inscription - Je viens d'ailleurs">
    <meta name="description" content="Inscrivez-vous sur Je viens d'ailleurs, la plateforme d'aide aux réfugiés dans l'ouest">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="jeviensdailleurs.fr/inscription.php">
    <meta property="og:title" content="Inscription - Je viens d'ailleurs">
    <meta property="og:description" content="Inscrivez-vous sur Je viens d'ailleurs, la plateforme d'aide aux réfugiés dans l'ouest">
    <meta property="og:image" content="/images/logo-metadata.svg">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="jeviensdailleurs.fr/inscription.php">
    <meta property="twitter:title" content="Inscription - Je viens d'ailleurs">
    <meta property="twitter:description" content="Inscrivez-vous sur Je viens d'ailleurs, la plateforme d'aide aux réfugiés dans l'ouest">
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
        <div class="sign-form">
            <header>
                <h1 class="merriweather"><?php echo _("Inscrivez-vous")?></h1>
                <p class="raleway"><?php echo _("Pour rejoindre la communauté")?></p>
                <h2 class="merriweather">Je viens <span class="accent">d'ailleurs</span></h2>
            </header>
            <form method="POST" action="">
                <label for="asso" class="switch raleway">
                    <input name="switchAsso" id="asso" type="checkbox" value="oui">
                    <?php echo _("Au nom d'une association")?>
                    <span class="slider round"></span>
                </label>
                <div class="fullname">
                    <input name="nom" required class="sign-input" type="text" id="fullname" placeholder="<?php echo _("Nom et prénom")?>"
                        aria-placeholder="<?php echo _("Nom et prénom")?>" value="<?php if(isset($nom)) { echo $nom; } ?>">
                    <svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 448 512">
                        <path fill="#A6A6A6"
                            d="M224 256c70.7 0 128-57.3 128-128S294.7 0 224 0 96 57.3 96 128s57.3 128 128 128zm89.6 32h-16.7c-22.2 10.2-46.9 16-72.9 16s-50.6-5.8-72.9-16h-16.7C60.2 288 0 348.2 0 422.4V464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48v-41.6c0-74.2-60.2-134.4-134.4-134.4z">
                        </path>
                    </svg>
                </div>
                <div class="pseudo">
                    <input name="pseudo" required class="sign-input" type="text" id="pseudo" placeholder="<?php echo _("Pseudo")?>"
                        aria-placeholder="<?php echo _("Pseudo")?>" value="<?php if(isset($pseudo)) { echo $pseudo; } ?>" pattern="[^-\s].{3,15}">
                    <svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 448 512">
                        <path fill="#A6A6A6"
                            d="M224 256c70.7 0 128-57.3 128-128S294.7 0 224 0 96 57.3 96 128s57.3 128 128 128zm89.6 32h-16.7c-22.2 10.2-46.9 16-72.9 16s-50.6-5.8-72.9-16h-16.7C60.2 288 0 348.2 0 422.4V464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48v-41.6c0-74.2-60.2-134.4-134.4-134.4z">
                        </path>
                    </svg>
                </div>
                <span class="error raleway"></span>
                <div class="email">
                    <input name="mail" required class="sign-input" type="email" id="email" placeholder="<?php echo _("Adresse e-mail")?>"
                        aria-placeholder="<?php echo _("Adresse e-mail")?>" value="<?php if(isset($mail)) { echo $mail; } ?>" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$">
                    <svg viewBox="0 0 512 512" aria-hidden="true" focusable="false" role="img"
                        xmlns="http://www.w3.org/2000/svg" xml:space="preserve"
                        style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;">
                        <path fill="#A6A6A6"
                            d="M464,64l-416,0c-26.517,0 -48,21.483 -48,48l0,288c0,26.517 21.483,48 48,48l416,0c26.517,0 48,-21.483 48,-48l0,-288c0,-26.517 -21.483,-48 -48,-48Zm0,48l0,40.811c-22.421,18.261 -58.176,46.656 -134.592,106.474c-2.816,2.24 -6.123,4.992 -9.749,8.022c-18.006,15.018 -44.352,36.992 -63.659,36.693c-19.307,0.299 -45.653,-21.675 -63.659,-36.693c-3.626,-3.03 -6.933,-5.782 -9.749,-8.022c-76.416,-59.818 -112.171,-88.213 -134.592,-106.474l0,-40.811l416,0Zm-416,102.4l0,185.6l416,0l0,-185.6c-22.912,18.261 -55.424,43.861 -104.939,82.645c-3.242,2.56 -6.784,5.504 -10.624,8.704c-22.869,19.03 -55.85,46.443 -92.437,46.251c-36.736,0.192 -70.08,-27.605 -92.629,-46.4c-3.798,-3.157 -7.275,-6.08 -10.432,-8.555c-49.536,-38.784 -82.027,-64.405 -104.939,-82.645Z" />
                    </svg>
                </div>
                <span class="error raleway"></span>
                <div class="password">
                    <input name="mdp" required class="sign-input" type="password" id="password"
                        placeholder="<?php echo _("Mot de passe")?>" aria-placeholder="<?php echo _("Mot de passe")?>" pattern="(?=.*[a-z]).{8,}">
                    <svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 25 28">
                        <path fill="#A6A6A6" fill-rule="evenodd"
                            d="M20.98214722 12.25h1.33927917C23.80021667 12.25 25 13.42578125 25 14.875v10.5C25 26.82421875 23.80021667 28 22.3214264 28H2.6785736C1.19978334 28 0 26.82421875 0 25.375v-10.5c0-1.44921875 1.19978333-2.625 2.6785736-2.625h1.33927918V8.3125C4.01785278 3.7296753 7.82365418 0 12.5 0c4.67634583 0 8.48214722 3.7296753 8.48214722 8.3125V12.25zm-12.5-3.9375V12.25h8.03572082V8.3125c0-2.17108154-1.80245971-3.9375-4.01786804-3.9375-2.21539307 0-4.01785278 1.76641846-4.01785278 3.9375z" />
                    </svg>
                </div>
                <span class="error raleway"></span>
                <div class="password-confirm">
                    <input name="mdp2" required class="sign-input" type="password" id="password-confirm"
                        placeholder="<?php echo _("Confirmation du mot de passe")?>" aria-placeholder="<?php echo _("Confirmation du mot de passe")?>" pattern="(?=.*[a-z]).{8,}">
                    <svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 25 28">
                        <path fill="#A6A6A6" fill-rule="evenodd"
                            d="M20.98214722 12.25h1.33927917C23.80021667 12.25 25 13.42578125 25 14.875v10.5C25 26.82421875 23.80021667 28 22.3214264 28H2.6785736C1.19978334 28 0 26.82421875 0 25.375v-10.5c0-1.44921875 1.19978333-2.625 2.6785736-2.625h1.33927918V8.3125C4.01785278 3.7296753 7.82365418 0 12.5 0c4.67634583 0 8.48214722 3.7296753 8.48214722 8.3125V12.25zm-12.5-3.9375V12.25h8.03572082V8.3125c0-2.17108154-1.80245971-3.9375-4.01786804-3.9375-2.21539307 0-4.01785278 1.76641846-4.01785278 3.9375z" />
                    </svg>
                </div>
                <span class="error raleway"></span>
                <input type="submit" name="forminscription" value="<?php echo _("Je m'inscris")?>"
                    class="button button-accent button-big confirm-signin" />
                <p class="new-account"><?php echo _("ou")?><a href="connexion.php"> <?php echo _("connectez-vous")?></a></p>
            </form>
            <?php
	         if(isset($erreur)) {
	            echo '<font color="red">'.$erreur."</font>";
	         }
             ?>

        </div>
        <img src="images/sign-illustration.svg" class="sign-illustration" alt="Illustration présentant la plateforme" />
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

    <script type="text/javascript">
        let switchAsso = document.getElementById('asso');
        (function () {
            switchAsso.addEventListener("click", clickAsso);
        })();

        function clickAsso(evt) {
            if (this.checked == true) {
                document.getElementById("fullname").placeholder = "<?php echo _("Nom de votre association")?>";
            } else {
                document.getElementById("fullname").placeholder = "<?php echo _("Nom et prénom")?>";
            }
        }
    </script>
    <script src="js/menu.js"></script>
    <script src="js/forms-confirmation.js"></script>

</body>

</html>