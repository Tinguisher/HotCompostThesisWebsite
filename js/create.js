// Load js if HTML is done
document.addEventListener('DOMContentLoaded', function () {
    // get if there is a need to use weight sensor to create hotcompost
    fetch('../contexts/GetWeightUseProcess.php')
        // get response as json
        .then(response => response.json())
        // get objects from fetch
        .then(data => {
            // if there is compost in progress, redirect to dashboard
            if (data.message == "In Progress") return(window.location = './dashboard.html');
            
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
                // if the request data is error, go back to dashboard
                if (data.status == "error") return(window.location = './dashboard.html');
                    
                // if the data status is success, output the weight values in weightValue
                const weightValue = document.getElementById("weightValue");
                weightValue.value = data.weight;

                // loop back to get new weight
                createCompost();
            })
            // error checker
            .catch(error => console.error(error));
    }

    // if there is click on the button done, proceed to creating the layer
    const addLayerForm = document.getElementById("addLayerForm");
    addLayerForm.addEventListener('submit', (ev) => {
        // prevent website from loading
        ev.preventDefault();

        // get the data from the form
        const addLayer = new FormData(addLayerForm);

        // create object for each data in addLayerForm
        const payload = Object.fromEntries(addLayer);

        // make a request to create layer
        fetch('../contexts/CreateLayerProcess.php', {
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
                // if the request data is error, go back to dashboard
                if (data.status == "error") return(window.location = './dashboard.html');
                    
                // if the data status is success, output the weight values in weightValue
                const weightValue = document.getElementById("weightValue");
                weightValue.textContent = data.weight;

                // loop back to get new weight
                createCompost();
            })
            // error checker
            .catch(error => console.error(error));
    });
});