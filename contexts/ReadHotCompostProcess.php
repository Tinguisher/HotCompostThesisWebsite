<?php
// access database
$mysqli = require_once "./database.php";

// try to create and catch if there is error
try{
    // make a string of sql to get hot compost in progress
    $sql = "SELECT *
        FROM `hotcompost`
        WHERE status IN ('In Progress', 'Mixing')
        LIMIT 1;";

    // prepare the statement
    $stmt = $mysqli -> prepare ($sql);

    // execute the statement
    $stmt -> execute();

    // get the result from the statement
    $result = $stmt -> get_result();

    // get only one from the executed statement
    $hotCompost = $result -> fetch_assoc();

    // if there is no hot compost in progress, exit
    if (!$hotCompost) exit("None");

    // if the status is mixing, check if mixing can be read only data again
    if ($hotCompost['status'] == "Mixing") include './UpdateMixToInProgProcess.php';

    // make a string of sql to check hot compost in progress and date to finish within 18 days
    $sql = "SELECT *
        FROM `hotcompost`
        WHERE id = ?
            AND status LIKE 'In Progress'
            AND createdAt < now() - interval 18 day;";

    // prepare the statement
    $stmt = $mysqli -> prepare ($sql);

    // bind the parameters to the statement
    $stmt -> bind_param ('i', $hotCompost['id']);

    // execute the statement
    $stmt -> execute();

    // get the result from the statement
    $result = $stmt -> get_result();

    // get only one from the executed statement
    $hotCompostDone = $result -> fetch_assoc();

    // if hot compost can be finish, exit by updating to completed in database
    if ($hotCompostDone) include './UpdateInProgToCompProcess.php';

    // make a string of sql to check if the compost is not yet mixed
    $sql = "SELECT *
        FROM `hotcompost`
        WHERE id = ?
            AND status LIKE 'In Progress'
            AND createdAt < now() - interval 4 day
            AND lastMixed < now() - interval 2 day;";

    // prepare the statement
    $stmt = $mysqli -> prepare ($sql);

    // bind the parameters to the statement
    $stmt -> bind_param ('i', $hotCompost['id']);

    // execute the statement
    $stmt -> execute();

    // get the result from the statement
    $result = $stmt -> get_result();

    // get only one from the executed statement
    $hotCompostNotMixed = $result -> fetch_assoc();

    // if hot compost is not yet mixed, update the status to mixing
    if ($hotCompostNotMixed) include './UpdateInProgToMixProcess.php';
    
    // if hot compost is in progress, exit its status
    exit ( $hotCompost['status'] );
}
// if there is error in query
catch (Exception $e){
    // make an error response
    exit ( "Error No: ". $e->getCode() ." - ". $e->getMessage() );
}

?>