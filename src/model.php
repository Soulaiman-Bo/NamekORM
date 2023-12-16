<?php 

require_once "SQL.php";

$servername = "localhost";
$username = "root";
$password = "";
$database = "peoplepertask";


  

class Model extends SQL {

    private static $servername;
    private static $username;
    private static $password;
    private static $database;
    protected static $connection = null;

}

