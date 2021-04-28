<?php
    // Common utility functions for the api to work
    
    // Add values for single or more than one values

    //include("../config/database.php");
    include("db.php");

    function add_actor($conn, $actors) {
         
        $actors = explode(",", $actors);
        
        // trim whitespace from the names
        array_walk($actors, 'trim_value');

        // to store the actor ids
        $actor_ids = array();
        foreach($actors as $actor) {
            array_push($actor_ids, add_value($conn, "FT_Actor", "Actor_name", $actor, 'ACTOR_ID'));
        }

        return $actor_ids;
        
    }

    function add_country($conn, $country) {
        // call the add_value with the table, column name and the id details
        return add_value($conn, "FT_Country", "Country_Name", $country, "COUNTRY_ID");
    }

    function add_genre($conn, $genres) {

        $genres = explode(",", $genres);
        
        // trim whitespace from the names
        array_walk($genres, 'trim_value');

        // to store the genre ids
        $genre_ids = array();
        foreach($genres as $genre) {
            array_push($genre_ids, add_value($conn, "FT_Genre", "Genre_Name", $genre, "GENRE_ID"));
        }
        return $genre_ids;
    }

    function add_director($conn, $director) {
        return add_value($conn, "FT_Director", "Director_Name", $director, "DIRECTOR_ID");
    }

    function add_rating($conn, $rating) {
        return add_value($conn, "FT_Rating", "Rating_Type", $rating, "RATING_ID");
    }

    function add_showtype($conn, $showtype) {
        return add_value($conn, "FT_ShowType", "Type_Name", $showtype, "SHOWTYPE_ID");
    }

    function add_show($conn, $show) {
        
        // trim whiteshpace from the values
        array_walk($show, 'trim_value');

        // Declaring variables
        $country_id = "NULL";
        $director_id = "NULL";
        $rating_id = "NULL";
        $showtype_id = "NULL";
        
        $release_year = mysqli_real_escape_string($conn, $show['release_year']);
        $date_added = mysqli_real_escape_string($conn, $show['date_added']);
        $description = mysqli_real_escape_string($conn, $show['description']);

        // Add the Actor details if not exists
        $actor_ids = add_actor($conn, $show['cast']);

        // Add the Country details if not exists
        if (!empty($show['country'])) {
            $country_id = add_country($conn, $show['country']);
        }

        // Add the Genre if not exists
        $genre_ids = add_genre($conn, $show['listed_in']);
        
        // Add the Director if not exists
        if(!empty($show['director'])) {
            $director_id = add_director($conn, $show['director']);
        }
        
        // Add the Rating Type if not exists
        if(!empty($show['rating'])) {
            $rating_id = add_rating($conn, $show['rating']);
        }
        
        // Add the Show type if not exists
        if(!empty($show['type'])) {
            $showtype_id = add_showtype($conn, $show['type']);
        }
        
        // Add the show with the collected id's
        $show_insert_query = "INSERT INTO FT_Show(DIRECTOR_ID, Year_Released, Date_Added, RATING_ID, COUNTRY_ID, SHOWTYPE_ID, `Description`) VALUES({$director_id}, {$release_year}, '{$date_added}', {$rating_id}, {$country_id}, {$showtype_id}, '{$description}')";
        $show_id = insert_query($conn, $show_insert_query);

        // Link show genre
        foreach($genre_ids as $genre_id) {
            $genre_insert_query = "INSERT INTO FT_ShowGenre(SHOW_ID, GENRE_ID) VALUES({$show_id}, {$genre_id})";
            insert_query($conn, $genre_insert_query);
        }

        //echo "<div>$show_insert_query</div>";
        // Link show cast
        foreach($actor_ids as $actor_id) {
            $cast_insert_query = "INSERT INTO FT_ShowCast(SHOW_ID, ACTOR_ID) VALUES({$show_id}, {$actor_id})";
            insert_query($conn, $cast_insert_query);
        }

        
        // Check if the show is movie or tvshow
        // split the integer from string 
        // split 112 or 4 from 112 mins or 4 seasons
        $duration = explode(" ", $show['duration']);

        if ($show['type'] == "TV Show") { // Call the movie or tvshow function
            add_tvshow($conn, array("name" => $show['title'], "show_id" => $show_id, "seasons" => $duration[0]));
        } else if($show['type'] == "Movie"){
            add_movie($conn, array("name" => $show['title'], "show_id" => $show_id, "duration" => $duration[0]));
        } 
    } 


    function add_movie($conn, $movie) {
        // add the data to the movie table
        $title = mysqli_real_escape_string($conn, $movie['name']);
        $show_id = mysqli_real_escape_string($conn, $movie['show_id']);
        $duration = mysqli_real_escape_string($conn, $movie['duration']);

        // Insert query
        $movie_insert_query = "INSERT INTO FT_Movie(SHOW_ID, Movie_name, Movie_Duration) VALUES({$show_id}, '{$title}', {$duration})";

        insert_query($conn, $movie_insert_query);
    }

    function add_tvshow($conn, $tvshow) {
        // add the data to the tvshow table
        $title = mysqli_real_escape_string($conn, $tvshow['name']);
        $show_id = mysqli_real_escape_string($conn, $tvshow['show_id']);
        $seasons = mysqli_real_escape_string($conn, $tvshow['seasons']);

        // Insert query
        $tvshow_insert_query = "INSERT INTO FT_TvShow(SHOW_ID, TVShow_Name, Seasons) VALUES({$show_id}, '{$title}', {$seasons})";

        insert_query($conn, $tvshow_insert_query);
    }

    //print_r(add_actor($conn, "Shah,Krish,Rakesh"));
?>