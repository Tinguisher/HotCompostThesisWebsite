<?php
// access database
$mysqli = require_once "./database.php";

// try to create and catch if there is error
try{
    // make a string of sql to update layering into in progress
    $sql = "UPDATE `hotcompost`
    SET `status` = 'In Progress',
        `lastMixed` = now()
    WHERE status = 'Layering';";

    // prepare the statement
    $stmt = $mysqli -> prepare ($sql);

    // execute the statement
    $stmt -> execute();

    // make a string of sql to update layering into in progress
    $sql = "UPDATE `layer`
    SET `part` = 'MistingDone'
    WHERE part = 'MistingAccepted';";

    // prepare the statement
    $stmt = $mysqli -> prepare ($sql);

    // execute the statement
    $stmt -> execute();
    
    // if hot compost is in progress, exit its status
    exit ( "Done the layering, starting the automated compost now" );
}
// if there is error in query
catch (Exception $e){
    // make an error response
    exit ( "Error No: ". $e->getCode() ." - ". $e->getMessage() );
}
?>