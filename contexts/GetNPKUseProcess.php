<?php
// all contents to read as json
header('Content-Type: application/json; charset=utf-8');

// access database
$mysqli = require_once "./database.php";

// make a string of sql to check web request to esp32
$sql = "SELECT *
    FROM `connection`
    WHERE id = 1
    LIMIT 1;";

// try to get and catch if there is error
try{
    // prepare the statement
    $stmt = $mysqli -> prepare ($sql);

    // execute the statement
    $stmt -> execute();

    // get the result from the statement
    $result = $stmt -> get_result();

    // get only one from the executed statement
    $connection = $result->fetch_assoc();

    // close statement and database free the result
    $stmt->close();
    $result->free();

    // if the request is empty, make a new one
    if (!$connection) {
        // make a string of sql to create a new connection for less error
        $sql = "INSERT INTO `connection`
                (`id`, `weight`, `request`)
                VALUES (1, 0, 'NPK')";

        // prepare the statement
        $stmt = $mysqli->prepare($sql);

        // execute the statement
        $stmt->execute();

        // close the statement
        $stmt->close();
    }

    // make a sql to update the request to NPK
    $sql = "UPDATE `connection`
    SET `request` = 'NPK'
    WHERE `id` = 1;";

    // prepare the statement
    $stmt = $mysqli->prepare($sql);

    // execute the statement
    $stmt->execute();

    // close the statement
    $stmt->close();

    // make a success response and proceed to check new npk
    $response = [
        'status' => "success",
        'message' => "Requesting the NPK values"
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