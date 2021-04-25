<?php

    // Include the database connection file

    include("../config/database.php");
    
    // Upload the file with the front-end app to the server
    // use file_get_contents($endpoint) to then fetch the file
    $endpoint = "netflix_titles.csv";

    $file = file($endpoint);

    //$lines = explode(PHP_EOL, $file);

    $rows = array_map('str_getcsv', $file);
    $header = array_shift($rows);
    $csv = array();

    //$array = $fields = array();
    /**
     * Allow the admin user to populate the database with csv files
     * 
     * - Read data from the file
     * - For each entry: 
     *  - Check for the data quality
     *  - Call the add() function 
     * 
     *  */ 

    // Create the array

    foreach($rows as $row) {
        if (count($header) == count($row)) {
            $csv[] = array_combine($header, $row);
        }
    }

   // Iterate through the data to upload
    foreach($csv as $row) {
        if(empty($row('title'))) {
            continue; // skip this movie and move to the next
        } else {
            include_once('show.php');
            add_show($conn,$row);
        }
    }

    //echo("Length: ". count($csv)."\n");
    //print_r($csv);
     
?>