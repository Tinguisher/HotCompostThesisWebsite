// Load js if HTML is done
document.addEventListener('DOMContentLoaded', function () {
    // create new URLSearchParams to get values
    const urlParams = new URLSearchParams(window.location.search);

    // get the value of receiptID in the url
    const compostID = urlParams.get("compostID");

    // if there is no compostID in the url, go back to dashboard
    if (!compostID) window.location = '../pages/dashboard.html';

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
                // redirect to the dashboard.html
                window.location = '../pages/dashboard.html';
            }
        })
        // error checker
        .catch(error => {
            // output the error in console
            console.error(error);

            // redirect to the dashboard.html
            window.location = '../pages/dashboard.html';
        });

    // process of creating the summary of this compostID
    createSummary = (readings) => {
        // get the container for the sensors to be input and clear it
        const sensorContainer = document.querySelector("[data-sensor-container]");
        sensorContainer.innerHTML = "";

        // get the reading
        readings.forEach(reading => {
            // get the template for sensor and clone it
            const sensorTemplate = document.querySelector("[data-sensor-template]");
            const row = sensorTemplate.content.cloneNode(true).children[0];

            // get the template child that data can be inserted
            const moisturePercent = row.querySelector("[data-moisture-percent]");
            const temperature = row.querySelector("[data-temperature]");
            const time = row.querySelector("[data-time]");

            // place the data got from the fetch  
            moisturePercent.textContent = `${reading.moisturePercent}%`;
            temperature.textContent = `${reading.temperatureCelsius}°C`;
            time.textContent = reading.time;

            // put each made row inside sensorContainer
            sensorContainer.appendChild(row);
        });
    }
});
