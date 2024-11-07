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
            AND layer.part IN ('MixRequest', 'MistingRequest', 'MixingAccepted', 'MistingAccepted')
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
    if ($hotCompostRequested['part'] == "MixRequest") include './RequestLayerMistMixProcess.php';
    if ($hotCompostRequested['part'] == "MistingRequest") include './RequestLayerMistProcess.php';
    if ($hotCompostRequested['part'] == "MixingAccepted") include './ReadLayeringTimeProcess.php';

    // if hot compost is in mixting accepted, exit its status
    exit ( $hotCompost['status'] );
}
// if there is error in query
catch (Exception $e){
    // make an error response
    exit ( "Error No: ". $e->getCode() ." - ". $e->getMessage() );
}
?>