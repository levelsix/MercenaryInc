#Mercenary, Inc

##Dependencies
###PDO
Make sure you have the MySQL PDO driver

`php -r 'print_r(PDO::getAvailableDrivers());'`

##Database Connection
In the properties directory, create a file called dbproperties.php and define four variables
`$user="...";

$password="...";

$database="mercenaryinc";

$server="...";`