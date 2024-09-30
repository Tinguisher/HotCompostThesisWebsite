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
    // make a string of sql
    $sql = "INSERT INTO `sensor`
            (`hotcompost_id`, `moisturePercent`, `temperatureCelsius`)
        VALUES ( (
            SELECT id
                FROM `hotcompost`
                WHERE status LIKE 'In Progress'
            ORDER BY createdAt DESC
            LIMIT 1
            ), ?, ?);";

    // prepare the statement
    $stmt = $mysqli -> prepare ($sql);

    // bind the parameters to the statement
    $stmt -> bind_param ('dd', $moisturePercent, $temperatureCelsius);

    // execute the statement
    $stmt -> execute();

    // make a success response
    echo "Successfully input the sensor values into the database.";
}
// if there is error in query
catch (Exception $e){
    // make an error response
    echo "Error No: ". $e->getCode() ." - ". $e->getMessage();
}

// close statement and database
$stmt -> close();
$mysqli -> close();

?>