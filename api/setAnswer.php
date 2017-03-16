<?php

	header('Content-Type: application/json');
	require "DAO.php";
	require "Methods.php";
	require "encryption_management.php";
	$conn = new DAO(); 
	$m = new Methods(); 
	$em = new EncryptionManager();

	if (!((isset($_POST["username"]) && isset($_POST["sessionId"]) && isset($_POST["gameId"])) || (isset($_COOKIE["username"]) && isset($_COOKIE["sessionId"]) && isset($_COOKIE["gameId"]))) || !isset($_POST["answer"])) {

		echo json_encode(
			array(

				success => false,
				reason => "Not enough parameters. Expected username, gameId and sessionId through cookies or the POST method. Answer must be submitted through the POST method"

			)
		, JSON_PRETTY_PRINT);
		http_response_code(422);
		die(); 

	}

	if (isset($_POST["username"]) && isset($_POST["sessionId"]) && isset($_POST["gameId"])) {

		$username = $_POST["username"];
		$sessionId = $_POST["sessionId"];
		$gameId = $_POST["gameId"];

	} else {

		$username = $_COOKIE["username"];
		$sessionId = $_COOKIE["sessionId"];
		$gameId = $_COOKIE["gameId"];

	}

	$answer = $_POST["answer"];

	$data = $conn->query("SELECT sessionId FROM user WHERE username = \"$username\" AND gameId = $gameId");
	
	if (isset($data[0]->{'sessionId'})) {

		$hashedId = $data[0]->{'sessionId'};

		if ($em->compareHash($sessionId, $hashedId)) {

			$result = $conn->update("UPDATE user SET answer = \"$answer\" where username = \"$username\" AND gameId = $gameId AND answer = null");

			if (!$result) {

				echo json_encode(
					array(

						success => false,
						reason => "invalid parameters or answer already set"

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
					reason => "incorrect sessionId"

				)
			, JSON_PRETTY_PRINT);
			http_response_code(401);

		}

	} else {

		echo json_encode(
			array(

				success => false,
				reason => "incorrect username or gameId"

			)
		, JSON_PRETTY_PRINT);
		http_response_code(400);

	}

?>