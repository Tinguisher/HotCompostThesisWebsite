<?php
// access database
$mysqli = require_once "./database.php";

// try to create and catch if there is error
try{
    // make a string of sql to get hot compost in progress
    $sql = "SELECT *
        FROM `layer`
        WHERE hotcompost_id = (
            SELECT id
                FROM `hotcompost`
                WHERE status LIKE 'Layering'
                LIMIT 1
            )
            AND layer.part IN ('Bottom Misting Request', 'Top Misting Request')
        LIMIT 1;";

    // prepare the statement
    $stmt = $mysqli -> prepare ($sql);

    // execute the statement
    $stmt -> execute();

    // get the result from the statement
    $result = $stmt -> get_result();

    // get only one from the executed statement
    $hotCompostRequested = $result -> fetch_assoc();
    
    // if there is no hot compost that is requesting, exit none
    if (!$hotCompostRequested) exit("None");

    // if there are request from parts or, update to accepted
    if ($hotCompostRequested['part'] == "Bottom Misting Request") include './UpdateBottomRequestToAccepted.php';
    if ($hotCompostRequested['part'] == "Top Misting Request") include './UpdateTopRequestToAccepted.php';
}
// if there is error in query
catch (Exception $e){
    // make an error response
    exit ( "Error No: ". $e->getCode() ." - ". $e->getMessage() );
}
?>