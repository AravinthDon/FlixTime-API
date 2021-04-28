<?php
    // to make the content type json
    header('Content-Type: application/json');

    // include the database config
    include("../config/database.php");
    

    function get_show_detailed($conn, $showid) {
        // include the db utilities file
        include("../utilities/db.php");
        // to store the array
        $show = array();

        // building the select query
        $SHOW_SELECT_QUERY = "SELECT FT_Show.SHOW_ID, FT_Director.Director_Name, Year_Released, Date_Added, FT_Rating.Rating_Type, FT_Country.Country_Name,FT_ShowType.Type_Name, FT_Show.Description 
                            FROM FT_Show 
                            LEFT JOIN FT_Director ON FT_Show.DIRECTOR_ID = FT_Director.DIRECTOR_ID 
                            LEFT JOIN FT_Rating ON FT_Show.RATING_ID = FT_Rating.RATING_ID 
                            LEFT JOIN FT_Country ON FT_Show.COUNTRY_ID = FT_Country.Country_Name 
                            LEFT JOIN FT_ShowType ON FT_Show.SHOWTYPE_ID = FT_ShowType.SHOWTYPE_ID 
                            WHERE SHOW_ID = {$showid}";
        
        $fetched_show = select_query($conn, $SHOW_SELECT_QUERY);

        if($fetched_show->num_rows > 0) {
            // Add the title first
            $show['Title'] = NULL;

            // fetch the associate array
            $row = $fetched_show->fetch_assoc();

            // create the json data
            $show['Director'] = $row['Director_Name'];
            $show['Year Released'] = $row['Year_Released'];
            $show['Date Added'] = $row['Date_Added'];
            $show['Rating'] = $row['Rating_Type'];
            $show['Country'] = $row['Country_Name'];
            $show['Description'] = $row['Description'];

            if($row['Type_Name'] == "TV Show") {
                $TVSHOW_SELECT_QUERY = "SELECT * FROM FT_TvShow WHERE SHOW_ID = {$showid}";
                $tvshow = select_query($conn, $TVSHOW_SELECT_QUERY);

                if($tvshow->num_rows > 0) {
                    $tvshow = $tvshow->fetch_assoc();
                    $show['Title'] = $tvshow['TVShow_name'];
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
        } else {
            $show['Message'] = "No Shows Found";
        }

        return $show;
    }

    // get the show details if show id is given
    if(isset($_GET['showid'])) {
        
        $showid = mysqli_real_escape_string($conn, $_GET['showid']);
        
        $show = get_show_detailed($conn, $showid);

        echo json_encode($show);
    }
?>