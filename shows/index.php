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
        $SHOW_FETCH_QUERY = "SELECT * FROM FT_Show";
        // check if the limit and offset is set
        if(isset($_GET['limit']) && isset($_GET['offset'])) {
            $limit = mysqli_real_escape_string($conn, $_GET['limit']);
            $offset = mysqli_real_escape_string($conn, $_GET['offset']);

            $SHOW_FETCH_QUERY .= " LIMIT {$offset}, {$limit}";
        }

        $fetched_shows = select_query($conn, $SHOW_FETCH_QUERY);

        $results = array();

        if($fetched_shows->num_rows > 0) {

            while ($row = $fetched_shows -> fetch_assoc()) {
                $show_id = $row['SHOW_ID'];
                $title = '';
                
                if($row['SHOWTYPE_ID'] == 2){
                    
                    // movie name
                    $MOVIE_NAME_FETCH_QUERY = "SELECT Movie_Name from FT_Movie WHERE SHOW_ID = $show_id";
                    $fetched_movie = select_query($conn, $MOVIE_NAME_FETCH_QUERY);
                    if($fetched_movie->num_rows > 0) {
                        $movie = $fetched_movie -> fetch_assoc();
                        $title = $movie['Movie_Name'];
                    } 
                } else {
                    $TVSHOW_NAME_FETCH_QUERY = "SELECT TVShow_Name from FT_TvShow WHERE SHOW_ID = $show_id";
                    $fetched_tvshow = select_query($conn, $TVSHOW_NAME_FETCH_QUERY);
                    if($fetched_tvshow->num_rows > 0) {
                        $tvshow = $fetched_tvshow -> fetch_assoc();
                        $title = $tvshow['TVShow_Name'];
                    } 
                }
                $MOVIE_NAME_FETCH_QUERY = "SELECT Movie_Name from FT_Movie WHERE SHOW_ID = $show_id";
                $fetched_movie = 
                $poster_url = '';
                $banner_url = '';
                get_urls($conn, $row['SHOW_ID'],$poster_url, $banner_url);
                array_push($results, array("show_id" => $row['SHOW_ID'], "Title" => $title, "poster_url" => $poster_url, "banner_url" => $banner_url));
            }

        } 
        // echo the result
        echo json_encode(array("status" => "success", "data" => $results));
    }
    

?>