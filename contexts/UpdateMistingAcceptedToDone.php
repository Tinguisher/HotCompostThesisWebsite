<?php
// if the file is accessed manually and not post
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    // make an error response
    exit("Error. Not a POST request");
}

// get post values
$requestAccepted = $_POST['requestAccepted'];

// check if there is empty input of data
if ( empty($requestAccepted) ) {
    // make an error response
    exit("Error. Values are required");
};

// access database
$mysqli = require_once "./database.php";

// try to create and catch if there is error
try{
    // make a string of sql to update the misting accepted to its layer
    if ($requestAccepted == "Top Misting Accepted") {
        $sql = "UPDATE `layer`
            SET `part` = 'Top'
            WHERE part = 'Top Misting Accepted';";
    }
    else if ($requestAccepted == "Bottom Misting Accepted") {
        $sql = "UPDATE `layer`
            SET `part` = 'Bottom'
            WHERE part = 'Bottom Misting Accepted';";
    }
    else exit("Error. Unidentified Request: ". $requestAccepted);
    
    // prepare the statement
    $stmt = $mysqli -> prepare ($sql);

    // execute the statement
    $stmt -> execute();

    // close statement
    $stmt -> close();
    

    // exit to done if bottom misting accepted
    if ($requestAccepted == "Bottom Misting Accepted") exit ("Done");

    // update the layering to in progress
    // make a string of sql to update layering into in progress
    $sql = "UPDATE `hotcompost`
        SET `status` = 'In Progress',
            `createdAt` = now(),
            `lastMixed` = now()
        WHERE status = 'Layering';";

    // prepare the statement
    $stmt = $mysqli -> prepare ($sql);

    // execute the statement
    $stmt -> execute();

    // close database
    $mysqli -> close();

    // exit the request as done to continue
    exit ("Done");
}
// if there is error in query
catch (Exception $e){
    // make an error response
    exit ( "Error No: ". $e->getCode() ." - ". $e->getMessage() );
}

?>