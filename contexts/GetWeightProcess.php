<?php
// all contents to read as json
header('Content-Type: application/json; charset=utf-8');

// access database
$mysqli = require_once "./database.php";

// try to get and catch if there is error
try{
    // make a string of sql to check web request to esp32
    $sql = "SELECT *
        FROM `connection`
        WHERE id = 1
        LIMIT 1;";
    // prepare the statement
    $stmt = $mysqli -> prepare ($sql);

    // execute the statement
    $stmt -> execute();

    // get the result from the statement
    $result = $stmt -> get_result();

    // get only one from the executed statement
    $connection = $result->fetch_assoc();

    // close statement and free the result
    $stmt->close();
    $result->free();

    $sql = "SELECT DISTINCT(material), SUM(layer.weight) AS weight
        FROM `hotcompost`,
            `layer`
        WHERE hotcompost.id = layer.hotcompost_id
            AND hotcompost.status LIKE 'Layering'
        GROUP BY material;";























    // make a success response and send the weight from the server
    $response = [
        'status' => "success",
        'message' => "Successfully got the new weight value",
        'weight' => $connection['weight']
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