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
	
	private $dbh;
	
	public function getConnection() {
		if (!$this->dbh) {
			try {
				$server = '50.18.188.59';
				$database = 'mercenaryinc';
				$user = 'calvin';
				$password = 'thetanati0n';
				
				$this->dbh = new PDO("mysql:host=$server;dbname=$database", $user, $password);
				$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			} catch (PDOException $e) {
				// Redirect to 'server down' page
				print "Our servers are currently being serviced. Please try again later.";
				print $e->getMessage();
			}
		}
		return $this->dbh;
	}
}
?>