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
                VALUES (1, 0, 'Weight')";

        // prepare the statement
        $stmt = $mysqli->prepare($sql);

        // execute the statement
        $stmt->execute();

        // close the statement
        $stmt->close();

        // the weight of the connection will be 0 at initial insert
        $connection['weight'] = 0;
    }

    // make a sql to update the request to Weight
    $sql = "UPDATE `connection`
    SET `request` = 'Weight'
    WHERE `id` = 1;";

    // prepare the statement
    $stmt = $mysqli->prepare($sql);

    // execute the statement
    $stmt->execute();

    // close the statement
    $stmt->close();

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