<?php

class DB_Functions
{
    private $db;
    public $conn;
    //put your code here
    // constructor
    function __construct()
    {
        require_once 'DB_Connect.php';
        // connecting to database
        $this->db = new DB_Connect();
        $this->conn = $this->db->connect();
    }

    // destructor
    function __destruct()
    {
    }

    /**
     * Random string which is sent by mail to reset password
     */
    public function random_string()
    {
        $character_set_array = array();
        $character_set_array[] = array('count' => 7, 'characters' => 'abcdefghijklmnopqrstuvwxyz');
        $character_set_array[] = array('count' => 1, 'characters' => '0123456789');
        $temp_array = array();
        foreach ($character_set_array as $character_set) {
            for ($i = 0; $i < $character_set['count']; $i++) {
                $temp_array[] = $character_set['characters'][rand(0, strlen($character_set['characters']) - 1)];
            }
        }
        shuffle($temp_array);
        return implode('', $temp_array);
    }

    public function forgotPassword($forgotpassword, $newpassword, $salt)
    {
        $result = mysqli_query($this->conn, "UPDATE `users` SET `encrypted_password` = '$newpassword',`salt` = '$salt'
        WHERE `email` = '$forgotpassword'");
        if ($result) {

            return true;
        } else {
            return false;
        }
    }

    /**
     * Adding new user to mysql database
     * returns user details
     */
    public function storeUser($fname, $lname, $email, $uname, $password)
    {
        $uuid = uniqid('', true);
        $hash = $this->hashSSHA($password);
        $encrypted_password = $hash["encrypted"]; // encrypted password
        $salt = $hash["salt"]; // salt
        $result = mysqli_query($this->conn, "INSERT INTO users(unique_id, fullname, phone, email, username, encrypted_password, salt, created_at) VALUES('$uuid', '$fname', '$lname', '$email', '$uname', '$encrypted_password', '$salt', NOW())");
        // check for successful store
        if ($result) {
            // get user details
            $uid = mysqli_insert_id($this->conn); // last inserted id
            $result = mysqli_query($this->conn, "SELECT * FROM users WHERE uid = $uid");
            // return user details
            return mysqli_fetch_array($result);
        } else {
            return false;
        }
    }

    /**
     * Verifies user by email and password
     */
    public function getUserByEmailAndPassword($email, $password)
    {
        $result = mysqli_query($this->conn, "SELECT * FROM users WHERE email = '$email'") or die(mysqli_error($this->conn));
        // check for result
        $no_of_rows = mysqli_num_rows($result);
        if ($no_of_rows > 0) {
            $result = mysqli_fetch_array($result);
            $salt = $result['salt'];
            $encrypted_password = $result['encrypted_password'];
            $hash = $this->checkhashSSHA($salt, $password);
            // check for password equality
            if ($encrypted_password == $hash) {
                // user authentication details are correct
                return $result;
            }
        } else {
            // user not found
            return false;
        }
    }

    /**
     * Checks whether the email is valid or fake
     */
    public function validEmail($email)
    {
        $isValid = false;
        $clean_email = filter_var($email, FILTER_SANITIZE_EMAIL);

        if ($email == $clean_email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            list($user, $domain) = explode('@', $email);
            $arr = dns_get_record($domain, DNS_MX);
            $arr = $arr + array(null);
            if ($arr[0]['host'] == $domain && !empty($arr[0]['target'])) {
                $isValid = true;
            }
        }
        return $isValid;
    }

    /**
     * Check user is existed or not
     */
    public function isUserExisted($email)
    {
        $mysql_qry = "SELECT email from users WHERE email = '$email';";
        $result = mysqli_query($this->conn, $mysql_qry);
        $no_of_rows = mysqli_num_rows($result);
        if (is_bool($no_of_rows) === true) {
            $no_of_rows = 0;
        }
        if ($no_of_rows > 0) {
            // user existed
            return true;
        } else {
            // user not existed
            return false;
        }
    }

    /**
     * Encrypting password
     * returns salt and encrypted password
     */
    public function hashSSHA($password)
    {

        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
        $hash = array("salt" => $salt, "encrypted" => $encrypted);
        return $hash;
    }

    /**
     * Decrypting password
     * returns hash string
     */
    public function checkhashSSHA($salt, $password)
    {
        $hash = base64_encode(sha1($password . $salt, true) . $salt);
        return $hash;
    }
}

?>