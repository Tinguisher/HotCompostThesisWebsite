<?php
// make a string of sql to update part to MixRequest
$sql = "UPDATE `hotcompost`
    SET `lastMixed` = now()
    WHERE status = 'Layering';";

// prepare the statement
$stmt = $mysqli->prepare($sql);

// execute the statement
$stmt->execute();

// close the statement
$stmt->close();
?>