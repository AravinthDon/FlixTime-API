<?php
    /**
     * Password hashing reference: https://www.sitepoint.com/hashing-passwords-php-5-5-password-hashing-api/
     */
    // set the headers
    header('Access-Control-Allow-Methods: POST');
    header('Content-Type: application/json');

    // check if the method is post
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        
        // check if the username and the password is given
        if(!isset($_POST['password']) && !isset($_POST['username'])) {
            echo json_encode(array("status" => "Error", "message" => "Username and Password not provided"));
        } elseif( !isset($_POST['password']) | !isset($_POST['username']) ) {
            // Respond with appropriate error messages
            if(!isset($_POST['username'])) {
                echo json_encode(array("status" => "Error", "message" => "Username not provided"));
            } elseif(!isset($_POST['password'])) {
                echo json_encode(array("status" => "Error", "message" => "Password not provided"));
            }
        } else {
            // include files
            include("../config/database.php");
            include("../utilities/db.php");

            // create user array
            $user = array();
            $user['username'] = $_POST['username'];
            $user['password'] = $_POST['password'];

            // trim whitespace on the $user details
            array_walk($user, 'trim_value');

            // sanitise the $user details
            array_walk($user, 'real_escape_string');

            // check if the username is already available
            $USER_CHECK_QUERY = "SELECT * FROM FT_User WHERE Username = '{$user['username']}'";

            $result = select_query($conn, $USER_CHECK_QUERY);

            if($result->num_rows > 0 ) {
                // the user is available check for the passowrd
                $row = $result->fetch_assoc();

                // get the details
                $user_id = $row['USER_ID'];
                $username = $row['Username'];
                $hash = $row['Password'];
                $api_key = $row['api_key'];
                
                // since the username is unique we only need to check the password
                if(password_verify($user['password'], $hash)){
                    $response = array();
                    $response['user_id'] = $user_id;
                    $response['api_key'] = $api_key;

                    echo json_encode(array("status" => "Success", "data" => $response));
                } else {
                    echo json_encode(array("status" => "Error", "message" => "Wrong Password"));
                }
            } else {
                echo json_encode(array("status" => "Error", "message" => "Username not found"));
            }

        }
    }
?>