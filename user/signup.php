<?php
    // set the headers
    header('Access-Control-Allow-Methods: POST');
    header('Content-Type: application/json');

    $response = array();

    if(!isset($_POST['email']) | !isset($_POST['password']) | !isset($_POST['firstname']) | !isset($_POST['lastname']) | isset($_POST['username'])) {
        $resp = array();
        $resp['Status'] = "Error";
        
        if(!isset($_POST['email']) && !isset($_POST['password'])) {
            $resp['Message'] = "Please Enter your email and password.";
            // send the response
        }

        if(!isset($_POST['email'])) {
            $resp['Message'] = "Please Enter your email!";
        }

        if(!isset($_POST['password'])) {
            $resp['Message'] = "Please Enter your password!";
        }
    } else {

    }
?>