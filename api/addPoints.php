<?php

	header('Content-Type: application/json');
	require "DAO.php";
	require "Methods.php";
	require "encryption_management.php";
	$conn = new DAO(); 
	$m = new Methods(); 
	$em = new EncryptionManager();

	if (!isset($_GET["gameId"]) && !isset($_GET["secret"]) && !isset($_GET["username"]) && !isset($_GET["points"])) {

		echo json_encode(
			array(

				success => false,
				reason => "not enough parameters. Expected username, gameId, secret and points"

			)
		, JSON_PRETTY_PRINT);
		http_response_code(422);
		die(); 

	}

	$gameId = htmlspecialchars($_GET["gameId"]);
	$username = htmlspecialchars($_GET["username"]);
	$secret = $_GET["secret"];
	$points = htmlspecialchars($_GET["points"]);
	$data = $conn->query("SELECT secret FROM game WHERE gameId = $gameId");
	
	if (isset($data[0]->{'secret'})) {

		$hashedSecret = $data[0]->{'secret'};
		if ($em->compareHash($secret, $hashedSecret)) {

			$result = $conn->update("UPDATE user SET score = score + $points where gameId = $gameId AND username = \"$username\"");

			if (!$result) {

				echo json_encode(
					array(

						success => false,
						reason => "invalid parameters"

					)
				, JSON_PRETTY_PRINT);
				http_response_code(400);
				die(); 

			}

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