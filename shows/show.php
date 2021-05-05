<?php
    // to make the content type json
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: GET POST PUT');
    header('Access-Control-Allow-Origin: *');
    // include the database config
    include("../config/database.php");
    // include the show utilities function
    include("../utilities/show.php");
    // include the db functions file
    /**
     * Gets the detailed show for the given showid
     */
    function get_show_detailed($conn, $showid) {
        // include the db utilities file
        //include("../utilities/db.php");
        // to store the array
        $show = array();

        // building the select query
        $SHOW_SELECT_QUERY = "SELECT FT_Show.SHOW_ID, FT_Director.Director_Name, Year_Released, Date_Added, FT_Rating.Rating_Type, FT_ShowType.Type_Name, FT_Show.Description, FT_Show.poster_url, FT_Show.banner_url
                            FROM FT_Show 
                            LEFT JOIN FT_Director ON FT_Show.DIRECTOR_ID = FT_Director.DIRECTOR_ID 
                            LEFT JOIN FT_Rating ON FT_Show.RATING_ID = FT_Rating.RATING_ID 
                            LEFT JOIN FT_ShowType ON FT_Show.SHOWTYPE_ID = FT_ShowType.SHOWTYPE_ID
                            WHERE SHOW_ID = {$showid}";
        
        $CAST_SELECT_QUERY = "SELECT FT_Actor.Actor_Name FROM FT_ShowCast LEFT JOIN FT_Actor ON FT_ShowCast.ACTOR_ID = FT_Actor.ACTOR_ID WHERE SHOW_ID = {$showid}";
        $COUNTRY_SELECT_QUERY = "SELECT FT_Country.Country_Name FROM FT_CountryReleased LEFT JOIN FT_Country ON FT_CountryReleased.COUNTRY_ID = FT_Country.COUNTRY_ID WHERE SHOW_ID = {$showid}";
        $GENRE_SELECT_QUERY = "SELECT FT_Genre.Genre_Name FROM FT_ShowGenre LEFT JOIN FT_Genre ON FT_ShowGenre.GENRE_ID = FT_Genre.GENRE_ID WHERE SHOW_ID = {$showid}";
        
        $fetched_show = select_query($conn, $SHOW_SELECT_QUERY);

        if($fetched_show->num_rows > 0) {

            
            // Add the title first
            $show['Title'] = NULL;

            // fetch the associate array
            $row = $fetched_show->fetch_assoc();

            // create the json data
            $show['ShowID'] = $row['SHOW_ID'];
            $show['Director'] = $row['Director_Name'];
            $show['YearReleased'] = $row['Year_Released'];
            $show['DateAdded'] = $row['Date_Added'];
            $show['Rating'] = $row['Rating_Type'];
            //$show['Country'] = $row['Country_Name'];
            $show['Description'] = $row['Description'];
            $show['poster_url'] = $row['poster_url'];
            $show['banner_url'] = $row['banner_url'];
            $show['type'] = $row['Type_Name'];
            $fetched_actors = select_query($conn, $CAST_SELECT_QUERY);
            $fetched_countries = select_query($conn, $COUNTRY_SELECT_QUERY);
            $fetched_genres = select_query($conn, $GENRE_SELECT_QUERY);

            // create the cast array
            $show['Cast'] = array();
            if($fetched_actors->num_rows > 0){
                
                // loop and add the actors to the array
                while($actor = $fetched_actors->fetch_assoc()) {
                    array_push($show['Cast'], $actor['Actor_Name']);
                }

            }
            
            $show['Country'] = array();
            if($fetched_countries->num_rows > 0) {

                // loop and add the countries to the array
                while($country = $fetched_countries->fetch_assoc()) {
                    array_push($show['Country'], $country['Country_Name']);
                }
            }

            $show['Genre'] = array();
            if($fetched_genres->num_rows > 0) {

                // loop and add the genres to the array
                while($genre = $fetched_genres->fetch_assoc()) {
                    array_push($show['Genre'], $genre['Genre_Name']);
                }
            }

            if($row['Type_Name'] == "TV Show") {
                $TVSHOW_SELECT_QUERY = "SELECT * FROM FT_TvShow WHERE SHOW_ID = {$showid}";
                $tvshow = select_query($conn, $TVSHOW_SELECT_QUERY);

                if($tvshow->num_rows > 0) {
                    $tvshow = $tvshow->fetch_assoc();
                    $show['Title'] = $tvshow['TVShow_Name'];
                    $show['Seasons'] = $tvshow['Seasons'];
                }
            } else if ($row['Type_Name'] == "Movie") {
                $MOVIE_SELECT_QUERY = "SELECT * FROM FT_Movie WHERE SHOW_ID = {$showid}";
                $movie = select_query($conn, $MOVIE_SELECT_QUERY);

                if($movie->num_rows > 0) {
                    $movie = $movie->fetch_assoc();
                    $show['Title'] = $movie['Movie_Name'];
                    $show['Duration'] = $movie['Movie_Duration']." min";
                }
            }
        } 
        return $show;
    }

    // get the show details if show id is given
    if($_SERVER['REQUEST_METHOD'] == "GET") {
        if(isset($_GET['showid'])) {
        
            $showid = mysqli_real_escape_string($conn, trim($_GET['showid']));
            
            $show = get_show_detailed($conn, $showid);
    
            if(count($show) == 0) {
                echo json_encode(array("data" => array()));
            } else {
                echo json_encode(array("data" => array($show)));
            }
            
        }
    }

    if($_SERVER['REQUEST_METHOD'] == "POST") {
        
        // array to store the show variables
        $show = array();

        if(isset($_POST['title'])) {
            $show['title'] = $_POST['title'];
        } else {
            echo json_decode(array("status" => "Error", "message" => "Title missing"));
        }

        if(isset($_POST['type'])) {
            $show['type'] = $_POST['type'];
        } else {
            echo json_decode(array("status" => "Error", "message" => "Show type missing"));
        }

        if(isset($_POST['director'])) {
            $show['director'] = $_POST['director'];
        } else {
            $show['director'] = NULL;
        }

        if(isset($_POST['cast'])) {
            $show['cast'] = $_POST['cast'];
        } else {
            $show['cast'] = NULL;
        }

        if(isset($_POST['country'])) {
            $show['country'] = $_POST['country'];
        } else {
            $show['country'] = NULL;
        }

        if(isset($_POST['date_added'])) {
            $show['date_added'] = $_POST['date_added'];
        } else {
            $show['date_added'] = NULL;
        }

        if(isset($_POST['release_year'])) {
            $show['release_year'] = $_POST['release_year'];
        }else {
            $show['release_year'] = NULL;
        }

        if(isset($_POST['duration'])){
            $show['duration'] = $_POST['duration'];
        } else {
            $show['duration'] = NULL;
        }

        if(isset($_POST['listed_in'])) {
            $show['listed_in'] = $_POST['listed_in'];
        } else {
            $show['listed_in'] = NULL;
        }

        if(isset($_POST['description'])) {
            $show['description'] = $_POST['description'];
        } else {
            $show['description'] = NULL;
        }

        if(isset($_POST['rating'])) {
            $show['rating'] = $_POST['rating'];
        } else {
            $show['rating'] = NULL;
        }

        // add the show
        $show_id = add_show($conn, $show);

        // echo show json
        if(empty($show_id)) {
            echo json_encode(array("status" => "Error", "Message" => "Unable to create show details"));
        } else {
            echo json_encode(array("status" => "Success", "ShowID" => $show_id));
        }
    }

    if($_SERVER['REQUEST_METHOD'] == "PUT") {
        
        // parse the input parameters into an array
        parse_str(file_get_contents("php://input"),$put_vars);

        
        if(!empty($put_vars)) {
            // print_r($put_vars);
            // if(isset($put_vars["showid"])) {
            //     echo json_encode(array("showid" => $put_vars));
            // }
            
            $show = array();
            // check if the show exists
            $show_keys = array("title", "type", "director", "cast", "country", "date_added", "release_year", "rating", "duration", "listed_in", "description", "poster_url", "banner_url");
            
            array_walk($put_vars, 'trim_value');
            
            // check if the key exists in put variables
            foreach($show_keys as $key) {
                if (array_key_exists($key, $put_vars)) {
                    $show[$key] = $put_vars[$key];
                    
                }
            }


            // clean the data
            array_walk($show, 'trim_value');

            // sanitise the data
            array_walk($show, 'real_escape_string');

            // check if the show exists
            $CHECK_SHOW_QUERY = "SELECT SHOW_ID FROM FT_Show WHERE SHOW_ID = {$show['showid']}";

            $show_id = select_query($conn, $CHECK_SHOW_QUERY);

            if($show->num_rows == 0) {
                echo json_encode(array("status" => "Error", "message" => "Show not found!"));
            } else {
                foreach($show_keys as $key) {
                    if(array_key_exists($key, $put_vars)) {
                        
                    }
                }

            }

        }
 
    }
    
?>