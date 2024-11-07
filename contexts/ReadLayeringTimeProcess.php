<?php
// make a string of sql to check if lastMixed for layering gap is 1 minute
$sql = "SELECT *
    FROM `hotcompost`
    WHERE status LIKE 'Layering'
        AND lastMixed < now() - interval 1 minute;";

// prepare the statement
$stmt = $mysqli -> prepare ($sql);

// execute the statement
$stmt -> execute();

// get the result from the statement
$result = $stmt -> get_result();

// get only one from the executed statement
$layeringInterval = $result -> fetch_assoc();

// if it is still not one minute when started to mix, still mix the compost
if (!$layeringInterval) exit("MixingAccepted");

// if the layering mixed enough after a minute
// make a string of sql to update the MixingAccepted to MixingDone
$sql = "UPDATE `layer`
    SET `part` = 'MixingDone'
    WHERE part = 'MixingAccepted';";

// prepare the statement
$stmt = $mysqli -> prepare ($sql);

// execute the statement
$stmt -> execute();

// close statement and database and free the result
$stmt -> close();
$result -> free();
$mysqli -> close();

// exit to none
exit ("None");
?>