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
	private $host = '50.18.188.59';
	private $dbname = 'mercenaryinc';
	private $user = 'calvin';
	private $password = 'thetanati0n';
	
	public function getConnection() {
		if (!$this->dbh) {
			try {
				$this->dbh = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->user, $this->password);
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