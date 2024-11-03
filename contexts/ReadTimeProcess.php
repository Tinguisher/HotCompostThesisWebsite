<?php
// access database
$mysqli = require_once "./database.php";

// try to create and catch if there is error
try{
    // make a string of sql to check latest hot compost made
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

    // if there is no hot compost in progress, exit
    if (!$hotCompost) exit("There seems to be no in progress or it is mixing");

    // make a string of sql to check if there is sensor reading within an hour
    $sql = "SELECT *
        FROM `sensor`, `hotcompost`
        WHERE sensor.hotcompost_id = hotcompost.id
            AND hotcompost.id = ?
            AND sensor.time > now() - interval 1 hour;";

    // prepare the statement
    $stmt = $mysqli -> prepare ($sql);

    // bind the parameters to the statement
    $stmt -> bind_param ('i', $hotCompost['id']);

    // execute the statement
    $stmt -> execute();

    // get the result from the statement
    $result = $stmt -> get_result();

    // get only one from the executed statement
    $sensorInterval = $result -> fetch_assoc();

    // close statement and database and free the result
    $stmt -> close();
    $result -> free();
    $mysqli -> close();

    // if there is already read within an hour, return "read done", else return "request read"
    exit ( $sensorInterval ? "read done" : "request read" );
}
// if there is error in query
catch (Exception $e){
    // make an error response
    exit ( "Error No: ". $e->getCode() ." - ". $e->getMessage() );
}

?>