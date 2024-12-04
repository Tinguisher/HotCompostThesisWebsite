<?php
// all contents to read as json
header('Content-Type: application/json; charset=utf-8');

// access database
$mysqli = require_once "./database.php";

// create sql to update in progress and mixing into cancelled
$sql = "UPDATE `hotcompost`
    SET `status` = 'Cancelled'
    WHERE `status` IN ('In Progress', 'Mixing');";

// try to get and catch if there is error
try{
    // prepare the statement
    $stmt = $mysqli -> prepare ($sql);

    // execute the statement
    $stmt -> execute();

    // close statement
    $stmt -> close();

    // if there is hot compost in progress, create, otherwise reply in progress
    $response = [
        'status' => "success",
        'message' => "Cancelled the compost"
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

// return the response as json
exit ( json_encode($response) );
?>