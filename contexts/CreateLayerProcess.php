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
$weightValue = $data['input_weightValue'];

// check if there is empty input of data
if ( empty($weightValue) ) {
    // make an error response
    $response = [
        'status' => "error",
        'message' => "All fields are required"
    ];
    // return the response to the web
    exit ( json_encode($response) );
};

// access database
$mysqli = require_once "./database.php";

// try to create and catch if there is error
try{
    // get the material name that will be put by the user
    include './GetMaterialProcess.php';

    // make a string of sql to create a new connection for less error
    $sql = "INSERT INTO `layer`
    (`hotcompost_id`, `material`, `weight`)
    VALUES ((
        SELECT id
            FROM `hotcompost`
            WHERE status LIKE 'Layering'
            LIMIT 1
        ), ?, ?);";

    // prepare the statement
    $stmt = $mysqli->prepare($sql);

    // bind the parameters to the statement
    $stmt -> bind_param ('sd', $layer['material'], $weightValue);

    // execute the statement
    $stmt->execute();

    // close the statement
    $stmt->close();

    // get the material name that will be put by the user
    include './GetMaterialProcess.php';

    // make a success response and give the new material to be input by the user
    $response = [
        'status' => "success",
        'message' => "Create",
        'material' => $layer['material']
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