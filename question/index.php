<?php
/**
 * PHP API for Storing locations, both home and current
 **/
if (isset($_POST['tag']) && $_POST['tag'] != '') {
    // Get tag
    $tag = $_POST['tag'];
    // Include Database handler

    require_once 'DB_Functions_Question.php';
    $db = new DB_Functions_Question();
    // response Array
    $response = array("tag" => $tag, "success" => 0, "error" => 0);
    // check for tag type
    if ($tag == 'postQuestion') {
        // Request type is storing question posted
        $email = $_POST['email'];
        $questionTags = $_POST['qTag'];
        $questionTitle = $_POST['qTitle'];
        $questionBody = $_POST['qBody'];
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];

        // post question
        $user = $db->postQuestion($email, $questionTags, $questionTitle, $questionBody, $latitude, $longitude);
        echo $user;
        if ($user) {
            // user stored successfully
            $response["success"] = 1;
            echo json_encode($response);
        } else {
            // user failed to store
            $response["error"] = 1;
            $response["error_msg"] = "JSON Error occurred in posting Question";
            $response["user"] = $user;
            echo json_encode($response);
        }
    } elseif ($tag == 'getAnswer') {

        $qTitle = $_POST['qTitle'];
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];

        // post question
        $result = $db->getAnswer($qTitle, $latitude, $longitude);
        if ($result) {
            // answer received successfully
            $rank = 0;
            $rows = array();
            if (mysqli_num_rows($result) > 0) {
                while ($row = $result->fetch_assoc()) {
                    $row["rank"] = $rank;
                    $rows[] = $row;
                    $rank = $rank + 1;
                }
                $response["success"] = 1;
                $response["answers"] = $rows;
                echo json_encode($response);
            } else {
                // user failed to store
                $response["error"] = 1;
                $response["error_msg"] = "JSON Error occurred in getting answer";
                echo json_encode($response);
            }
        } else {
            // user failed to store
            $response["error"] = 1;
            $response["error_msg"] = "JSON Error occurred in getting answer";
            echo json_encode($response);
        }
    } elseif ($tag == 'aurDikhao') {

        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];
        $timestamp = $_POST['timestamp'];
        $rank = $_POST["offset"];

        // show more questions
        $result = $db->aurDikhao($latitude, $longitude, $timestamp);
        if (mysqli_num_rows($result) > 0) {
            $rows = array();
            while ($row = $result->fetch_assoc()) {
                $row["rank"] = $rank;
                $rows[] = $row;
                $rank = $rank + 1;
            }
            $response["success"] = 1;
            $response["questions"] = $rows;
            echo json_encode($response);

            echo json_encode($response);
        } else {
            // user failed to store
            $response["error"] = 1;
            $response["error_msg"] = "JSON Error occurred in getting questions";
            echo json_encode($response);
        }
    } elseif ($tag == 'postAnswer') {
        $email = $_POST['email'];
        $questionTitle = $_POST['qTitle'];
        $answer = $_POST['answer'];
        // post answer
        $user = $db->postAnswer($questionTitle, $email, $answer);
        if ($user) {
            // user stored successfully
            $response["success"] = 1;
            echo json_encode($response);
        } else {
            // user failed to store
            $response["error"] = 1;
            $response["error_msg"] = "JSON Error occurred in posting answer";
            $response["user"] = $user;
            echo json_encode($response);
        }
    }
}