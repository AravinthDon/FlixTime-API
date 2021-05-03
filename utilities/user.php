<?php
    // Common functionalities for the user table
    include("db.php");
    /**
     * check if the user credentials are valid for the api
     */
     function is_credentials_valid($conn, $user_id, $api_key) {
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

    /**
     * get the user's watchlist
     */
    function get_user_watchlist($conn, $user_id) {
        // sanitise the data
        $user_id = mysqli_real_escape_string($conn, trim($user_id));
        // build the query
        $USER_WATCHLIST_QUERY = "SELECT SHOW_ID FROM FT_Watchlist WHERE USER_ID = {$user_id}";

        // fetch the shows
        $fetched_shows = select_query($conn, $USER_WATCHLIST_QUERY);
        // create the shows array
        $shows = array();
        if($fetched_shows->num_rows > 0) {
            // fetch the data and add it to the results  
            while($show = $fetched_shows -> fetch_assoc()) {
                array_push($shows, $show['SHOW_ID']);
            }
        }

        return $shows;

    }

    /**
     * add a show to the user's watchlist
     */
    function add_user_watchlist($conn, $user_id, $show_id) {
        
        // sanitise the data
        $user_id = mysqli_real_escape_string($conn, trim($user_id));
        $show_id = mysqli_real_escape_string($conn, trim($show_id));

        $USER_ADD_WATCHLIST_QUERY = "INSERT INTO FT_Watchlist(`USER_ID`, SHOW_ID) VALUES({$user_id}, {$show_id})";
        // add the show
        return insert_query($conn, $USER_ADD_WATCHLIST_QUERY);
    }

    function remove_user_watchlist($conn, $user_id, $show_id) {

        // sanitise the data
        $user_id = mysqli_real_escape_string($conn, trim($user_id));
        $show_id = mysqli_real_escape_string($conn, trim($show_id));

        $USER_DELETE_WATCHLIST_QUERY = "DELETE FROM FT_Watchlist WHERE `USER_ID` = {$user_id} AND SHOW_ID = {$show_id}";

        $result = $conn->query($USER_DELETE_WATCHLIST_QUERY);

        if(!$result) {
            echo "Delete Error: ".$conn->error;
        } else {
            return $result;
        }

    }
    
?>