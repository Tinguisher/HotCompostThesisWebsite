<?php
// make a string of sql to check if the mixing can be stop after 10mins
$sql = "SELECT *
        FROM `hotcompost`
        WHERE id = ?
            AND status LIKE 'Mixing'
            AND lastMixed < now() - interval 10 minute;";

// prepare the statement
$stmt = $mysqli->prepare($sql);

// bind the parameters to the statement
$stmt -> bind_param ('i', $hotCompost['id']);

// execute the statement
$stmt->execute();

// get the result from the statement
$result = $stmt -> get_result();

// get only one from the executed statement
$hotCompostMixingStop = $result -> fetch_assoc();

// if the hot compost is mixing less than 10mins, exit mixing
if (!$hotCompostMixingStop) exit("Mixing");

// if the hot compost mixed enough, update to In Progress
// make a string of sql to update In Progress
$sql = "UPDATE `hotcompost`
        SET `status` = 'In Progress',
            `lastMixed` = now()
        WHERE status = 'Mixing'
            AND id = ?;";

// prepare the statement
$stmt = $mysqli -> prepare ($sql);

// bind the parameters to the statement
$stmt -> bind_param ('i', $hotCompost['id']);

// execute the statement
$stmt->execute();

// close statement and database and free the result
$stmt -> close();
$result -> free();
$mysqli -> close();

// exit back to in progress since it is not mixing anymore
exit("In Progress");
?>