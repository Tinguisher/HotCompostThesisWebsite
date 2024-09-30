<?php
// access database
$mysqli = require_once "./database.php";

// try to create and catch if there is error
try{
    // make a string of sql to check latest hot compost made
    $sql = "SELECT *
        FROM `hotcompost`
        ORDER BY createdAt DESC
        LIMIT 1;";

    // prepare the statement
    $stmt = $mysqli -> prepare ($sql);

    // execute the statement
    $stmt -> execute();

    // get the result from the statement
    $result = $stmt -> get_result();

    // get only one from the executed statement
    $hotCompost = $result -> fetch_assoc();

    // close statement and database free the result
    $stmt -> close();
    $result -> free();
    $mysqli -> close();

    // exit the status of the most recent hot compost
    exit( $hotCompost['status'] == "Completed" ? "None" : $hotCompost['status'] );
}
// if there is error in query
catch (Exception $e){
    // make an error response
    exit ( "Error No: ". $e->getCode() ." - ". $e->getMessage() );
}

?>