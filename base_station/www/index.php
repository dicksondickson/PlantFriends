<?php
//
// Plant Friends Android App data server
// Serves data to the Android app
//
// Put it in the root of your www dir. IE. /var/www
//
// This PHP script have two modes:
// Default - Display list of sensor nodes in NodeIndex table
// With node id input  - Dump latest data entry and the average of the past 5 days
//
// App access specific node data via URL format:
//
//	http://RASPI_ADDRESS/index.php?NodeID=1
//
//
// Dickson Chow
// http://dicksonchow.com
//
// First Release: July 8, 2014.
// Updated: June 28, 2014.
//
// MIT License
// http://opensource.org/licenses/mit-license.php
//



// Declare database variables. Change accordingly
$hostdb = "localhost";  // database host name (should be localhost)
$userdb = "plantuser"; // database username. change accordingly
$passdb = "password"; // database password. change accordingly
$db = "plantfriendsdb"; // database name. change accordingly


	
// function for mysql_query
function execute_query($query) {
    $connect3 = mysql_query($query);
    if (!$connect3) {
        echo "Cannot execute query: $query\n";
        trigger_error(mysql_error()); 
    } 
}

	
// connect to database
$connect1 = mysql_connect($hostdb, $userdb, $passdb);
if (!$connect1) {
	echo "Could not connect to server\n";
	trigger_error(mysql_error(), E_USER_ERROR);
	}

	
// select database
$connect2 = mysql_select_db($db);
if (!$connect2) {
	echo "Cannot select database\n";
	trigger_error(mysql_error(), E_USER_ERROR); 
	}

	


// Get parameters from input. Determine display mode.
if (!isset($_GET['NodeID'])) { 
	$_GET['NodeID'] = "undefined";
	$displayMode = 0; //default is to load the NodeIndex
	} else {
		$NodeID = $_GET["NodeID"];
		$displayMode = 1;
	}	




// Show all nodes in NodeIndex table 
if ( $displayMode == 0 ) {
	// Load NodeIndex from the database	
	$query = "SELECT * FROM NodeIndex";
	$queryx = mysql_query($query);
	while(($NodeIndex[] = mysql_fetch_assoc($queryx)) || array_pop($NodeIndex)); 
	
	// output nodeindex information
	foreach ( $NodeIndex as $x) {
		$xx = $x[NodeID].':'.$x[Alias].':'.$x[Plant].':'.$x[Location].':'.$x[Comments];
		echo "$xx"."\n";
	}
}



// Show Specific node's data.
if ( $displayMode == 1 ) {



	// Match NodeID to NodeIndex. If found, dump sensor data for specific node
	$query = "SELECT NodeID, Alias FROM NodeIndex";
	$queryx = mysql_query($query);
	while(($NodeIndex[] = mysql_fetch_assoc($queryx)) || array_pop($NodeIndex));
	foreach ( $NodeIndex as $x) {
		if ($x[NodeID] == $NodeID) {
			$AliasID = $x[Alias].$x[NodeID];
			$AliasID = preg_replace('/\s+/', '', $AliasID); // stripe whitespaces!
			//echo "$AliasID";
			break;
		}
	}
	

	// Get latest sensor node entry from database. 
	$query = "SELECT DateTime, SoilMoist, TempC, Humid, Voltage FROM "."$AliasID"." WHERE DATE(DateTime) = DATE(DATE_SUB(NOW() , INTERVAL 0 DAY )) ORDER BY id DESC LIMIT 1";
	$queryx = mysql_query($query);
	$DataNow = mysql_fetch_row($queryx);
	while(($DataNow[] = mysql_fetch_row($queryx)) || array_pop($DataNow));
	$DataNow[0] = date("F-jS, Y", strtotime($DataNow[0])); // Clean date format
	// query will return a date of December 31st, 1969 if no data exits. fix it.
	if (strpos($DataNow[0],'1969')){
		$DataNow = [ 0 =>"No Data", 1 => 0, 2 => 0, 3 => 0, 4 => 0];
	}
	//print_r ($DataNow);
	
	
	// Implode the array into a single string with a colon to seperate each datatype
	$DataNow = implode(":", $DataNow);
	echo $DataNow;
	echo "\n";
		
	
	// Get averages for the last five days. loop through each day (ago).
	for ($xday = 1; $xday <= 5; $xday++){
	
		// query the averages for specific day
		$query = "SELECT DateTime, AVG(SoilMoist), AVG(TempC), AVG(Humid), AVG(Voltage) FROM "."$AliasID"." WHERE DATE(DateTime) = DATE(DATE_SUB(NOW() , INTERVAL "."$xday"." DAY ))";
		$queryx = mysql_query($query);
		$DataPast = mysql_fetch_row($queryx);
		while(($DataPast[] = mysql_fetch_row($queryx)) || array_pop($DataPast));
		//print_r ($DataPast);
	
		// Clean our data
		$DataPast[0] = date("F-jS, Y", strtotime($DataPast[0])); // Clean date format
		$DataPast[1] = round ($DataPast[1]); // round soil moisture
		$DataPast[2] = round ($DataPast[2], 2); // round temperature
		$DataPast[3] = round ($DataPast[3]); // round humidity
		$DataPast[4] = round ($DataPast[4], 3); // round voltage
		
		// query will return a date of December 31st, 1969 if no data exits. fix it.
		if (strpos($DataPast[0],'1969')){
		$DataPast = [ 0 =>"No Data", 1 => 0, 2 => 0, 3 => 0, 4 => 0];
		}
		
		// Implode data into single line with colon to seperate the datatypes
		$DataToApp = implode(":", $DataPast);
		echo $DataToApp;
		echo "\n";
	}
}
mysql_close();
?>