// Load js if HTML is done
document.addEventListener('DOMContentLoaded', function () {
    // check all compost history and go to history of hot compost if there is click
    const historyCompostButton = document.getElementById("historyCompostButton");
    historyCompostButton.addEventListener('click', () => {
        window.location.href = './history_compost.html';
    })

    // navigate to npk page if there is click
    const useNPKButton = document.getElementById("useNPKButton");
    useNPKButton.addEventListener('click', () => {
        window.location.href = './npk_reading.html';
    })

    // get the status of current hot compost
    fetch('../contexts/GetLatestRecordProcess.php')
        // get response as json
        .then(response => response.json())
        // get objects from fetch
        .then(data => {
            // get element ids for data to be input
            const moisturePercentage = document.getElementById("moisturePercentage");
            const temperatureCelsius = document.getElementById("temperatureCelsius");
            const time = document.getElementById("time");

            // put the values in the element
            moisturePercentage.textContent = `${data.sensor.moisturePercent}%`;
            temperatureCelsius.textContent = `${data.sensor.temperatureCelsius}°C`;
            time.textContent = `TIME: ${data.sensor.time}`;

            // if there is click in current history button, go to its sensor page
            const currentHistoryButton = document.getElementById("currentHistoryButton");
            currentHistoryButton.addEventListener('click', () => {
                window.location.href = `./history_reading.html?compostID=${data.sensor.id}`
            })

















            // get the container id
            const sensorContainer = document.getElementById("sensorContainer");

            // clear the content of the container
            sensorContainer.innerHTML = "";

            // if there is no current in progress, create
            if (data.message == "Create") {
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
            }
            // // if there is compost in progress
            // // put the moisture reading inside the container
            // const moisturePercentage = document.createElement('p');
            // moisturePercentage.textContent = `Moisture Percentage: ${data.sensor.moisturePercent}`;
            // sensorContainer.appendChild(moisturePercentage);

            // // put the temperature reading inside the container
            // const temperatureCelsius = document.createElement('p');
            // temperatureCelsius.textContent = `Temperature: ${data.sensor.temperatureCelsius}`;
            // sensorContainer.appendChild(temperatureCelsius);

            // // put the time inside the container
            // const time = document.createElement('p');
            // time.textContent = `Time: ${data.sensor.time}`;
            // sensorContainer.appendChild(time);

            // // create a button to see current compost history
            // const currentHistoryButton = document.createElement('button');
            // currentHistoryButton.textContent = "View Current Compost History";
            // sensorContainer.appendChild(currentHistoryButton);

            // // if there is click in the currentHistoryButton, go to check its history
            // currentHistoryButton.addEventListener('click', () => {
            //     window.location.href = `./history_reading.html?compostID=${data.sensor.id}`
            // })

            return;

        })

        // error checker
        .catch(error => console.error(error));
});