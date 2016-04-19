<?php

//TODO: Migrate to another Database that supports Foreign Key
class DB_Functions_Question
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

    public function postQuestion($email, $questionTags, $questionTitle, $questionBody, $latitude, $longitude)
    {
        $quid = uniqid('', true);
        mysqli_query($this->conn, "INSERT INTO `questions`(email, latitude, longitude, questionTags, questionTitle, questionBody, timestamp, quid) VALUES('$email', '$latitude', '$longitude', '$questionTags', '$questionTitle', '$questionBody', NOW(), '$quid');");
        $result = mysqli_query($this->conn, "SELECT * FROM `questions` WHERE questionTitle = '$questionTitle'");
        // check for successful store
        if ($result) {
            // return question details
            return mysqli_fetch_array($result);
        } else {
            return false;
        }
    }

    public function getAnswer($qTitle, $latitude, $longitude)
    {
        //TODO: Refine the answers according to the location of the user
        $qidResult = mysqli_query($this->conn, "SELECT quid FROM `questions` WHERE questionTitle = '$qTitle'");
        $tempArray = mysqli_fetch_array($qidResult);
        $qid = $tempArray["quid"];
        $result = mysqli_query($this->conn, "SELECT answer, email FROM `answers` WHERE qid = '$qid'");
        if ($result) {
            // return all the answer to the particular question
            return $result;
        } else {
            return false;
        }
    }

    public function aurDikhao($latitude, $longitude, $timestamp)
    {
        //TODO: Refine the questions according to the location of the user
        $result = mysqli_query($this->conn, "SELECT questionTitle FROM `questions` WHERE timestamp > '$timestamp' LIMIT 10");
        if ($result) {
            // return all the answer to the particular question
            return $result;
        } else {
            return false;
        }
    }

    public function postAnswer($qTitle, $email, $answer)
    {
        $qidResult = mysqli_query($this->conn, "SELECT quid FROM `questions` WHERE questionTitle = '$qTitle'");
        $tempArray = mysqli_fetch_array($qidResult);
        $qid = $tempArray["quid"];
        mysqli_query($this->conn, "INSERT INTO `answers`(qid, email, answer, timestamp) VALUES ('$qid','$email', '$answer', 'NOW()');");
        $result = mysqli_query($this->conn, "SELECT * FROM `answers` WHERE qid = '$qid'");
        // check for successful store
        if ($result) {
            // return question details
            return mysqli_fetch_array($result);
        } else {
            return false;
        }
    }
}