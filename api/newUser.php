<?php 

	header('Content-Type: application/json');
	require "DAO.php";
	require "Methods.php";
	require "encryption_management.php";
	$conn = new DAO(); 
	$m = new Methods(); 
	$em = new EncryptionManager();

	header("Access-Control-Allow-Origin: http://localhost:8001", false);

	if ($_SERVER["REQUEST_METHOD"] != "POST") {

		echo json_encode(
			array(

				success => false,
				reason => "invalid method. Expected POST"

			)
		, JSON_PRETTY_PRINT);
		http_response_code(405);
		die(); 

	}

	if (!isset($_POST["username"]) && !isset($_POST["code"])) {

		echo json_encode(
			array(

				success => false,
				reason => "not enough parameters. Expected username and code"

			)
		, JSON_PRETTY_PRINT);
		http_response_code(422);
		die(); 

	}

	$name = htmlspecialchars($_POST["username"]);
	$code = strtoupper(htmlspecialchars($_POST["code"]));

	$conn = new DAO(); 

	$data = $conn->query("SELECT gameId FROM game WHERE code = \"$code\"");
	if (isset($data[0]->{'gameId'})) {

		$gameId = $data[0]->{'gameId'};
		$chars = array_merge(range('A','Z'), range('a','z'), range('0','9'));
		$sessionId = $m->getSalt(255,$chars);
		$sessionIdHashed = $em->hashString($sessionId);
		$result = $conn->update("INSERT INTO user (gameId, username, score, sessionId, answer) VALUES ($gameId,\"$name\", 0, \"$sessionIdHashed\",\"\")");

		if (!$result) {

			echo json_encode(
				array(

					success => false,
					reason => "username already eists"

				)
			, JSON_PRETTY_PRINT);
			http_response_code(400);
			die(); 

		}

		echo json_encode(
			array(

				success => true,
				userId => $conn->getLastId(),
				sessionId => $sessionId

			)
		, JSON_PRETTY_PRINT);

	} else {

		echo json_encode(
			array(

				success => false,
				reason => "incorrect game code"

			)
		, JSON_PRETTY_PRINT);
		http_response_code(400);

	}

?>