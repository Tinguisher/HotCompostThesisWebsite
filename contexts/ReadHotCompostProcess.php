<?php
// access database
$mysqli = require_once "./database.php";

// try to create and catch if there is error
try{
    // make a string of sql
    $sql = "SELECT *
        FROM `hotcompost`
        WHERE status NOT LIKE 'Completed';";

    // prepare the statement
    $stmt = $mysqli -> prepare ($sql);

    // execute the statement
    $stmt -> execute();

    // get the result from the statement
    $result = $stmt -> get_result();

    // get all values from the executed statement
    $hotCompost = $result -> fetch_all( MYSQLI_ASSOC );

    // if there is already hot compost in progress, return "In Progress", else return "None"
    echo $hotCompost ? "In Progress" : "None";
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