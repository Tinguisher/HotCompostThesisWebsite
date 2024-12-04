<?php
// make a string of sql to check if hot compost can be mist by 2 Bottom Not Watered
$sql = "SELECT COUNT(layer.part) AS count
        FROM `hotcompost`,
            `layer`
        WHERE hotcompost.id = (
                SELECT id
                    FROM `hotcompost`
                    WHERE status LIKE 'Layering'
                    LIMIT 1
            )
            AND part LIKE 'Bottom Not Watered'
            AND hotcompost.id = layer.hotcompost_id;";

// prepare the statement
$stmt = $mysqli->prepare($sql);

// execute the statement
$stmt->execute();

// get the result from the statement
$result = $stmt -> get_result();

// get only one from the executed statement
$layerMisted = $result->fetch_assoc();

// close statement and free the result
$stmt->close();
$result->free();

// make a string of sql to check if there is top layer
$sql = "SELECT *
    FROM `layer`
    WHERE hotcompost_id = (
        SELECT id
            FROM `hotcompost`
            WHERE status LIKE 'Layering'
            LIMIT 1
        )
        AND part LIKE 'Top Not Watered';";

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

// if there is compost that is in top layer, create one for error check
if (!$compostTopLayer) {
    $compostTopLayer = [
        'weight' => 0,
        'part' => "None"
    ];
}

// if the green and brown material is not watered, request for the mist before proceeding
$layer['mist'] = $layerMisted['count'] >= 2 ? true : false;

// if the request mist is false, check for top layer if it needs to be mist
if ($layer['mist'] == false) {
    // if there is weight already in the top layer, make the mistbutton true
    $layer['mist'] = ($compostTopLayer['weight'] != 0) ? true : false;
}

?>