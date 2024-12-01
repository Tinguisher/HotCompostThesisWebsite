<?php
// make a string of sql to update the hot compost in progress to mixing
$sql = "UPDATE `hotcompost`
    SET `status` = 'Mixing',
        `lastMixed` = now()
    WHERE status = 'In Progress'
        AND id = ?;";
        
// prepare the statement
$stmt = $mysqli -> prepare ($sql);

// bind the parameters to the statement
$stmt -> bind_param ('i', $hotCompost['id']);

// execute the statement
$stmt -> execute();

// close statement
$stmt -> close();

// make a string of sql to update the hot compost in progress to mixing
$sql = "INSERT INTO `notification`
    (`hotcompost_id`, `type`) VALUES
    (?, 'Mix');";

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

// exit back to Mixing since it is updated to mixing
exit("Mixing");
?>