<?php session_start();

/*********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	Sources/MathValidator.php
	
	©2009 soren121. All rights reserved.
	Released under the GNU General
	Public License. For all licensing
	information, please see the
	LICENSE.txt document included in this
	distribution.
	
	Based on MathGuard v2 by Matej Koval.
	The original code can be found here:
	www.codegravity.com/projects/mathguard

*********************************************/

class MathValidator {	
	# Hashes user answer, hour, and a prime number
	function hashEncode($input, $prime) {
		return md5($input.date("H").$prime);
	}

	# Calls encode and returns hash
	function generateCode($a, $b, $prime) {
		return MathValidator :: hashEncode($a + $b, $prime);
	}

	# Check result of form
	function checkResult($mathvalidator_answer, $mathvalidator_code, $prime = 37) {
		$result_encoded = MathValidator :: hashEncode($mathvalidator_answer, $prime);		
		if ($result_encoded == $mathvalidator_code)
			return true;
		else
			return false;
	}

	# Echo or return question into form
	function insertQuestion($output = 'e', $prime = 37) {
		$a = rand() % 10;
		$b = rand() % 10;
		if($output == 'e') { echo $a." + ".$b; }
		elseif($output == 'r') { return $a." + ".$b; }
		$_SESSION['mathvalidator_c'] = MathValidator :: generateCode($a, $b, $prime);
	}
}

?>