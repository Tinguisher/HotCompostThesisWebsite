// Load js if HTML is done
document.addEventListener('DOMContentLoaded', function () {
    // create new URLSearchParams to get values
    const urlParams = new URLSearchParams(window.location.search);

    // get the value of receiptID in the url
    const compostID = urlParams.get("compostID");

    // get the data of the sensor for this compost id
    fetch(`../contexts/GetSensorHistoryProcess.php?compostID=${compostID}`)
        // get response as json
        .then(response => response.json())

        // get objects from fetch
        .then(data => {
            // if passing of data is success
            if (data.status == "success") {
                // go to creating the compost summary of this id
                createSummary(data.compost);
            }

            // if passing of data is not success
            else {
                // redirect to the home.php
                window.location = '../pages/home.php';
            }
            console.log(data);
        })
        // error checker
        .catch(error => {
            // output the error in console
            console.error(error);

            // redirect to the home.php
            window.location = '../pages/home.php';
        });

    // process of creating the summary of this compostID
    createSummary = (readings) => {

    }
});
