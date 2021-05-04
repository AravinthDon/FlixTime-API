<?php
    // returns the show details

    // to make the content type json
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: GET');

    // include the database config
    include("../config/database.php");
    include("../utilities/show.php");


    if($_SERVER['REQUEST_METHOD'] == 'GET') {

        // show fetching query
        $SHOW_FETCH_QUERY = "SELECT SHOW_ID FROM FT_Show";
        // check if the limit and offset is set
        if(isset($_GET['limit']) && isset($_GET['offset'])) {
            $limit = mysqli_real_escape_string($conn, $_GET['limit']);
            $offset = mysqli_real_escape_string($conn, $_GET['offset']);

            $SHOW_FETCH_QUERY .= " LIMIT {$limit}, {$offset}";
        }

        $fetched_shows = select_query($conn, $SHOW_FETCH_QUERY);

        $results = array();

        if($fetched_shows->num_rows > 0) {

            // include the core.php file for getting the $HOME_URL variable
            include("../config/core.php");
            $SHOW_URL = $HOME_URL. "shows/show.php?showid=";

            // create the array
            
            while ($row = $fetched_shows -> fetch_assoc()) {
                $url = $SHOW_URL.$row['SHOW_ID'];
                array_push($results, array("show_id" => $row['SHOW_ID'], "url" => $url));
            }

        } 
        // echo the result
        echo json_encode(array("status" => "success", "data" => $results));
    }
    

?>