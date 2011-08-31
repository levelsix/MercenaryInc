<?php
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
	
	public static function SelectRowAsClass($query, $values, $className) {
		$mydb = self::getFactory()->getConnection();
		$sth = $mydb->prepare($query);
		$sth->execute($values);
		
		$sth->setFetchMode(PDO::FETCH_CLASS, $className);
		$obj = $sth->fetch();
		return $obj;
	}
			
	private static function selectFromTable($tablename, $columns, $conditions) {
		
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
		$stmtString .= self::getArrayInString($setclauses, ',') . " WHERE ";
		$stmtString .= self::getArrayInString($condclauses, 'and');
		
		$stmt = $mydb->prepare($stmtString);

		return $stmt->execute($values);
	}
	
	/* 
	 * $params should be an associative array from columns to values
	 * used for basic inserts
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
		$stmtString .= self::getArrayInString($keys, ',') . ") VALUES (";
		$stmtString .= self::getArrayInString($questions, ',') . ")";
		
		
		$stmt = $mydb->prepare($stmtString);
				
		return $stmt->execute($values);
	}
	
	private static function getArrayInString($array, $delim) {
		$arrlength = count($array);
		$toreturn = "";
		for ($i = 0; $i < $arrlength; $i++) {
			$toreturn .= $array[$i];
			if ($i != $arrlength-1) {
				$toreturn .= ", ";
			}
		}
		return $toreturn;
	}
	
}
?>