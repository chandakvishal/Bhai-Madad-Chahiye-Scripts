<?php
/**
 * PHP API for Storing locations, both home and current
 **/
if (isset($_POST['tag']) && $_POST['tag'] != '') {
    // Get tag
    $tag = $_POST['tag'];
    // Include Database handler

    require_once 'DB_Functions_Location.php';
    $db = new DB_Functions_Location();
    // response Array
    $response = array("tag" => $tag, "success" => 0, "error" => 0);
    // check for tag type
    if ($tag == 'homeLocation') {
        // Request type is storing home location
        $email = $_POST['email'];
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];

        // store user
        $user = $db->storeUserHomeLocation($email, $latitude, $longitude);
        if ($user) {
            // user stored successfully
            $response["success"] = 1;
            $response["homelocal"]["email"] = $user["email"];
            $response["homelocal"]["latitude"] = $user["latitude"];
            $response["homelocal"]["longitude"] = $user["longitude"];
            echo json_encode($response);
        } else {
            // user failed to store
            $response["error"] = 1;
            $response["error_msg"] = "JSON Error while storing Location";
            echo json_encode($response);
        }
    }
}