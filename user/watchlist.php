<?php
    // set the access control methods
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: GET POST PUT');

    // include the required files
    include("../config/database.php");
    include("../utilities/user.php");

    // check if the credentials are set
    if($_SERVER['REQUEST_METHOD'] == 'GET') {
        // check if the credentials are provided
        if(isset($_GET['user_id']) && isset($_GET['api_key'])){
            if(is_credentials_valid($conn, $_GET['user_id'], $_GET['api_key'])) {
                // fetch the user watchlist

                if(isset($_GET['show_id'])) {
                    if(($watchlist_id = isWatchlist($conn, $_GET['user_id'], $_GET['show_id'])) != null){
                        echo json_encode(array("status" => "Found", "watchlistid" => $watchlist_id));
                    } else {
                        echo json_encode(array("status" => "Nope", "watchlistid" => null));
                    }
                } else {
                    $shows = get_user_watchlist($conn, $_GET['user_id']);
                    
                    // respond with the data
                    echo json_encode(array("status" => "Success", "data" => $shows));
                }
                
            } else {
                header("HTTP/1.0 401 Unauthorized");
                echo json_encode(array("status" => "Error", "message" => "Invalid User"));
            }
        } else {
            header("HTTP/1.0 401 Unauthorized");
            echo json_encode(array("status" => "Error", "message" => "Missing User Credentials"));
        }


    } elseif($_SERVER['REQUEST_METHOD'] == 'POST') {
        // check if the credentials are provided
        if(isset($_POST['user_id']) && isset($_POST['api_key'])) {
            if(is_credentials_valid($conn, $_POST['user_id'], $_POST['api_key'])) {
                // check if the show id is provided
                if(isset($_POST['show_id'])) {
                    $watchlist_id = add_user_watchlist($conn, $_POST['user_id'], $_POST['show_id']);
                    echo json_encode(array("status" => "Success", "data" => array("watchlist_id" => $watchlist_id)));
                }
            } else {
                header("HTTP/1.0 401 Unauthorized");
                echo json_encode(array("status" => "Error", "message" => "Invalid User"));
            }
        } else {
            header("HTTP/1.0 401 Unauthorized");
            echo json_encode(array("status" => "Error", "message" => "Missing User Credentials"));
        }
        
    } elseif($_SERVER['REQUEST_METHOD'] == 'DELETE') {

        parse_str(file_get_contents("php://input"), $delete_vars);

        if(!empty($delete_vars)) {
            // check if the credentials are provided
            if(isset($delete_vars['user_id']) && isset($delete_vars['api_key'])) {
                if(is_credentials_valid($conn, $delete_vars['user_id'], $delete_vars['api_key'])) {
                    // check if the show id is provided
                    if(isset($delete_vars['show_id'])) {
                        remove_user_watchlist($conn, $delete_vars['user_id'], $delete_vars['show_id']);
                        echo json_encode(array("status" => "Success", "data" => array()));
                    } 
                } else {
                        header("HTTP/1.0 401 Unauthorized");
                        echo json_encode(array("status" => "Error", "message" => "Invalid User"));
                    }
            } else {
                    header("HTTP/1.0 401 Unauthorized");
                    echo json_encode(array("status" => "Error", "message" => "Missing User Credentials"));
            }

        }  
    }
?>