<?php    
	    $hostname   = "localhost";
	    $databaseuser  = "visufo";
	    $databasepassword    = "7yKJGlzwdj07";
	    $database   = "visufo_jva";
     
	    $bdd = new PDO('mysql:host='.$hostname.';dbname='.$database.'', ''.$databaseuser.'', ''.$databasepassword.'');
		$bdd->exec("SET CHARACTER SET utf8");

?>