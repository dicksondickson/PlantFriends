<?php
/**
 * Handles all admin functions
 * @author Gadelhas
 * @author Dickson Chow
 * @link http://dicksonchow.com/plantfriends2
 * @license http://opensource.org/licenses/MIT MIT License
*/
class Admin {
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
	 * Adds a node to database
	 * @param integer $nodeid ID of the node
	 * @param string $alias Name of the node
	 * @param string $loc Location of the node
	 * @param string $plant Plant that the node is watching
	 * @param string $comment Any comment that the user wants to add
	 * @return bool (string on error)
	*/
    public function addNode($nodeid, $alias, $loc, $plant, $comment)
    {
    	// Check if connection to database exists.
    	if(!$this->dbConnection()) {
    		return false;
    	} // else
    	if(is_numeric($nodeid) && $nodeid > 0) {
    		$alias = trim($alias);
    		$loc = trim($loc);
    		$plant = trim($plant);
    		$comment = trim($comment);
    		$aliasID = $alias.$nodeid;
    		// Insert onto NodeIndex
	    	$query = $this->dbconn->prepare('INSERT INTO NodeIndex VALUES(:nodeid, :alias, :loc, :plant, :comment)');
	    	$query->bindValue(':nodeid', $nodeid, PDO::PARAM_INT);
	    	$query->bindValue(':alias', $alias, PDO::PARAM_STR);
	    	$query->bindValue(':loc', $loc, PDO::PARAM_STR);
	    	$query->bindValue(':plant', $plant, PDO::PARAM_STR);
	    	$query->bindValue(':comment', $comment, PDO::PARAM_STR);
	    	$query->execute();
	    	// Create table
	    	$query = $this->dbconn->prepare("CREATE TABLE $aliasID (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, DateTime TIMESTAMP, ErrorLvl INT, SoilMoist INT, TempC INT, Humid INT, Voltage FLOAT)");
	    	$query->execute();
	    	// Populate new table with data
	    	$query = $this->dbconn->prepare("INSERT INTO :aliasid (DateTime, ErrorLvl, SoilMoist, TempC, Humid, Voltage) VALUES (CURRENT_TIMESTAMP,'0','0','0','0','0')");
	    	$query->bindValue(':aliasid', $aliasID, PDO::PARAM_INT);
	    	$query->execute();
	    	return true;
    	} else {
    		return 'Node id needs to be numeric and bigger than 0 (zero)';
    	}
    }

    /**
	 * Updates a node on the database
	 * NOTE: No need to update NodeID (?)
	 * @param integer $nodeid ID of the node
	 * @param string $alias Name of the node
	 * @param string $loc Location of the node
	 * @param string $plant Plant that the node is watching
	 * @param string $comment Any comment that the user wants to add
	 * @return bool
	*/
    public function updateNode($nodeid, $alias, $loc, $plant, $comment)
    {
    	if(!$this->dbConnection()) {
    		return false;
    	}
        $query = $this->dbconn->prepare('SELECT Alias FROM NodeIndex WHERE NodeID = :nodeid');
        $query->bindValue(':nodeid', $nodeid, PDO::PARAM_INT);
        $query->execute();
        $oldAlias = $query->fetch();

    	$query = $this->dbconn->prepare('UPDATE NodeIndex SET Alias = :alias, Location = :loc, Plant = :plant, Comments = :comment WHERE NodeID = :nodeid');
    	$query->bindValue(':nodeid', $nodeid, PDO::PARAM_INT);
    	$query->bindValue(':alias', $alias, PDO::PARAM_STR);
    	$query->bindValue(':loc', $loc, PDO::PARAM_STR);
    	$query->bindValue(':plant', $plant, PDO::PARAM_STR);
    	$query->bindValue(':comment', $comment, PDO::PARAM_STR);
    	$query->execute();
    	// check if Alias was changed.
    	$aliasid = $alias.$nodeid;
    	$oldAliasid = $oldAlias[0].$nodeid;
        if($oldAliasid != $aliasid) {
    		$query = $this->dbconn->prepare('RENAME TABLE '.$oldAliasid.' TO '.$aliasid);
    		$query->execute();
    	}
    	return true;
    }

    /**
	 * Deletes a node from the database
	 * @param integer $nodeid ID of the node
	 * @param string $alias Name of the node
	 * @return bool
	*/
    public function deleteNode($nodeid, $alias)
    {
    	if(!$this->dbConnection()) {
    		return false;
    	}
    	$query = $this->dbconn->prepare('DELETE FROM NodeIndex WHERE NodeID = :nodeid');
    	$query->bindValue(':nodeid', $nodeid, PDO::PARAM_INT);
    	$query->execute();
        $aliasid = $alias.$nodeid;
    	$query = $this->dbconn->prepare('DROP TABLE '.$aliasid);
    	$query->execute();
    	return true;
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
     * Get only a node
     * @return array
    */
    public function getNode($nodeid)
    {
    	if(!$this->dbConnection()) {
    		return false;
    	}
    	$query = $this->dbconn->prepare('SELECT * FROM NodeIndex WHERE NodeID = :nodeid LIMIT 1');
    	$query->bindValue(':nodeid', $nodeid, PDO::PARAM_INT);
    	$query->execute();
    	return $query->fetchObject();
    }
}