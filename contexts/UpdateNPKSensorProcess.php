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
$pHReading = $_POST['inputPHReading'];

// access database
$mysqli = require_once "./database.php";

// try to create and catch if there is error
try{
    // make a string of sql to put the weight reading to database
    $sql = "UPDATE `connection`
        SET `nitrogen` = ?,
            `phosphorus` = ?,
            `potassium` = ?,
            `ph` = ?
        WHERE `id` = 1;";
    
    // prepare the statement
    $stmt = $mysqli -> prepare ($sql);

    // bind the parameters to the statement
    $stmt -> bind_param ('dddd', $nitrogenReading, $phosphorusReading, $potassiumReading, $pHReading);

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