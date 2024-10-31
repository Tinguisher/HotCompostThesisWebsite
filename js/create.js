// Load js if HTML is done
document.addEventListener('DOMContentLoaded', function () {
    // get the status of current hot compost
    fetch('../contexts/GetLatestRecordProcess.php')
        // get response as json
        .then(response => response.json())
        // get objects from fetch
        .then(data => {
            // if there is compost in progress, redirect to dashboard
            if (data.message != "Create") window.location = './dashboard.html';

            // if there is no current in progress, create
            createCompost();
        })

        // error checker
        .catch(error => console.error(error));

    // this is the process of making the hot compost pile
    createCompost = () => {
        // make a request to esp32 to get weight
        fetch('../contexts/GetWeightProcess.php')
            // get response as json
            .then(response => response.json())
            // get objects from fetch
            .then(data => {
                // if the request data is success
                if (data.status == "success") {
                    // output the data to the web
                    console.log(data);
                    return;
                }
                
                console.error(data.message);
            })

            // error checker
            .catch(error => console.error(error));

    }
});