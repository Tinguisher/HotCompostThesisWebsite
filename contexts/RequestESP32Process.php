<?php
// all contents to read as json
header('Content-Type: application/json; charset=utf-8');

// if the file is accessed manually and not post
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    // make an error response
    exit("Error. Not a POST request");
}

// get raw input from the form instead of json
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// get post values
$request = $data['request'];

// access database
$mysqli = require_once "./database.php";

// try to create and catch if there is error
try{
    // make a string of sql to update part to MixRequest
    $sql = "UPDATE `connection`
        SET `request` = ?
        WHERE `id` = 1;";

    // prepare the statement
    $stmt = $mysqli->prepare($sql);

    // bind the parameters to the statement
    $stmt -> bind_param ('s', $request);

    // execute the statement
    $stmt->execute();

    // close the statement
    $stmt->close();

    // make a success response and give the esp32 to water
    $response = [
        'status' => "success",
        'message' => "Requested for esp32"
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