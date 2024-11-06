<?php
// make a string of sql to check if there is top layer
$sql = "SELECT *
FROM `layer`
WHERE hotcompost_id = (
    SELECT id
        FROM `hotcompost`
        WHERE status LIKE 'Layering'
        LIMIT 1
    )
    AND part LIKE 'Top Layer';";

// prepare the statement
$stmt = $mysqli->prepare($sql);

// execute the statement
$stmt->execute();

// get the result from the statement
$result = $stmt -> get_result();

// get only one from the executed statement
$compostTopLayer = $result->fetch_assoc();

// free data, close the statement
$result -> free();
$stmt -> close();
?>