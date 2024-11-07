<?php
// access database
$mysqli = require_once "./database.php";

// try to create and catch if there is error
try{
    // make a string of sql to put the weight reading to database
    $sql = "UPDATE `hotcompost`
        SET `status` = 'Mixing',
            `lastMixed` = now()
        WHERE `id` = (
                SELECT id
                    FROM `hotcompost`
                    WHERE status LIKE 'In Progress'
            );";
    
    // prepare the statement
    $stmt = $mysqli -> prepare ($sql);

    // execute the statement
    $stmt -> execute();

    // close statement and database
    $stmt -> close();
    $mysqli -> close();

    // make a success response
    exit ("Successfully requested to mix the compost");
}
// if there is error in query
catch (Exception $e){
    // make an error response
    exit ( "Error No: ". $e->getCode() ." - ". $e->getMessage() );
}
?>