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

    const mistButton = document.getElementById("mistButton");
    mistButton.addEventListener('click', () => {
        const useMisting = document.getElementById("useMisting");
        const stopMisting = document.getElementById("stopMisting");

        let payload = {};

        // toggle what to show when clicking the mist button
        if (stopMisting.hidden == true) {
            payload = {request: "MistRequest"};
            stopMisting.hidden = false;
            useMisting.hidden = true;
        }

        else {
            payload = {request: "None"};
            stopMisting.hidden = true;
            useMisting.hidden = false;
        }

        // request for misting to esp32
        fetch('../contexts/RequestESP32Process.php', {
            method: "POST",
            headers: {
                // state as a json type
                'Content-Type': 'application/json; charset=utf-8'
            },
            // give the request as a JSON to the server
            body: JSON.stringify(payload)
        })
            // get response as json
            .then(response => response.json())
            // get objects from fetch
            .then(data => {
                if (data.status == "success") {
                    console.log("NOICE");
                }
            })
    })

    // get the status of current hot compost
    fetch('../contexts/GetLatestRecordProcess.php')
        // get response as json
        .then(response => response.json())
        // get objects from fetch
        .then(data => {
            // if there is no compost, go to dashboard with no compost
            if (data.message == "Create") return (window.location.href = './Dashboard_No_Compost.html');

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
        })

        // error checker
        .catch(error => console.error(error));
});