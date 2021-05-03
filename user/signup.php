<?php

    /**
     * Passowrd hash reference: https://www.sitepoint.com/hashing-passwords-php-5-5-password-hashing-api/
     */
    // set the headers
    header('Access-Control-Allow-Methods: POST');
    header('Content-Type: application/json');

    /**
     * Random token generator
     * Code Referenced from: https://stackoverflow.com/questions/4356289/php-random-string-generator/31107425#31107425
     */
    function random_str(
        int $length = 64,
        string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
    ): string {

        $keyspace = str_shuffle($keyspace );

        if ($length < 1) {
            return NULl;
        }
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces []= $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST') {

        // check if the username and password is given
        if(!isset($_POST['password']) && !isset($_POST['username'])) {
            echo json_encode(array("status" => "Error", "message" => "Username and Password not provided"));
        } elseif( !isset($_POST['password']) | !isset($_POST['username']) ) {
            if(!isset($_POST['username'])) {
                echo json_encode(array("status" => "Error", "message" => "Username not provided"));
            } elseif(!isset($_POST['password'])) {
                echo json_encode(array("status" => "Error", "message" => "Passowrd not provided"));
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
            $USER_CHECK_QUERY = "SELECT USER_ID FROM FT_User WHERE Username = '{$user['username']}'";
    
            $result = select_query($conn, $USER_CHECK_QUERY);
    
            if($result->num_rows > 0) {
                //array_push();
                echo json_encode(array("status" => "Error", "message" => "Username already taken"));
            } else {
                // generate the has
                $hash = password_hash($user['password'], PASSWORD_DEFAULT);
                // generate the random key
                $token_key = random_str();
                $CREATE_USER_QUERY = "INSERT INTO FT_User(Username, `Password`, api_key) VALUES('{$user['username']}', '{$hash}', '{$token_key}')";
    
                $user_id = insert_query($conn, $CREATE_USER_QUERY);
                
                // create the response
                $response = array();
                if(!empty($user_id)) {
                    $response['user_id'] = $user_id;
                    $response['api_key'] = $token_key;
    
                    echo json_encode(array("status" => "Success", "data" => $response));
                } else {
                    header("HTTP/1.0 500 Internal Server Error");
                    echo json_encode(array("status" => "Error", "message" => "Internal Server Error"));
                }
 
            }
    }
    

    
    }
    




?>