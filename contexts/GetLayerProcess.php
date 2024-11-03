<?php
// make a string of sql to check if hot compost can be finish up
$sql = "SELECT COUNT(layer.hotcompost_id) AS count
        FROM `hotcompost`,
            `layer`
        WHERE hotcompost.id = (
                SELECT id
                    FROM `hotcompost`
                    WHERE status LIKE 'Layering'
                    LIMIT 1
            )
            AND hotcompost.id = layer.hotcompost_id;";

// prepare the statement
$stmt = $mysqli->prepare($sql);

// execute the statement
$stmt->execute();

// get the result from the statement
$result = $stmt -> get_result();

// get only one from the executed statement
$layerCount = $result->fetch_assoc();

// close statement and free the result
$stmt->close();
$result->free();

// if the top layer is brown and more than 1 layer then it can be finish
$layer['finish'] = ($layerCount['count'] % 2 == 1 && $layerCount['count'] > 1) ? true : false;
?>