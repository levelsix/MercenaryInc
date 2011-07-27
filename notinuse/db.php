<html>

<head>
</head>
<body>

<?php

$user="ccs108cchan91";
$password="azeodexu";
$database="c_cs108_cchan91";
$server="mysql-user-master.stanford.edu";
mysql_connect($server,$user,$password);
@mysql_select_db($database) or die( "Unable to select database");

$query = "DROP TABLE IF EXISTS users;";
mysql_query($query) or die(mysql_error());

$query = "DROP TABLE IF EXISTS characters;";
mysql_query($query) or die(mysql_error());

$query = "CREATE TABLE users (
    ID int NOT NULL AUTO_INCREMENT,
    username VARCHAR(64),
    password VARCHAR(64),
    PRIMARY KEY (ID)
);";
mysql_query($query) or die(mysql_error());

$query = "CREATE TABLE characters (
    ID int NOT NULL AUTO_INCREMENT,
    charname VARCHAR(64),
    userid int,
    class VARCHAR(64),
    PRIMARY KEY (ID)
);";
mysql_query($query) or die(mysql_error());

$query = "INSERT INTO users (username, password) VALUES
       ('Conrad Chan', 'pw'), 
       ('King King', 'pw');";
mysql_query($query) or die(mysql_error()); 


echo "Database Created!";
mysql_close();
?>

</body>
</html>