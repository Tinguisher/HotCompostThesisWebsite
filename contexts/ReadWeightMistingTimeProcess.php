<?php
// access database
$mysqli = require_once "./database.php";

// try to create and catch if there is error
try{
    // make a string of sql to get hot compost in progress
    $sql = "SELECT SUM(weight) AS total_weight
        FROM `layer`
        WHERE hotcompost_id = (
            SELECT id
                FROM `hotcompost`
                WHERE status LIKE 'Layering'
                LIMIT 1
            )
            AND part LIKE '%Misting Accepted';";

    // prepare the statement
    $stmt = $mysqli -> prepare ($sql);

    // execute the statement
    $stmt -> execute();

    // get the result from the statement
    $result = $stmt -> get_result();

    // get only one from the executed statement
    $mistingTime = $result -> fetch_assoc();

    // 3kg into 1 minute or 3000 * 20
    $mistingRequestTime = $mistingTime['total_weight'] * 20;
    
    // if hot compost is in mixting accepted, exit its status
    exit ( $mistingRequestTime );
}
// if there is error in query
catch (Exception $e){
    // make an error response
    exit ( "Error No: ". $e->getCode() ." - ". $e->getMessage() );
}
?>