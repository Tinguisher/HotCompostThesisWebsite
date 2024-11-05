<?php
// access database
$mysqli = require_once "./database.php";

// try to create and catch if there is error
try{
    // make a string of sql to get hot compost in progress
    $sql = "SELECT *
        FROM `hotcompost`
        WHERE status LIKE 'In Progress'
        LIMIT 1;";

    // prepare the statement
    $stmt = $mysqli -> prepare ($sql);

    // execute the statement
    $stmt -> execute();

    // get the result from the statement
    $result = $stmt -> get_result();

    // get only one from the executed statement
    $hotCompost = $result -> fetch_assoc();

    // make a string of sql to check hot compost in progress and date to finish within 18 days
    $sql = "SELECT *
        FROM `sensor`, `hotcompost`
        WHERE sensor.hotcompost_id = hotcompost.id
            AND hotcompost.id = ?
            AND hotcompost.status LIKE 'In Progress'
            AND hotcompost.createdAt < now() - interval 18 day;";

    // prepare the statement
    $stmt = $mysqli -> prepare ($sql);

    // bind the parameters to the statement
    $stmt -> bind_param ('i', $hotCompost['id']);

    // execute the statement
    $stmt -> execute();

    // get the result from the statement
    $result = $stmt -> get_result();

    // get only one from the executed statement
    $hotCompostDone = $result -> fetch_assoc();

    // if there is no hot compost that is in 18 days, exit the status or none if there is no in progress
    if (!$hotCompostDone) exit( $hotCompost['status'] ?? "None" );

    // if there is hot compost that is in 18 days, make the status as completed
    // make a string of sql to update the hot compost to completed
    $sql = "UPDATE `hotcompost`
        SET `status` = 'Completed'
        WHERE status = 'In Progress'
            AND id = ?;";
            
    // prepare the statement
    $stmt = $mysqli -> prepare ($sql);

    // bind the parameters to the statement
    $stmt -> bind_param ('i', $hotCompost['id']);

    // execute the statement
    $stmt -> execute();

    // close statement and database free the result
    $stmt -> close();
    $result -> free();
    $mysqli -> close();

    // exit None since there is no more hot compost in progress
    exit("None");
}
// if there is error in query
catch (Exception $e){
    // make an error response
    exit ( "Error No: ". $e->getCode() ." - ". $e->getMessage() );
}

?>