<?php
    // Common functionalities for the user table

    /**
     * check if the user credentials are valid for the api
     */
     function credetianls_valid($conn, $user_id, $api_key) {
        // include the db helping file
        include("db.php");
        // sanitise the data
        $user_id = mysqli_real_escape_string($conn, trim($user_id));
        $api_key = mysqli_real_escape_string($conn, trim($api_key));

        $API_KEY_VALIDATE_QUERY = "SELECT * FROM FT_User WHERE USER_ID = {$user_id} AND api_key = '{$api_key}'";

        $result = select_query($conn, $API_KEY_VALIDATE_QUERY);

        if($result->num_rows > 0) {
            return true;
        } else {
            return false;
        }
     }
    
?>