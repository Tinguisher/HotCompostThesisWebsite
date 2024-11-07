<?php
// make a string of sql to check if the mixing can be stop after 5mins
$sql = "UPDATE `layer`
        SET `part` = 'MixingAccepted'
        WHERE part = 'MixRequest';";

// prepare the statement
$stmt = $mysqli->prepare($sql);

// execute the statement
$stmt->execute();

// udpate the time for layering
include './RequestLastMixedLayeringProcess.php';

// exit back to in progress since it is not mixing anymore
exit("MixAccepted");
?>