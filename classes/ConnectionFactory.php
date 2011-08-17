<?php
class ConnectionFactory {
	
	private static $factory;
	
	function __construct() {
	}
	
	public static function getFactory() {
		if (!self::$factory)
			self::$factory = new ConnectionFactory();
		return self::$factory;
	}
	
	private $db;
	
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
}
?>