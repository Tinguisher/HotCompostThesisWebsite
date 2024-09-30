<?php
// access database
$mysqli = require_once "./database.php";

// try to create and catch if there is error
try{
    // make a string of sql
    $sql = "SELECT *
        FROM `sensor`, `hotcompost`
        WHERE sensor.hotcompost_id = (
            SELECT id
                FROM `hotcompost`
                WHERE status LIKE 'In Progress'
                ORDER BY createdAt DESC
                LIMIT 1
            )
            AND sensor.time > now() - interval 1 hour;";

    // prepare the statement
    $stmt = $mysqli -> prepare ($sql);

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