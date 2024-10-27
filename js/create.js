// Load js if HTML is done
document.addEventListener('DOMContentLoaded', function () {
    // get the status of current hot compost
    fetch('../contexts/GetLatestRecordProcess.php')
        // get response as json
        .then(response => response.json())
        // get objects from fetch
        .then(data => {
            // if there is no current in progress, create
            if (data.message == "Create") return;

            // if there is compost in progress, redirect to dashboard
            window.location = './dashboard.html';
        })

        // error checker
        .catch(error => console.error(error));
});