// Load js if HTML is done
document.addEventListener('DOMContentLoaded', function () {
    // check if there is a compost that is in progress
    fetch('../contexts/GetLatestRecordProcess.php')
        // get response as json
        .then(response => response.json())
        // get objects from fetch
        .then(data => {
            // if there is a compost already, go to dashboard with reading
            if (data.message == "Read") return (window.location.href = './dashboard.html');
        })

        // error checker
        .catch(error => console.error(error));
});