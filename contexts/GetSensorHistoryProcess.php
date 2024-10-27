<?php
// all contents to read as json
header('Content-Type: application/json; charset=utf-8');

// access database
$mysqli = require_once "./database.php";

// get the id in the session
session_start();

$sql = "SELECT sensor.moisturePercent,
        sensor.temperatureCelsius,
        sensor.time
    FROM `hotcompost`,
        `sensor`
    WHERE hotcompost.id = sensor.hotcompost_id
        AND hotcompost.id = ?;";

// try to create and catch if there is error
try{
    // prepare the statement
    $stmt = $mysqli -> prepare ($sql);

    // bind the parameters to the statement
    $stmt -> bind_param ('i', $_GET['compostID']);

    // execute the statement
    $stmt -> execute();

    // get the result from the statement
    $result = $stmt -> get_result();

    // get only one from the executed statement
    $compost = $result -> fetch_all( MYSQLI_ASSOC );

    // free data, close the statement
    $result -> free();
    $stmt -> close();

    // make a success response
    $response = [
        'status' => "success",
        'message' => "Successfully got the sensor data of the compostID",
        'compost' => $compost
    ];
}
// if there is error in query
catch (Exception $e){
    // make an error response
    $response = [
        'status' => "error",
        'message' => "Error No: ". $e->getCode() ." - ". $e->getMessage()    // get error code and message
    ];
}

// close the database
$mysqli -> close();

// return the response as json to the history_reading.js
exit ( json_encode($response) );

?>