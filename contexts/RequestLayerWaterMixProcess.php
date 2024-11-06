<?php
// all contents to read as json
header('Content-Type: application/json; charset=utf-8');

// access database
$mysqli = require_once "./database.php";

// try to create and catch if there is error
try{
    // make a string of sql to update part to MixRequest
    $sql = "UPDATE `layer`
    SET `part` = 'MixRequest'
    WHERE `part` = 'Bottom'
        AND hotcompost_id = (
            SELECT id
                FROM `hotcompost`
                WHERE status LIKE 'Layering'
                LIMIT 1
            );";

    // prepare the statement
    $stmt = $mysqli->prepare($sql);

    // execute the statement
    $stmt->execute();

    // close the statement
    $stmt->close();

    // make a string of sql to update part to MixRequest
    $sql = "UPDATE `hotcompost`
        SET `lastMixed` = now()
        WHERE status = 'Layering';";

    // prepare the statement
    $stmt = $mysqli->prepare($sql);

    // execute the statement
    $stmt->execute();

    // close the statement
    $stmt->close();

    // make a success response and give the new material to be input by the user
    $response = [
        'status' => "success",
        'message' => "Requested for esp32 to water and mix"
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