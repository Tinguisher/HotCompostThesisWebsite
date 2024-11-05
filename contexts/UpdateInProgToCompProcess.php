<?php
// make a string of sql to update the hot compost to completed
$sql = "UPDATE `hotcompost`
    SET `status` = 'Completed'
    WHERE status = 'In Progress'
        AND id = ?;";
        
// prepare the statement
$stmt = $mysqli -> prepare ($sql);

// bind the parameters to the statement
$stmt -> bind_param ('i', $hotCompost['id']);

// execute the statement
$stmt -> execute();

// close statement and database and free the result
$stmt -> close();
$result -> free();
$mysqli -> close();

// exit back to None since there is no more in progress
exit("None");
?>