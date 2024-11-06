<?php
// make a string of sql to check if hot compost can be mix
$sql = "SELECT COUNT(layer.part) AS count
        FROM `hotcompost`,
            `layer`
        WHERE hotcompost.id = (
                SELECT id
                    FROM `hotcompost`
                    WHERE status LIKE 'Layering'
                    LIMIT 1
            )
            AND part LIKE 'Bottom'
            AND hotcompost.id = layer.hotcompost_id;";

// prepare the statement
$stmt = $mysqli->prepare($sql);

// execute the statement
$stmt->execute();

// get the result from the statement
$result = $stmt -> get_result();

// get only one from the executed statement
$layerMixed = $result->fetch_assoc();

// close statement and free the result
$stmt->close();
$result->free();

// if the green and brown material is not watered and mixed, request for the mix before proceeding
$layer['mix'] = $layerMixed['count'] >= 2 ? true : false;
?>