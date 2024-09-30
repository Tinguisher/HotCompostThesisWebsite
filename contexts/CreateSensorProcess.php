<?php
// if the file is accessed manually and not post
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    // make an error response
    exit("Error. Not a POST request");
}

// get post values
$moisturePercent = $_POST['inputMoisturePercent'];
$temperatureCelsius = $_POST['inputTemperatureCelsius'];

// check if there is empty input of data
if ( empty($moisturePercent) || empty($temperatureCelsius)) {
    // make an error response
    exit("Error. All sensor values are required");
};

// access database
$mysqli = require_once "./database.php";

// try to create and catch if there is error
try{
    // make a string of sql to check latest hot compost made
    $sql_getHotCompost = "SELECT *
        FROM `hotcompost`
        ORDER BY createdAt DESC
        LIMIT 1;";
    
    // prepare the statement
    $stmt = $mysqli -> prepare ($sql_getHotCompost);

    // execute the statement
    $stmt -> execute();

    // get the result from the statement
    $result = $stmt -> get_result();

    // get only one from the executed statement
    $hotCompost = $result -> fetch_assoc();

    // if the hot compost that is not "In Progress", exit
    if ($hotCompost['status'] != "In Progress") exit();

    // make a string of sql to insert sensor data
    $sql_insertSensorData = "INSERT INTO `sensor`
            (`hotcompost_id`, `moisturePercent`, `temperatureCelsius`)
        VALUES (?, ?, ?);";

    // prepare the statement
    $stmt = $mysqli -> prepare ($sql_insertSensorData);

    // bind the parameters to the statement
    $stmt -> bind_param ('idd', $hotCompost['id'], $moisturePercent, $temperatureCelsius);

    // execute the statement
    $stmt -> execute();

    // close statement and database free the result
    $stmt -> close();
    $result -> free();
    $mysqli -> close();

    // make a success response
    exit ("Successfully input the sensor values into the database.");
}
// if there is error in query
catch (Exception $e){
    // make an error response
    exit ( "Error No: ". $e->getCode() ." - ". $e->getMessage() );
}


?>