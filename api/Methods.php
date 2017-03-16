<?php

	class Methods {

		public function __construct(){
		}

		function getSalt($size, $characters){

			$salt = "";
			$saltchars = $characters; 

		    for ($i = 0; $i < $size; $i++) {

		      $salt .= $saltchars[array_rand($saltchars)];

		    }

		    return $salt; 

		}

	}

?>