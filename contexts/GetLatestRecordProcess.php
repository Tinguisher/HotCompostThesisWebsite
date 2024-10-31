<?php
// all contents to read as json
header('Content-Type: application/json; charset=utf-8');

// access database
$mysqli = require_once "./database.php";

// create a sql to get most recent reading of sensors
$sql = "SELECT hotcompost.id,
        sensor.moisturePercent,
        sensor.temperatureCelsius,
        sensor.time
    FROM `hotcompost`,
        `sensor`
    WHERE hotcompost.status NOT LIKE 'Completed'
    ORDER BY sensor.time DESC
    LIMIT 1";

// try to get and catch if there is error
try{
    // prepare the statement
    $stmt = $mysqli -> prepare ($sql);

    // execute the statement
    $stmt -> execute();

    // get the result from the statement
    $result = $stmt -> get_result();

    // get one values from the database
    $hotCompostReading = $result -> fetch_assoc();

    // response message is dependent if there is hot compost in progress
    $response = $hotCompostReading ? [
        'status' => "success",
        'message' => "Read",
        'sensor' => $hotCompostReading
    ] : [
        'status' => "success",
        'message' => "Create"
    ];

    // free data and close statement
    $result -> free();
    $stmt -> close();

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

// return the response as json
exit ( json_encode($response) );

?>