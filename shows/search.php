<?php

    // to make the content type json
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Origin: *');

    // include the database config
    include("../config/database.php");
    include("../utilities/db.php");
    /**
     * returns the show_id if the show is available
     * else returns NULL
     */
    function search_show($shows, $show_title, $show_type) {

        // array to store the matched shows
        $matched_shows = array();

        // if show list is not empty search for the string in the show
        if($shows->num_rows > 0) {

            // include the core.php file for getting the $HOME_URL variable
            include("../config/core.php");
            $SHOW_URL = $HOME_URL. "shows/show.php?showid=";
            
            // search for the title in the fetched results and build the urls
            while($row = $shows->fetch_assoc()) {
                
                //print_r($row);
                $title = NULL;

                // assign title to the title variable based on the show type
                if ($show_type == "Movie") {
                    $title = $row['Movie_Name'];
                } else if ($show_type == "TV Show") {
                    $title = $row['TVShow_Name'];
                } 

                // Check if the title has the term show_title in it
                if(strstr(strtoupper($title), strtoupper($show_title))) {
                    //echo($title);
                    array_push($matched_shows, array("Title" => $title, "URL" => $SHOW_URL. strval($row['SHOW_ID']), )); 
                }
            }
        }
        //print_r($matched_shows);
        
        return $matched_shows;

        
    }


    // to store the results of movie and tvshows
    $results = array();

    // Get the movie title from the url
    if(isset($_GET['keyword'])) {
        $search_title = trim($_GET['keyword']);

        // fetch the title for the movies and the tv shows
        $MOVIE_FETCH_QUERY = "SELECT * FROM FT_Movie";
        $TVSHOW_FETCH_QUERY = "SELECT * FROM FT_TvShow";

        // get the movies
        $movies = select_query($conn, $MOVIE_FETCH_QUERY);
        //echo $search_title;
        // search for the movie
        $movie_results = search_show($movies, $search_title, "Movie");

        // get the tv shows
        $tvshows = select_query($conn, $TVSHOW_FETCH_QUERY);
        // search for the tv show
        $tvshow_results = search_show($tvshows, $search_title, "TV Show");

        $temp_results = array(); // temp array
        $results["Movies"] = $movie_results;
        $results["TV_Shows"] = $tvshow_results;
        // push the results array
        //array_push($results, $temp_results);
        echo json_encode(array("results" => $results));
    }
    
    


    
?>