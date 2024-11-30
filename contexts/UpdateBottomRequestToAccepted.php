<?php
// make a string of sql to check if the mixing can be stop after 5mins
$sql = "UPDATE `layer`
        SET `part` = 'Bottom Misting Accepted'
        WHERE part = 'Bottom Misting Request';";

// prepare the statement
$stmt = $mysqli->prepare($sql);

// execute the statement
$stmt->execute();

// exit back to in progress since it is not mixing anymore
exit("Bottom Misting Accepted");
?>