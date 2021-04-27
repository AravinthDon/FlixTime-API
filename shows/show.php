<?php
    // to make the content type json
    header('Content-Type: application/json');

    // include the database config
    include("../config/database.php");
    include("../utilities/db.php");

    if(isset($_GET['showid'])) {
        
        $showid = mysqli_real_escape_string($conn, $_GET['showid']);
        // building the select query
        $SHOW_SELECT_QUERY = "SELECT * FROM FT_Show WHERE SHOW_ID = {$showid}";
        
    }
?>