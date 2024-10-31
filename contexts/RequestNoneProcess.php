<?php
// make a string of sql to check web request to esp32
$sql = "SELECT *
        FROM `connection`
        WHERE id = 1
        LIMIT 1;";

// prepare the statement
$stmt = $mysqli->prepare($sql);

// execute the statement
$stmt->execute();

// get the result from the statement
$result = $stmt->get_result();

// get only one from the executed statement
$connection = $result->fetch_assoc();

// close statement and database free the result
$stmt->close();
$result->free();

// if the request is empty, make a new one
if (!$connection) {
    // make a string of sql to create a new connection for less error
    $sql = "INSERT INTO `connection`
            (`id`, `weight`, `request`)
            VALUES (1, 0, 'None')";

    // prepare the statement
    $stmt = $mysqli->prepare($sql);

    // execute the statement
    $stmt->execute();

    // close the statement
    $stmt->close();

    // make a connection with key of request and value of "None"
    $connection['request'] = "None";
}

// make a sql to update the request to None
$sql = "UPDATE `connection`
    SET `weight` = 0,
        `request` = 'None'
    WHERE `id` = 1;";

// prepare the statement
$stmt = $mysqli->prepare($sql);

// execute the statement
$stmt->execute();

// close statement and database free the result
$stmt->close();

?>