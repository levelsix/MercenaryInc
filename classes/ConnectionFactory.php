<?php

include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/Utils.php");

class ConnectionFactory {
	
	/*
	 * http://www.karlrixon.co.uk/articles/sql/update-multiple-rows-with-different-values-and-a-single-sql-query/
	* http://dev.mysql.com/doc/refman/5.0/en/insert-on-duplicate.html
	* */
	
	private static $factory;
	
	function __construct() {
	}
	
	public static function getFactory() {
		if (!self::$factory)
			self::$factory = new ConnectionFactory();
		return self::$factory;
	}
	
	private $db;
	
	//TODO: make this private after refactoring, should only be called in this class
	//should be private static?
	public function getConnection() {
		if (!$this->db) {
			try {
				include $_SERVER['DOCUMENT_ROOT'] . '/properties/dbproperties.php';
				$this->db = new PDO("mysql:host=$server;dbname=$database", $user, $password);
				$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			} catch (PDOException $e) {
				// Redirect to 'server down' page
				include $_SERVER['DOCUMENT_ROOT'] . '/properties/serverproperties.php';
				header("Location: " . $serverRoot . "/errorpage.html");
				exit;
			}
		}
		return $this->db;
	}
	
	public static function SelectValue($retrieveValue, $tablename, $conditions) {
		$mydb = self::getFactory()->getConnection();
		
		$values = array();
		
		$condclauses = array();
		foreach($conditions as $key=>$value) {
			$condclauses[] = $key."=?";
			$values[] = $value;
		}
		
		$stmtString = "SELECT ". $retrieveValue . " FROM " . $tablename . " WHERE ";
		$stmtString .= getArrayInString($condclauses, 'and');
		
		$stmt = $mydb->prepare($stmtString);
		$stmt->execute($values);
		
		return $stmt->fetchColumn();		
	}
	
	public static function SelectRowAsClass($query, $values, $className) {
		$mydb = self::getFactory()->getConnection();
		$sth = $mydb->prepare($query);
		$sth->execute($values);
				
		$sth->setFetchMode(PDO::FETCH_CLASS, $className);
		$obj = $sth->fetch();
		return $obj;
	}
	
	public static function SelectRowsAsClasses($query, $values, $className) {
		$mydb = self::getFactory()->getConnection();
		$sth = $mydb->prepare($query);
		$sth->execute($values);
		
		$sth->setFetchMode(PDO::FETCH_CLASS, $className);
		$objs = $sth->fetchAll();
		return $objs;
	}
	
	
	/*trying sth new
	public static function SelectRowsAsClasses($tablename, $desiredFields, $conditions) {
		$values = array();
		$condclauses = array();
		foreach($conditions as $key=>$value) {
			$condclauses[] = $key."=?";
			$values[] = $value;
		}
		$query = createSelectQuery($tablename, $desiredFields, $condclauses, $values);
		
		echo $query;
		
		$mydb = self::getFactory()->getConnection();
		$sth = $mydb->prepare($query);
		$sth->execute($values);
		
		$sth->setFetchMode(PDO::FETCH_CLASS, $className);
		$objs = $sth->fetchAll();
		return $objs;
	}
	
	private function createSelectQuery($tablename, $desiredFields, $condclauses, $values) {
		$query = "SELECT ";
		if ($params) {
			$query .= getArrayInString($desiredFields, ',');
		} else {
			$query .= "* ";
		}
		$query .= "from " . $tablename . " where ";
		$query .= getArrayInString($condclauses, " OR ");
		return $query;
	}
	*/
	
	public static function SelectAsStatementHandler($query, $values) {
		$mydb = self::getFactory()->getConnection();
		$sth = $mydb->prepare($query);
		$sth->execute($values);
		
		$sth->setFetchMode(PDO::FETCH_ASSOC);
		
		return $sth;
	}
	
	public static function SelectRowAsAssociativeArray($query, $values) {
		$sth = ConnectionFactory::SelectAsStatementHandler($query, $values);
		return $sth->fetch();
	}
	
	/*
	* $params should be an associative array from columns to values
	* $conditions same
	*/
	public static function updateTableRowRelativeBasic($tablename, $params, $conditions) {
		$mydb = self::getFactory()->getConnection();
		//TODO: after refactor, just eliminate getFactory, change getConnection to static, and call that?
	
		$values = array();
	
		$setclauses = array();
		foreach($params as $key=>$value) {
			$setclauses[] = $key . "=" . $key . "+?";
			$values[] = $value;
		}
	
		$condclauses = array();
		foreach($conditions as $key=>$value) {
			$condclauses[] = $key."=?";
			$values[] = $value;
		}
	
		$stmtString = "UPDATE ". $tablename . " SET ";
		$stmtString .= getArrayInString($setclauses, ',') . " WHERE ";
		$stmtString .= getArrayInString($condclauses, 'and');
	
		$stmt = $mydb->prepare($stmtString);
	
		return $stmt->execute($values);
	}
	
	
	/*
	* $params should be an associative array from columns to values
	* $conditions same
	*/
	public static function updateTableRowAbsoluteBasic($tablename, $params, $conditions) {
		$mydb = self::getFactory()->getConnection();
		//TODO: after refactor, just eliminate getFactory, change getConnection to static, and call that?
		
		$values = array();
		
		$setclauses = array();
		foreach($params as $key=>$value) {
			$setclauses[] = $key . "=?";
			$values[] = $value;
		}
		
		$condclauses = array();
		foreach($conditions as $key=>$value) {
			$condclauses[] = $key."=?";
			$values[] = $value;
		}
		
		$stmtString = "UPDATE ". $tablename . " SET ";
		$stmtString .= getArrayInString($setclauses, ',') . " WHERE ";
		$stmtString .= getArrayInString($condclauses, 'and');
		
		$stmt = $mydb->prepare($stmtString);

		return $stmt->execute($values);
	}
	
