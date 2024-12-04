<?php
// all contents to read as json
header('Content-Type: application/json; charset=utf-8');

// access database
$mysqli = require_once "./database.php";

// try to get and catch if there is error
try{
    // create a sql to get most recent reading of sensors that are in progress
    $sql = "SELECT hotcompost.id,
            hotcompost.status,
            sensor.moisturePercent,
            sensor.temperatureCelsius,
            sensor.time
        FROM `hotcompost`,
            `sensor`
        WHERE hotcompost.id = sensor.hotcompost_id
        AND hotcompost.status IN ('In Progress', 'Mixing')
        ORDER BY sensor.time DESC
        LIMIT 1";

    // prepare the statement
    $stmt = $mysqli -> prepare ($sql);

    // execute the statement
    $stmt -> execute();

    // get the result from the statement
    $result = $stmt -> get_result();

    // get one values from the database
    $hotCompostReading = $result -> fetch_assoc();

    // free data and close statement
    $result -> free();
    $stmt -> close();

    // create a sql to get notifications of hot compost
    $sql = "SELECT notification.type,
            notification.time
        FROM `hotcompost`,
            `notification`
        WHERE hotcompost.id = notification.hotcompost_id
        AND hotcompost.status IN ('In Progress', 'Mixing')
        ORDER BY notification.time DESC;";
        
    // prepare the statement
    $stmt = $mysqli -> prepare ($sql);

    // execute the statement
    $stmt -> execute();

    // get the result from the statement
    $result = $stmt -> get_result();

    // get all from the executed statement
    $hotCompostNotifications = $result -> fetch_all( MYSQLI_ASSOC );

    // response message is dependent if there is hot compost in progress
    $response = $hotCompostReading ? [
        'status' => "success",
        'message' => "Read",
        'sensor' => $hotCompostReading,
        'notifications' => $hotCompostNotifications
    ] : [
        'status' => "success",
        'message' => "Create"
    ];

    // free data and close statement
    $result -> free();
    $stmt -> close();

    // refresh the request of web to esp32
    include './RequestNoneProcess.php';
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