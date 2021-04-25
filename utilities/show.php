<?php
    // Common utility functions for the api to work

    function add_actor($conn, $actor) {
        
        include_once("db.php");

        $actor_name = $conn->real_escape_string($actor['name']);
        $query = "INSERT INTO FT_Actor(Actor_name) VALUES({$actor_name})";

        if ($actor_id = insert_query($conn, $query)) {
            return $actor_id;
        }

        return NULL;
    }

    function add_show($conn, $show) {
        
        $show_id = 0;
        
        
        
        // Add the Actor details if not exists
        // Add the Country details if not exists
        // Add the Genre if not exists
        // Add the Director if not exists
        // Add the Rating Type if not exists
        

        // Add the show with the collected id's

        // Link show genre

        // Link show cast

        // 

        // Check if the show is movie or tvshow
        if ($show['type'] == 'TV Show') { // Call the movie or tvshow function
            add_tvshow($conn, array("name: " => $show['title'], "duration: " => $show['duration']));
        } else if($show['type'] ){
            add_movie($conn, array("name: " => $show['title'], "duration: " => $show['duration']));
        }



    }
    function add_movie($conn, $movie) {
        // add the data to the movie table



    }

    function add_tvshow($conn, $tvshow) {
        // add the data to the tvshow table

    }
?>