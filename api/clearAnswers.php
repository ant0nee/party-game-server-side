<?php

	header('Content-Type: application/json');
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
				reason => "not enough parameters. Expected name and code"

			)
		, JSON_PRETTY_PRINT);
		http_response_code(422);
		die(); 

	}

	$gameId = htmlspecialchars($_GET["gameId"]);
	$secret = $_GET["secret"];
	$data = $conn->query("SELECT secret FROM game WHERE gameId = $gameId");
	if (isset($_GET["answerType"])) {

		$answerType = htmlspecialchars($_GET["answerType"]));

	} else {

		$answerType = null; 

	}
	
	if (isset($data[0]->{'secret'})) {

		$hashedSecret = $data[0]->{'secret'};
		if ($em->compareHash($secret, $hashedSecret)) {

			$conn->query("UPDATE user SET answer = null, answerType = $answerType WHERE gameId = $gameId");
			echo json_encode(
				array(

					success => true,

				)
			, JSON_PRETTY_PRINT);

		} else {

			echo json_encode(
				array(

					success => false,
					reason => "incorrect secret"

				)
			, JSON_PRETTY_PRINT);
			http_response_code(401);

		}

	} else {

		echo json_encode(
			array(

				success => false,
				reason => "incorrect gameId"

			)
		, JSON_PRETTY_PRINT);
		http_response_code(400);

	}

?>