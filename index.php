<?php
/**
 * PHP API for Login, Register, Changepassword, Resetpassword Requests and for Email Notifications.
 **/
if (isset($_POST['tag']) && $_POST['tag'] != '') {
    // Get tag
    $tag = $_POST['tag'];
    // Include Database handler
    require_once 'include/DB_Functions.php';
    $db = new DB_Functions();
    // response Array
    $response = array("tag" => $tag, "success" => 0, "error" => 0);
    // check for tag type
    if ($tag == 'login') {
        // Request type is check Login
        $email = $_POST['email'];
        $password = $_POST['password'];
        // check for user
        $user = $db->getUserByEmailAndPassword($email, $password);
        if ($user != false) {
            // user found
            // echo json with success = 1
            $response["success"] = 1;
            $response["user"]["fname"] = $user["fullname"];
            $response["user"]["phone"] = $user["phone"];
            $response["user"]["email"] = $user["email"];
            $response["user"]["uname"] = $user["username"];
            $response["user"]["uid"] = $user["unique_id"];
            $response["user"]["created_at"] = $user["created_at"];
            echo json_encode($response);
        } else {
            // user not found
            // echo json with error = 1
            $response["error"] = 1;
            $response["error_msg"] = "Incorrect email or password!";
            echo json_encode($response);
        }
    } else if ($tag == 'chgpass') {
        $email = $_POST['email'];
        $newpassword = $_POST['newpas'];
        $hash = $db->hashSSHA($newpassword);
        $encrypted_password = $hash["encrypted"]; // encrypted password
        $salt = $hash["salt"];
        $subject = "Change Password Notification";
        $message = "Hello User,\n\nYour Password is successfully changed.nnRegards,\nBhai.";
        $from = "bhai@bhaimadadchahiye.club";
        $headers = "From:" . $from;
        if ($db->isUserExisted($email)) {

            $user = $db->forgotPassword($email, $encrypted_password, $salt);
            if ($user) {
                $response["success"] = 1;
                mail($email, $subject, $message, $headers);
                echo json_encode($response);
            } else {
                $response["error"] = 1;
                echo json_encode($response);
            }
            // user is already existed - error response
        } else {
            $response["error"] = 2;
            $response["error_msg"] = "User not exist";
            echo json_encode($response);
        }
    } else if ($tag == 'forpass') {
        $forgotpassword = $_POST['forgotPassword'];
        $randomcode = $db->random_string();
        $hash = $db->hashSSHA($randomcode);
        $encrypted_password = $hash["encrypted"]; // encrypted password
        $salt = $hash["salt"];
        $subject = "Password Recovery";
        $message = "Hello User,\n\nYour Password is successfully changed. Your new Password is $randomcode . Login with your new Password and change it in the User Panel.\n\nRegards,\nBhai.";
        $from = "bhai@bhaimadadchahiye.club";
        $headers = "From:" . $from;
        if ($db->isUserExisted($forgotpassword)) {

            $user = $db->forgotPassword($forgotpassword, $encrypted_password, $salt);
            if ($user) {
                $response["success"] = 1;
                mail($forgotpassword, $subject, $message, $headers);
                echo json_encode($response);
            } else {
                $response["error"] = 1;
                echo json_encode($response);
            }
            // user is already existed - error response
        } else {

            $response["error"] = 2;
            $response["error_msg"] = "User not exist";
            echo json_encode($response);
        }
    } else if ($tag == 'register') {
        // Request type is Register new user
        $fname = $_POST['fname'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $uname = $_POST['uname'];
        $password = $_POST['password'];

        $subject = "Registration";
        $message = "Hello $fname,\n\nYou have successfully registered to our service.\n\nRegards,\nBhai.";
        $from = "bhai@bhaimadadchahiye.club";
        $headers = "From:" . $from;
        // check if user is already existed
        if ($db->isUserExisted($email)) {
            // user is already existed - error response
            $response["error"] = 2;
            $response["error_msg"] = "User already existed";
            echo json_encode($response);
        } else if (!$db->validEmail($email)) {
            $response["error"] = 3;
            $response["error_msg"] = "Invalid Email Id";
            echo json_encode($response);
        } else {
            // store user
            $user = $db->storeUser($fname, $phone, $email, $uname, $password);
            if ($user) {
                // user stored successfully
                $response["success"] = 1;
                $response["user"]["fname"] = $user["fullname"];
                $response["user"]["phone"] = $user["phone"];
                $response["user"]["email"] = $user["email"];
                $response["user"]["uname"] = $user["username"];
                $response["user"]["uid"] = $user["unique_id"];
                $response["user"]["created_at"] = $user["created_at"];
                mail($email, $subject, $message, $headers);
                echo json_encode($response);
            } else {
                // user failed to store
                $response["error"] = 1;
                $response["error_msg"] = "JSON Error occured in Registartion";
                echo json_encode($response);
            }
        }
    } else {
        $response["error"] = 3;
        $response["error_msg"] = "JSON ERROR";
        echo json_encode($response);
    }
} else {
    echo "Bhai Bas aa raha hai....";
}