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


	Dickson Chow
	http://dicksonchow.com
	

	First Release: July 8, 2014.
	Updated: July 25, 2014.

	MIT License
	http://opensource.org/licenses/mit-license.php

*/

require('main.php');
$result = '';

// Get parameters from input. Determine display mode.
if (!isset($_GET['NodeID'])) { 
	$nodes = $home->getAllNodes();
	foreach ($nodes as $n) {
		$result .= $n['NodeID'].":".$n['Alias'].":".$n['Plant'].":".$n['Location'].":".$n['Comments'];
		$result .= "\n";
	}
} else {
	$NodeID = $_GET['NodeID'];
	$alias = $home->getAliasID($NodeID);
	$result .= $home->getLastEntry($alias);
	$result .= "\n";
	$result .= $home->getAverageDays($alias, 5);	
}	
?>
<?php echo $result; ?>
