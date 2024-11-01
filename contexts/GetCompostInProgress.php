<?php
// all contents to read as json
header('Content-Type: application/json; charset=utf-8');

// access database
$mysqli = require_once "./database.php";

// create a sql to check there is no hotcompost in progress
$sql = "SELECT *
    FROM `hotcompost`
    WHERE status IN ('In Progress', 'Mixing')
    ORDER BY createdAt DESC
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
    $hotCompostInProgress = $result -> fetch_assoc();

    // if there is hot compost in progress, create, otherwise reply in progress
    $response = $hotCompostInProgress ? [
        'status' => "success",
        'message' => "Create"
    ] : [
        'status' => "success",
        'message' => "In Progress"
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