<?php

class DB_Functions_Location
{
    private $db;
    public $conn;
    //put your code here
    // constructor
    function __construct()
    {
        require_once '../include/DB_Connect.php';
        // connecting to database
        $this->db = new DB_Connect();
        $this->conn = $this->db->connect();
    }

    // destructor
    function __destruct()
    {
    }

    public function storeUserHomeLocation($email, $latitude, $longitude)
    {
        $lat = floatval($latitude);
        $long = floatval($longitude);
        mysqli_query($this->conn, "INSERT INTO `homelocal`(email, latitude, longitude) VALUES('$email', '$lat', '$long');");
        $result = mysqli_query($this->conn, "SELECT * FROM `homelocal` WHERE email = '$email'");
        // check for successful store
        if ($result) {
            // return user details
            return mysqli_fetch_array($result);
        } else {
            return false;
        }
    }
}