	/*
	 * Update a row in a table with both absolute and relative values
	 * i.e. combines the functionality of updateTableRowRelative and updateTableRowAbsolute
	 */
	public static function updateTableRowGenericBasic($tablename, $absParams, $relParams, $conditions) {
		$mydb = self::getFactory()->getConnection();
		//TODO: after refactor, just eliminate getFactory, change getConnection to static, and call that?
		
		$values = array();
		
		$absSetClauses = array();
		foreach($absParams as $key=>$value) {
			$absSetClauses[] = $key . "=?";
			$values[] = $value;
		}
		
		$relSetClauses = array();
		foreach($relParams as $key=>$value) {
			$relSetClauses[] = $key . "=" . $key . "+?";
			$values[] = $value;
		}
		
		$condclauses = array();
		foreach($conditions as $key=>$value) {
			$condclauses[] = $key."=?";
			$values[] = $value;
		}
		
		$stmtString = "UPDATE ". $tablename . " SET ";
		$stmtString .= getArrayInString($absSetClauses, ',') . ", " . getArrayInString($relSetClauses, ',');
		$stmtString .= " WHERE ";
		$stmtString .= getArrayInString($condclauses, 'and');
		
		$stmt = $mydb->prepare($stmtString);
		
		return $stmt->execute($values);
	}
	
	/* 
	 * $params should be an associative array from columns to values
	 * used for basic inserts
	 * returns success or failure
	 */
	public static function InsertIntoTableBasic($tablename, $params) {
		$mydb = self::getFactory()->getConnection();
		//TODO: after refactor, just eliminate getFactory, change getConnection to static, and call that?		 
		
		$questions = array();
		$keys = array();
		$values = array();
		foreach($params as $key=>$value) {
			$keys[] = $key;
			$values[] = $value;
			$questions[] = '?';
		}
				
		$stmtString = "INSERT INTO ". $tablename . "(";
		$stmtString .= getArrayInString($keys, ',') . ") VALUES (";
		$stmtString .= getArrayInString($questions, ',') . ")";
		
		
		$stmt = $mydb->prepare($stmtString);
				
		return $stmt->execute($values);
	}
	
	/*
	* $params should be an associative array from columns to values
	* used for basic inserts
	* returns success or failure
	*/
	public static function InsertIgnoreIntoTableBasic($tablename, $params) {
		$mydb = self::getFactory()->getConnection();
		//TODO: after refactor, just eliminate getFactory, change getConnection to static, and call that?
		
		$questions = array();
		$keys = array();
		$values = array();
		foreach($params as $key=>$value) {
			$keys[] = $key;
			$values[] = $value;
			$questions[] = '?';
		}
		
		$stmtString = "INSERT IGNORE INTO ". $tablename . "(";
		$stmtString .= getArrayInString($keys, ',') . ") VALUES (";
		$stmtString .= getArrayInString($questions, ',') . ")";
		
		
		$stmt = $mydb->prepare($stmtString);
						
		return $stmt->execute($values);
	}
	
	public static function InsertIntoTableBasicReturnInsertID($tablename, $params) {
		$mydb = self::getFactory()->getConnection();
		//TODO: after refactor, just eliminate getFactory, change getConnection to static, and call that?
	
		$questions = array();
		$keys = array();
		$values = array();
		foreach($params as $key=>$value) {
			$keys[] = $key;
			$values[] = $value;
			$questions[] = '?';
		}
	
		$stmtString = "INSERT INTO ". $tablename . "(";
		$stmtString .= getArrayInString($keys, ',') . ") VALUES (";
		$stmtString .= getArrayInString($questions, ',') . ")";
	
	
		$stmt = $mydb->prepare($stmtString);
	
		$success = $stmt->execute($values);
		if ($success) {
			return $mydb->lastInsertId(); 
		}
		return 0;
	}
	
	public static function InsertOnDuplicateKeyUpdate($tablename, $params, $columnUpdate, $updateQuantity) {
		$mydb = self::getFactory()->getConnection();
		
		$questions = array();
		$keys = array();
		$values = array();
		foreach($params as $key=>$value) {
			$keys[] = $key;
			$values[] = $value;
			$questions[] = '?';
		}
		$values[] = $updateQuantity;
		
		$stmtString = "INSERT INTO ". $tablename . "(";
		$stmtString .= getArrayInString($keys, ',') . ") VALUES (";
		$stmtString .= getArrayInString($questions, ',') . ") ";
		$stmtString .= "ON DUPLICATE KEY UPDATE ";
		$stmtString .= $columnUpdate."=".$columnUpdate."+?";
		
		$stmt = $mydb->prepare($stmtString);
		
		return $stmt->execute($values);
	}
	
	public static function DeleteZeroAndBelowQuantity($tablename) {
		$mydb = self::getFactory()->getConnection();
		//TODO: after refactor, just eliminate getFactory, change getConnection to static, and call that?
		
		$stmtString = "DELETE FROM ". $tablename . " WHERE quantity<=?";		
		
		$stmt = $mydb->prepare($stmtString);
		
		return $stmt->execute(array(0));
	}
	
}
?>