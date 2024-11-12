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
$topBrown = $data['input_topBrown'];

// check if there is empty input of data
if ( empty($topBrown) ) {
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

// create a sql to update the weight and request for the last part to mist
$sql = "UPDATE `layer`
    SET `weight` = ?
    WHERE hotcompost_id = (
            SELECT id
            FROM `hotcompost`
            WHERE status LIKE 'Layering'
            LIMIT 1
        )
        AND `part` LIKE 'Top Layer';";

// try to get and catch if there is error
try{
    // prepare the statement
    $stmt = $mysqli -> prepare ($sql);

    // bind the parameters to the statement
    $stmt -> bind_param ('d', $topBrown);

    // execute the statement
    $stmt -> execute();

    // close statement
    $stmt -> close();

    // if there is hot compost in progress, create, otherwise reply in progress
    $response = [
        'status' => "success",
        'message' => "You have successfully updated the top brown compost and now requesting to be misted in esp32"
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