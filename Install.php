<?php
/*************************************************

  LightBlog - PHP SQLite blogging platform
  Copyright 2008 soren121 and aldo.
  
  This software is released under the GNU
  General Public License version 3. For more
  accurate licensing information, please see
  the LICENSE.txt file included in this
  distribution.
  
  Install.php
  
*************************************************/

// SQL query extractor class
// Written by Merianos Nikos of phpclasses.com
// Released into the public domain

class queryExtractor {
	private $fileName;	
	private $fileContent;
	public $SqlQueries;
	public function __construct($fileLocation = '') {
		if(strlen($fileLocation) < 1) {
			$this->fileName = '';
		}
		
		else {
			$this->fileName = $fileLocation;
		}
	}

	public function extractQueries() {
		// set defaults
		$fileSize = 0;
		$query = '';
		
		// return nothing if SQL file is empty
		if(strlen($this->fileName) < 1) {
			return '';
		}
		
		else {
			// open file for reading
    		$file = @fopen("$this->fileName", "r");
			// read 32K of file
    		$rf = fread($file, 32000);
			// close file
    		fclose($file);

    		if(strlen($rf) > 0) {
    			$fileSize = strlen($rf);
    			
    			for($i = 0; $i <= $fileSize; $i++) {
    				if(substr($rf, $i, 1) == ';') {
    					$this->SqlQueries[] = $query . substr($rf, $i, 1);
    				}
					
    				else {
    					$query .= substr($rf, $i, 1);
    				}
    			}
    		}
			
    		else {
    			$this->SqlQueries = '';
    			return $this->SqlQueries;
    		}
		}
	}
}

/**
 * EXAMPLE
 * 
 * $qe = new queryExtractor("C:\Program Files\Apache Software Foundation\Apache2.2\htdocs\Tserkanos\classes\sqlFileReader\DatabaseStructure.sql");
 * $qe->extractQueries();
 * 
 * foreach ($qe->SqlQueries as $value)
 * {
 * 	echo($value . "<br /><br />");
 * }
 */

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <title>LightBlog 1.0 Installer</title>
  <style type="text/css">
  
  </style>
</head>
<body>

</body>
</html>