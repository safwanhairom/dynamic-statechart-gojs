<?php  

/* database credentials object-oriented style */
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'go_js');


/* connnection database  */
$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

//check connection
if($mysqli === true){
    die("Error: could not connect. syntax problem" . $mysqli->connect_error);
}
    

?>