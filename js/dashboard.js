// Load js if HTML is done
document.addEventListener('DOMContentLoaded', function () {
    // get the status of current hot compost
    fetch('../contexts/GetLatestRecordProcess.php')
        // get response as json
        .then(response => response.json())
        // get objects from fetch
        .then(data => {
            // get the container id
            const sensorContainer = document.getElementById("sensorContainer");

            // clear the content of the container
            sensorContainer.innerHTML = "";

            // if there is compost in progress
            if (data.message == "Read") {
                // put the moisture reading inside the container
                const moisturePercentage = document.createElement('p');
                moisturePercentage.textContent = `Moisture Percentage: ${data.sensor.moisturePercent}`;
                sensorContainer.appendChild(moisturePercentage);

                // put the temperature reading inside the container
                const temperatureCelsius = document.createElement('p');
                temperatureCelsius.textContent = `Temperature: ${data.sensor.temperatureCelsius}`;
                sensorContainer.appendChild(temperatureCelsius);

                // put the time inside the container
                const time = document.createElement('p');
                time.textContent = `Time: ${data.sensor.time}`;
                sensorContainer.appendChild(time);

                return;
            }
            // if there is no current in progress, create
            // create a message to inform the user
            const createMessage = document.createElement('p');
            createMessage.textContent = "There seems to be no in progress of hot compost, Create New One?";
            sensorContainer.appendChild(createMessage);

            // create a button to create a new compost
            const createButton = document.createElement('button');
            createButton.textContent = "Create New Compost";
            sensorContainer.appendChild(createButton);

            // if there is click in the create button, go to create.html 
            createButton.addEventListener('click', () => {
                window.location.href = './create.html';
            });

            // go back to check new sensor reading
            return;
        })

        // error checker
        .catch(error => console.error(error));
});