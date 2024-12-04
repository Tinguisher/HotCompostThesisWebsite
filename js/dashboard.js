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
            payload = { request: "MistRequest" };
            stopMisting.hidden = false;
            useMisting.hidden = true;
        }

        else {
            payload = { request: "None" };
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

    // get latest record function to be looped
    function getLatestRecord() {
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

                // create a notification
                createNotificationRows(data.notifications);

                // if the status of compost is mixing, play the buzzer
                if (data.sensor.status == "Mixing"){
                    const buzzer = new Audio('../assets/Buzzer sound effect.mp3');
                    buzzer.play();
                }

                // loop back to check new updates
                setTimeout(getLatestRecord, 3000);
            })

            // error checker
            .catch(error => {
                console.error(error);
                // loop back to check new updates
                setTimeout(getLatestRecord, 3000);
            });
    }

    // process of creating each notifications
    createNotificationRows = (notifications) => {
        // get the container for the notifications to be input and clear it
        const notificationContainer = document.querySelector("[data-notification-container]");
        notificationContainer.innerHTML = "";

        // get and output the notifications
        notifications.forEach(notification => {
            // get the template for sensor and clone it
            const notificationTemplate = document.querySelector("[data-notification-template]");
            const row = notificationTemplate.content.cloneNode(true).children[0];

            // get the template child that data can be inserted
            const type = row.querySelector("[data-type]");
            const time = row.querySelector("[data-time]");

            // place the data got from the fetch  
            type.textContent = notification.type;
            time.textContent = `TIME: ${notification.time}`;

            // put each made row inside container
            notificationContainer.appendChild(row);
        });
    }

    // if there is click in notification, toggle
    const notificationButton = document.getElementById("notificationButton");
    notificationButton.addEventListener('click', () => {
        // get the popout to be seen
        const notificationPopOut = document.getElementById("notificationPopOut");

        // get 2 types of hue for bell
        const yellowBell = "sepia(100%) saturate(1000%) hue-rotate(0deg)";
        const whiteBell = "unset";

        // toggle for each click
        if (notificationButton.style.filter == yellowBell) {
            notificationButton.style.filter = whiteBell;
            notificationPopOut.style.display = "none";
        }
        else {
            notificationButton.style.filter = yellowBell;
            notificationPopOut.style.display = "flex";
            
        }
    })

    // go to get new record
    getLatestRecord();
});