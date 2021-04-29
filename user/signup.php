<?php
    // set the headers
    header('Access-Control-Allow-Methods: POST');
    header('Content-Type: application/json');

    $response = array();

    if( !isset($_POST['password']) | !isset($_POST['firstname']) | !isset($_POST['lastname']) | isset($_POST['username']) ) {

        $resp = array();
        $resp['Status'] = "Error";
        $message = "Please include your ";

        
        if(!isset($_POST['firstname'])) {
            $message = " First name";
        }

        if(!isset($_POST['lastname'])) {
            $message.= " Last name";
        }

        if(!isset($_POST['username'])) {
            $message .= " Username";
        }

        if(!isset($_POST['password'])) {
            $message .= " Password";
        }
        $message .= ".";
        $resp['Message'] = $message;
        // add the response 
        array_push($response, $resp);
        // send the response
        echo json_encode($response);

    } 
    
    // include files
    include("../config/database.php");
    include("../utilities/db.php");

    // create user array
    $user = array();
    $user['username'] = $_POST['username'];
    //$user['email'] = $_POST['email'];
    $user['password'] = $_POST['password'];
    $user['firstname'] = $_POST['firsname'];
    $user['lastname'] = $_POST['lastname'];

    // trim whitespace on the $user details
    
    array_walk($user, 'trim_value');

    // sanitise the $user details
    array_walk($user, 'real_escape_string');

    // check if the username is already available
    $USER_CHECK_QUERY = "SELECT USER_ID FROM FT_User WHERE Username = '{$user['username']}'";

    $result = select_query($conn, $USER_CHECK_QUERY);

    if($result->num_rows > 0) {
        array_push($response, array("status" => "Error", "message" => "Username already used."));
        echo json_encode($response);
    }

    $CREATE_USER_QUERY = "INSERT INTO FT_User(First_Name, Last_Name, Username, `Password`) VALUES('{$user['firstname']}', '{$user['lastname']}', '{$user['username']}', '{$user['password']}'";

    $user_id = insert_query($conn, $CREATE_USER_QUERY);

    // create the token key

    // store it in session variable

    // output the token key



?>