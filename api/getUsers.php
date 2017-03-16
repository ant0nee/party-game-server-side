<?php

	header('Content-Type: application/json');
	if (file_exists("cache/".md5($_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].$_SERVER['QUERY_STRING']).".php")) {

		if (time()-filemtime("cache/".md5($_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].$_SERVER['QUERY_STRING']).".php")<=5) {

			$cache = fopen("cache/".md5($_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].$_SERVER['QUERY_STRING']).".php","r");
			echo substr(fread($cache,filesize("cache/".md5($_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].$_SERVER['QUERY_STRING']).".php")),29);
			fclose($cache);
			die(); 

		} 

	}

	if (file_exists("pcache/".md5($_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].$_SERVER['QUERY_STRING']).".php")) {

		$cache = fopen("pcache/".md5($_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].$_SERVER['QUERY_STRING']).".php","r");
		echo substr(fread($cache,filesize("pcache/".md5($_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].$_SERVER['QUERY_STRING']).".php")),29);
		fclose($cache);
		http_response_code(400);
		die(); 

	}

	ob_start();

	$cacheData = false; 
	require "DAO.php";
	require "Methods.php";
	require "encryption_management.php";
	$conn = new DAO(); 
	$m = new Methods(); 
	$em = new EncryptionManager();

	if (!isset($_GET["gameId"]) && !isset($_GET["secret"])) {

		echo json_encode(
			array(

				success => false,
				reason => "not enough parameters. Expected gameId and secret"

			)
		, JSON_PRETTY_PRINT);
		http_response_code(422);

	} else {

		$gameId = htmlspecialchars($_GET["gameId"]);
		$secret = $_GET["secret"];
		$data = $conn->query("SELECT secret FROM game WHERE gameId = $gameId");
		
		if (isset($data[0]->{'secret'})) {

			$hashedSecret = $data[0]->{'secret'};
			if ($em->compareHash($secret, $hashedSecret)) {

				$result = $conn->query("SELECT username, answer, score FROM user WHERE gameId = $gameId");
				echo json_encode($result);
				$cacheData = true; 

			} else {

				echo json_encode(
					array(

						success => false,
						reason => "incorrect secret"

					)
				, JSON_PRETTY_PRINT);
				http_response_code(401);

			}
			$cacheData = true; 

		} else {

			echo json_encode(
				array(

					success => false,
					reason => "incorrect gameId"

				)
			, JSON_PRETTY_PRINT);
			$cacheData = true; 
			http_response_code(400);

		}
	}

	if ($cacheData) {

		//temporary cache
		$data = fopen("cache/".md5($_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].$_SERVER['QUERY_STRING']).".php", "w");
		$enc = "<?php die('access denied') ?>".ob_get_contents();
		fwrite($data, $enc);
		fclose($data);

	} else {

		//permenant cache
		$data = fopen("pcache/".md5($_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].$_SERVER['QUERY_STRING']).".php", "w");
		$enc = "<?php die('access denied') ?>".ob_get_contents();
		fwrite($data, $enc);
		fclose($data);

	}

?>