// Load js if HTML is done
document.addEventListener('DOMContentLoaded', function () {
    // get global variables to be manipulated
    const material = document.getElementById("material");
    const addLayerForm = document.getElementById("addLayerForm");
    let finishButton = addLayerForm.querySelector('button');

    // get if there is a need to use weight sensor to create hotcompost
    fetch('../contexts/GetWeightUseProcess.php')
        // get response as json
        .then(response => response.json())
        // get objects from fetch
        .then(data => {
            // if there is compost in progress, redirect to dashboard
            if (data.message == "In Progress") return (window.location = './dashboard.html');

            // change the material name to be seen by the user
            material.textContent = `Your material is: ${data.material}`;

            // if compost can be finish go to process of adding a button
            if (data.finish) finishCompost();

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
                if (data.status == "error") return (window.location = './dashboard.html');

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
                if (data.status == "error") return (window.location = './dashboard.html');

                // change to the next material name
                material.textContent = `Your material is: ${data.material}`;

                // if the material can be finish, show button, if can't then delete
                data.finish ? finishCompost() : finishButton.remove();
            })
            // error checker
            .catch(error => console.error(error));
    });

    // process of adding a finish button
    finishCompost = () => {
        // create a button to finish and put it inside the form
        finishButton = document.createElement('button');
        finishButton.textContent = "Finish up compost";
        finishButton.type = "button";
        addLayerForm.appendChild(finishButton);

        // if there is click on the finish button
        finishButton.addEventListener('click', () => {
            // make a request to make the hot compost as in progress
            fetch('../contexts/UpdateInProgressProcess.php')
                // get response as json
                .then(response => response.json())
                // get objects from fetch
                .then(data => {
                    // if the setting up of hot compost is success, go back to dashboard
                    if (data.status == "success") window.location = './dashboard.html';

                    // if there is error in the server, output the message in console
                    console.error(data.message);
                })
                // error checker
                .catch(error => console.error(error));
        })
    }
});