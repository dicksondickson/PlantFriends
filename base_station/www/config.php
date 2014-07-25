<?php
/*

		Plant Friends Android App data server
		Serves data to the Android app

		Put it in the root of your www dir. IE. /var/www

		This PHP script have two modes:
		Default - Display list of sensor nodes in NodeIndex table
		With node id input  - Dump latest data entry and the average of the past 5 days

		App access specific node data via URL format:

		http://RASPI_ADDRESS/index.php?NodeID=1
		
		
		Edit database information accordingly.


		Dickson Chow
		http://dicksonchow.com

		First Release: July 8, 2014.
		Updated: July 25, 2014.

		MIT License
		http://opensource.org/licenses/mit-license.php

*/

// Database variables
define('DB_HOST', 'localhost'); // Database hostname, default: localhost
define('DB_USER', 'plantuser'); // Database username
define('DB_PASS', 'password'); // Database password
define('DB_NAME', 'plantfriendsdb'); // Database name