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
	$conn = new DAO(); 
	header("Access-Control-Allow-Origin: http://localhost:8001", false);
	if (!isset($_GET["username"]) && !isset($_GET["gameId"])) {

		echo json_encode(
			array(

				success => false,
				reason => "not enough parameters. Expected username and gameId"

			)
		, JSON_PRETTY_PRINT);
		http_response_code(422);

	} else {

		$cacheData = true; 
		$username = htmlspecialchars($_GET["username"]);
		$gameId = htmlspecialchars($_GET["gameId"]);
		$data = $conn->query("SELECT answer FROM user WHERE username = \"$username\" && gameId = \"$gameId\"");
		if (isset($data[0]->{'answer'})) {

			$canAnswer = false; 
			if ($data[0]->{'answer'} == null && $data[0]->{'answer'}!="") {

				$canAnswer = true; 

			}

			echo json_encode(
			array(

				success => true,
				canSubmit => $canAnswer

			)
			, JSON_PRETTY_PRINT);

		} else {

			echo json_encode(
			array(

				success => false,
				reason => "incorrect gameId"

			)
			, JSON_PRETTY_PRINT);
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