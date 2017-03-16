<?php

	class DAO {

		private $conn;
		private $lastId; 

		public function __construct(){

			$server = "localhost";
			$user = "";
			$password = "";
			$database = ""; 

			error_reporting(0);
			$this->conn = new mysqli($server, $user, $password, $database);

			if ($this->conn->connect_error) {
    			
    			echo json_encode(
    				array(

    					success => false,
    					reason => "Failed to connect to database".$this->conn->connect_error

    				)
    			, JSON_PRETTY_PRINT);
    			http_response_code(424);
    			die(); 

			} 

			error_reporting(1);

		}

		public function update($sql){

			if ($this->conn->query($sql) === TRUE) {

				return true; 

			} else {

				return false; 

			}

		}

		public function query($sql) {

			$result = $this->conn->query($sql);
			$jsonData = array();

			if ($result->num_rows > 0) {
		    	
		    	while($row = $result->fetch_assoc()) {

		    		$jsonData[] = $row; 

		    	}

		    }

		    return json_decode(json_encode($jsonData));

		}

		public function getLastId() {

			return $this->conn->insert_id;

		}

		public function printError() {	

			echo $this->conn->error;

		}

		public function getError() {	

			echo $this->conn->error;

		}

	}

?>