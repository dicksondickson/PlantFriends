<?php
/**
 * Handles all Home functions
 * @author Gadelhas
 * @author Dickson Chow
 * @link http://dicksonchow.com/plantfriends2
 * @license http://opensource.org/licenses/MIT MIT License
*/
class Home {
	private $dbconn = null;

	/**
	 * Creates connection to the database
	 * Thanks to: PHP-Login (Panique http://php-login.net)
	*/ 
	private function dbConnection()
    {
        // if connection already exists
        if ($this->dbconn != null) {
            return true;
        } else {
            try {
                // Generate a database connection, using the PDO connector
                $this->dbconn = new PDO('mysql:host='. DB_HOST .';dbname='. DB_NAME . ';charset=utf8', DB_USER, DB_PASS);
                return true;
            } catch (PDOException $e) {
                $this->errors[] = 'Error on database: ' . $e->getMessage();
            }
        }
        // default return
        return false;
    }

    /**
     * Gets an array of all nodes
     * @return array
    */
    public function getAllNodes()
    {
    	if(!$this->dbConnection()) {
    		return false;
    	}
    	$query = $this->dbconn->prepare('SELECT * FROM NodeIndex');
    	$query->execute();
    	return $query->fetchAll();
    }

    /**
     * Get a single node
     * @param integer $nodeid ID of the node
     * @return array
    */
    public function getAliasID($nodeid)
    {
    	if(!$this->dbConnection()) {
    		return false;
    	}
    	$query = $this->dbconn->prepare('SELECT NodeID, Alias FROM NodeIndex WHERE NodeID = :nodeid LIMIT 1');
    	$query->bindValue(':nodeid', $nodeid, PDO::PARAM_INT);
    	$query->execute();
    	$obj = $query->fetchObject();
    	$AliasID = $obj->Alias.$obj->NodeID;
    	return $AliasID;
    }

    /**
     * Get last entry of a node
     * @param string $aliasid the name used to get the table
     * @return array
    */
    public function getLastEntry($aliasid)
    {
    	if(!$this->dbConnection()) {
    		return false;
    	}
    	$query = $this->dbconn->prepare('SELECT DateTime, SoilMoist, TempC, Humid, Voltage FROM '.$aliasid.' WHERE DATE(DateTime) = DATE(DATE_SUB(NOW() , INTERVAL 0 DAY )) ORDER BY id DESC LIMIT 1');
    	$query->execute();
    	$obj = $query->fetch();
    	//var_dump($obj);
    	$obj['DateTime'] = date("F-jS, Y", strtotime($obj['DateTime']));
    	if (strpos($obj['DateTime'],'1970')){
			$obj = [ 'DateTime' => "No Data", 'SoilMoist' => 0, 'TempC' => 0, 'Humid' => 0, 'Voltage' => 0];
		}
    	return $obj['DateTime'].':'.$obj['SoilMoist'].':'.$obj['TempC'].':'.$obj['Humid'].':'.$obj['Voltage'];
    }

    /**
     * Get the average for a number of days
     * @param string $aliasid the name used to get the table
     * @param integer $days number of days to show. Default = 5
     * @return array
    */
    public function getAverageDays($aliasid, $days = 5)
    {
    	if(!$this->dbConnection()) {
    		return false;
    	}
    	$result = '';
    	for ($i=1; $i <= $days; $i++) { 
    		$query = $this->dbconn->prepare('SELECT DateTime, AVG(SoilMoist) as SoilMoist, AVG(TempC) as TempC, AVG(Humid) as Humid, AVG(Voltage) as Voltage FROM '.$aliasid.' WHERE DATE(DateTime) = DATE(DATE_SUB(NOW() , INTERVAL :days DAY ))');
    		$query->bindValue(':days', $i, PDO::PARAM_INT);
    		$query->execute();
    		$obj = $query->fetch();
    		//var_dump($obj);
    		$obj['DateTime'] = date("F-jS, Y", strtotime($obj['DateTime']));
    		if (strpos($obj['DateTime'],'1969')){
				$obj = [ 'DateTime' => "No Data", 'SoilMoist' => 0, 'TempC' => 0, 'Humid' => 0, 'Voltage' => 0];
			}
    		$result .= $obj['DateTime'].":".round($obj['SoilMoist']).":".round($obj['TempC'], 2).":".round($obj['Humid']).":".round($obj['Voltage'], 3)."\n";
			//echo $result;
    	}
    	return $result;
    }
}