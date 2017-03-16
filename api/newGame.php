<?php

	header('Content-Type: application/json');
	require "DAO.php";
	require "Methods.php";
	require "encryption_management.php";
	$conn = new DAO(); 
	$m = new Methods(); 
	$em = new EncryptionManager();

	$iterations = 0; 
	$result = false; 
	$chars = array_merge(range('A','Z'), range('a','z'), range('0','9'));
	$secret = $m->getSalt(255,$chars);
	$secretHashed = $em->hashString($secret);
	$chars = array_merge(range('A','Z'), range('0','9'));

	while (!$result && $iterations <= 10) {

		$iterations++; 
		$code = $m->getSalt(4, $chars);
		$result = $conn->update("INSERT INTO game(lastConnected, firstConnected, secret, code) VALUES (NOW(), NOW(), \"$secretHashed\", \"$code\")");

	}

	if ($result == true) {

		echo json_encode(
			array(

				success => true,
				iterations => $iterations,	
				gameId => $conn->getLastId(),
				code => $code,
				secret => $secret


			)
		, JSON_PRETTY_PRINT);

	} else {

		echo json_encode(
			array(

				success => false,
				reason => "timed out trying to create the game"

			)
		, JSON_PRETTY_PRINT);

		http_response_code(408);

	}

?>