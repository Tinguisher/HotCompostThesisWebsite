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

    // make a string of sql to check if there is compost already in layering process
    $sql = "SELECT *
        FROM `hotcompost`
        WHERE status LIKE 'Layering'
        LIMIT 1;";

    // prepare the statement
    $stmt = $mysqli -> prepare ($sql);

    // execute the statement
    $stmt -> execute();

    // get the result from the statement
    $result = $stmt -> get_result();

    // get only one from the executed statement
    $compostLayering = $result->fetch_assoc();

    // close statement and free the result
    $stmt->close();
    $result->free();

    // if there is no compost that is being layered, create new one
    if (!$compostLayering) {
        // make a string of sql to create a new connection for less error
        $sql = "INSERT INTO `hotcompost`
                (`status`)
                VALUES ('Layering');";

        // prepare the statement
        $stmt = $mysqli->prepare($sql);

        // execute the statement
        $stmt->execute();

        // close the statement
        $stmt->close();
    }

    // get the material name that will be put by the user
    include './GetMaterialProcess.php';

    // check if the bottom layer is already mixed
    include './GetLayerProcess.php';

    // check if there is top most layer already
    include './GetTopLayerProcess.php';

    // get the weight of brown and green that is in layering
    include './GetWeightRatioLayeringProcess.php';

    // make a success response and proceed to create with the next layer to be input
    $response = [
        'status' => "success",
        'message' => "Create",
        'material' => $layer['material'],
        'mix' => $layer['mix'],
        'finish' => $layer['finish'],
        'topLayer' => $compostTopLayer ? true : false,
        'brownWeight' => $compostWeight[0]['weight'] ?? 0,
        'greenWeight' => $compostWeight[1]['weight'] ?? 0
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