<?php
// if the file is accessed manually and not post
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    // make an error response
    exit("Error. Not a POST request");
}

// get post values
$nitrogenReading = $_POST['inputNitrogenReading'];
$phosphorusReading = $_POST['inputPhosphorusReading'];
$potassiumReading = $_POST['inputPotassiumReading'];

// check if there is empty input of data
if ( empty($nitrogenReading) || empty($phosphorusReading) || empty($potassiumReading)){
    // make an error response
    exit("Error. All sensor values are required");
};

// access database
$mysqli = require_once "./database.php";

// try to create and catch if there is error
try{
    // make a string of sql to put the weight reading to database
    $sql = "UPDATE `connection`
        SET `nitrogen` = ?,
            `phosphorus` = ?,
            `potassium` = ?
        WHERE `id` = 1;";
    
    // prepare the statement
    $stmt = $mysqli -> prepare ($sql);

    // bind the parameters to the statement
    $stmt -> bind_param ('ddd', $nitrogenReading, $phosphorusReading, $potassiumReading);

    // execute the statement
    $stmt -> execute();

    // close statement and database
    $stmt -> close();
    $mysqli -> close();

    // make a success response
    exit ("Successfully input the NPK value to the database");
}
// if there is error in query
catch (Exception $e){
    // make an error response
    exit ( "Error No: ". $e->getCode() ." - ". $e->getMessage() );
}


?>