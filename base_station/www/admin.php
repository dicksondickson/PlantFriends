<br>
<p>
<h1>Plant Friends</h1>
<br>
<h3>CRAZY ROUGH INTERFACE FOR ADDING AND EDITING SENSOR NODES.</h3>
</p>
<br>
<?php
//
// Plant Friends Admin Interface
// Insert and delete sensor nodes in the NodeIndex table
// Extremely alpha, use with care.
//
// Put it in your /var/www/admin dir.
//
// Dickson Chow
// http://dicksonchow.com
//
// First Release: June 24, 2014.
// Updated: June 26, 2014.
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


// Modify the database according to what options are activated
// If post is not empty...
if (!empty($_POST)) {


// Get all variables from "post" data and update data accordingly
$nodeidx = $_POST['nodeid'];
$nodeid = (int)$nodeidx; // convert to integer
$nodeidtemp = (int)$_POST['nodeidtemp']; // enable changing NodeID simultanously with other variables.
$alias = $_POST['alias'];
$aliastemp = $_POST['aliastemp'];
$location = $_POST['location'];
$plant = $_POST['plant'];
$comments = $_POST['comments'];
$AliasIDtemp = $aliastemp.(string)$nodeidtemp; // create names for table associated with node
$AliasIDtemp = preg_replace('/\s+/', '', $AliasIDtemp); // stripe whitespaces!
$AliasID = $alias.(string)$nodeid;
$AliasID = preg_replace('/\s+/', '', $AliasID); // stripe whitespaces!


// Prevent letters from being entered as NodeID. If encounterd, will increment after highest NodeID.
if ($nodeid <= 0) {
	$queryx = "SELECT MAX(NodeID) FROM NodeIndex";
	$resultx = mysql_query($queryx);
	$resulty = mysql_fetch_row($resultx);
	$nodeid = $resulty[0];
	$nodeid = $nodeid + 1;
	$nodeidtemp = $nodeid;
	$AliasIDtemp = $aliastemp.(string)$nodeidtemp; // create names for table associated with node
	$AliasIDtemp = preg_replace('/\s+/', '', $AliasIDtemp); // stripe whitespaces!
	$AliasID = $alias.(string)$nodeid;
	$AliasID = preg_replace('/\s+/', '', $AliasID); // stripe whitespaces!
	
	}


// If "addnode" is not empty (addnode button pressed), add new node and a respective table for the node
if (!empty($_POST["addnode"])) {
	
	//Add node to NodeIndex
	$query = "INSERT INTO NodeIndex VALUES('$nodeid','$alias','$location','$plant','$comments')";
	execute_query($query);
	
	
	//Create table for new node
	$query = "CREATE TABLE "."$AliasID"."(id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, DateTime TIMESTAMP, ErrorLvl INT, SoilMoist INT, TempC INT, Humid INT, Voltage FLOAT)";
	execute_query($query);
	
	// Insert dummy data to new table
	$query = "INSERT INTO "."$AliasID"."(DateTime, ErrorLvl, SoilMoist, TempC, Humid, Voltage) VALUES (CURRENT_TIMESTAMP,'0','0','0','0','0')";
	execute_query($query);
	
	
	
}


// If "updatenode" is not empty (update button pressed), update node information. Renames table for the node if exists
if (!empty($_POST["updatenode"])) {
		$query = "UPDATE NodeIndex SET Alias = '$alias' , Location = '$location' , Plant = '$plant' , Comments = '$comments' WHERE NodeID = '$nodeidtemp'"; // We update other fields first before updating NodeID or else it won't work
		execute_query($query);
		$query = "UPDATE NodeIndex SET NodeID = '$nodeid' WHERE Alias = '$alias'"; // Now we update the NodeID
		execute_query($query);
		if(mysql_num_rows(mysql_query("SHOW TABLES LIKE '$AliasIDtemp'"))==1) {
			$query = "RENAME TABLE $AliasIDtemp TO $AliasID"; // rename table
			execute_query($query);
		}
	}



// If "deletenode" is not empty (delete button pressed), delete respective node from table. If a table for the node exits, delete too.
if (!empty($_POST["deletenode"])) {
	$query = "DELETE FROM NodeIndex WHERE NodeID = '$nodeidtemp'";
	execute_query($query);
	if(mysql_num_rows(mysql_query("SHOW TABLES LIKE '$AliasIDtemp'"))==1) {
		$query = "DROP TABLE $AliasIDtemp"; // drop table
		execute_query($query);
		}
	}
	
	
}


// Display a list of all the nodes in NodeIndex table
// Select NodeIndex table
$query = "SELECT * FROM NodeIndex";
$nodearray = mysql_query($query);
while ($row = mysql_fetch_assoc($nodearray)) {

	// This is responsible for displaying the editable textboxes with node information inside.
	// Wanted everything in one file and not have to do a include.
	//include 'row.htm';
	$nodedisplay = '<form method="POST" action="index.php">
	<input type="hidden" name="nodeidtemp" value="'."{$row['NodeID']}".'"></input>
	Node ID:<input type="text" name="nodeid" value="'."{$row['NodeID']}".'"></input>
	Alias:<input type="text" name="alias" value="'."{$row['Alias']}".'"></input>
	<input type="hidden" name="aliastemp" value="'."{$row['Alias']}".'"></input>
	Location:<input type="text" name="location" value="'."{$row['Location']}".'"></input>
	Plant:<input type="text" name="plant" value="'."{$row['Plant']}".'"></input>Comments:<textarea style="vertical-align:bottom;" type="text" name="comments" cols="18" rows="2" >'."{$row['Comments']}".'</textarea><br>
	<input type="submit" name="updatenode" value="Update"></input>
	<input type="submit" name="deletenode" value="Delete"></input>
	</form><br>';
	
	echo "$nodedisplay";
}


mysql_close();
?>
<br>
<p>
ADD NEW SENSOR NODE
<form method="POST" action="index.php">
NodeID <input type="text" name="nodeid"></input><br/>
Alias <input type="text" name="alias"></input><br/>
Location <input type="text" name="location"></input><br/>
Plant <input type="text" name="plant"></input><br/>
Comment <input type="text" name="comments"></input><br/>
<input type="submit" name="addnode" value="Add Sensor Node"></input>
</form>
</p>