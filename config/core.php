<?php
    // Set global variables for the api

    $HOME_URL =  "http://". $_SERVER['SERVER_NAME']. "flixtime/api/";

    // Add the encryption key for Basic Authentication
    $SECRET_KEY = "SLKDFJAL;L;288RUF;ASL6G";

    // Implementing log feature in future versions
    //$LOG_DIRECTORY = "";

    //TMDB API key V3 Auth
    $TMDB_API_KEY = "60c346e259ff93cf8f32f57bd0857505";

    //TMDB API KEY V4 Auth
    $TMDB_API_KEY_V4 = "eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI2MGMzNDZlMjU5ZmY5M2NmOGYzMmY1N2JkMDg1NzUwNSIsInN1YiI6IjYwODdmMjQxNTVjOTI2MDA1OTJmNjI1MiIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.soDrjpf5MMrt0lhegJFk8HXoCQ2TIKnGTVZVAqTsM5Q";

    // Search url for tmdb api
    $TMDB_SEARCH_URL = "https://api.themoviedb.org/3/search/movie?api_key=". $TMDB_API_KEY ."&query=";

    // Image fetching url for tmdb api
    $TMDB_IMAGE_URL = "http://image.tmdb.org/t/p/original";


?>