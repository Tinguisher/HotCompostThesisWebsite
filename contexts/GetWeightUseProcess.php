<?php
// all contents to read as json
header('Content-Type: application/json; charset=utf-8');

// process of requesting weight to the esp32
function requestWeight () {
    // access the database outside function
    global $mysqli;

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

    // make a success response and proceed to create
    $response = [
        'status' => "success",
        'message' => "Create"
    ];

    // return the response
    return ($response);
}

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

    // free data and close statement
    $result -> free();
    $stmt -> close();

    // if there is hot compost in progress, create, otherwise reply in progress
    $response = $hotCompostInProgress ? [
        'status' => "success",
        'message' => "In Progress"
    ] : requestWeight();
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