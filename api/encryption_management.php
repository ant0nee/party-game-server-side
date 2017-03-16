<?php 
	
	include_once "Methods.php";
	class EncryptionManager {

		/*
			Description: A class that holds all the methods to encrypt, decrypt and hash data
			Author: ant0nee
		*/

		private $m;
		public function __construct(){

			$this->m = new Methods(); 

		}

		public function hashString($input, $rounds = 9) {

			/*
				Description: Hashes and salts a string
				Author: ant0nee
				input: 
					$input - Required: The string to hash (String)
					$rounds - Optional: The number of iterations the algorithm goes through. (int) Default: 9 
				return: The hashed string (String)
			*/

		    $saltchars = array_merge(range('A','Z'), range('a','z'), range('0','9')); 
		    $salt = $this->m->getSalt(22,$saltchars);
		    return crypt($input, sprintf("$2y$%02d$",$rounds) . $salt);

	  	}

		public function compareHash($inputstring, $hashedstring) {

			/*
				Description: Compares plain text to a hashed string using the hashString($input, $rounds = 9) method. Returns true if they match.
				Author: ant0nee
				input: 
					$inputstring - Required: The plaintext (String)
					$hashedstring - Required: The hashed string (String)
				return: True if they match (Boolean)
			*/

		  return crypt($inputstring, $hashedstring) == $hashedstring;

		}
	
	}

?>
