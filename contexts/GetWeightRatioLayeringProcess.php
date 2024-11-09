<?php
// make sql to check the weight of the green and brown material that is layering
$sql = "SELECT DISTINCT(material), SUM(layer.weight) AS weight
    FROM `hotcompost`,
        `layer`
    WHERE hotcompost.id = layer.hotcompost_id
        AND hotcompost.status LIKE 'Layering'
    GROUP BY material
    ORDER BY material ASC;";

// prepare the statement
$stmt = $mysqli -> prepare ($sql);

// execute the statement
$stmt -> execute();

// get the result from the statement
$result = $stmt -> get_result();

// get only one from the executed statement
$compostWeight = $result -> fetch_all( MYSQLI_ASSOC );

// close statement and free the result
$stmt->close();
$result->free();
?>