<?php
// make a string of sql to check the layer material
$sql = "SELECT layer.material,
            layer.part
        FROM `hotcompost`,
            `layer`
        WHERE hotcompost.id = (
                SELECT id
                    FROM `hotcompost`
                    WHERE status LIKE 'Layering'
                    LIMIT 1
            )
            AND hotcompost.id = layer.hotcompost_id
        ORDER BY layer.id DESC
        LIMIT 1;";

// prepare the statement
$stmt = $mysqli->prepare($sql);

// execute the statement
$stmt->execute();

// get the result from the statement
$result = $stmt -> get_result();

// get only one from the executed statement
$layer = $result->fetch_assoc();

// close statement and free the result
$stmt->close();
$result->free();

// make a string of sql to check the layer material
$sql = "SELECT *
        FROM `hotcompost`,
            `layer`
        WHERE hotcompost.id = (
                SELECT id
                    FROM `hotcompost`
                    WHERE status LIKE 'Layering'
                    LIMIT 1
            )
            AND hotcompost.id = layer.hotcompost_id
            AND layer.part IN ('MixRequest', 'MistingRequest', 'MixingAccepted', 'MistingAccepted');";

// prepare the statement
$stmt = $mysqli->prepare($sql);

// execute the statement
$stmt->execute();

// get the result from the statement
$result = $stmt -> get_result();

// get only one from the executed statement
$ESP32Process = $result->fetch_assoc();

// close statement and free the result
$stmt->close();
$result->free();

// if there is no layer created in compost, give the material as green to transpose it to brown later
if (!$layer) {
    $layer = [
        'material' => "Green",
        'part' => "Bottom"
    ];
}

// if the previous layer is Brown, let the user know that Green Material will be next
$layer['material'] = $layer['material'] == "Brown" ? "Green" : "Brown";

// get process is in request or accepted
$layer['ESP32Process'] = ($ESP32Process ? true: false);

// if the layer part is top most, request for brown material
if ($layer['part'] == "Top Layer") $layer['material'] = "Brown";

?>