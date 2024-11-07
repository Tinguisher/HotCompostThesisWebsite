// Load js if HTML is done
document.addEventListener('DOMContentLoaded', function () {
    // get global variables to be manipulated
    const material = document.getElementById("material");
    const addLayerForm = document.getElementById("addLayerForm");
    const submitFormButton = document.getElementById("submitFormButton");
    const alertMixDiv = document.getElementById("alertMixDiv");
    const mixButton = document.getElementById("mixButton");
    const topBrownLayerDiv = document.getElementById("topBrownLayerDiv");
    const lastBrownWeight = document.getElementById("lastBrownWeight");
    let finishButton = addLayerForm.querySelector('button');

    // function to be repeated in initial and after water and mix
    checkLayering = () => {
        // get if there is a need to use weight sensor to create hotcompost
        fetch('../contexts/GetWeightUseProcess.php')
            // get response as json
            .then(response => response.json())
            // get objects from fetch
            .then(data => {
                // if there is compost in progress, redirect to dashboard
                if (data.message == "In Progress") return (window.location = './dashboard.html');

                // if there are brown and material consecutively, mix first before proceeding to next
                if (data.mix) return (mixRequest());

                // if the server requests for top most layer, add last layer to finish
                if (data.topLayer) return (lastLayerFinish());

                // change the material name to be seen by the user
                material.textContent = `Your material is: ${data.material}`;

                // if compost can be finish go to process of adding a button
                if (data.finish) finishCompost();
                if (finishButton && !data.finish) finishButton.remove();

                // if there is no current in progress, create
                createCompost();
            })
            // error checker
            .catch(error => console.error(error));
    }

    // go to initial function
    checkLayering();

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

                // if the data status is success, output the weight values in weightValue and lastbrown weight
                const weightValue = document.getElementById("weightValue");
                weightValue.value = (data.weight <= 0) ? "0" : data.weight;
                lastBrownWeight.value = (data.weight <= 0) ? "0" : data.weight;

                // check if the submit buttons must be clickable or not depending on weight value
                submitFormButton.disabled = (data.weight <= 0) ? true : false;
                topBrownLayerButton.disabled = (data.weight <= 0) ? true : false;
                if (finishButton) finishButton.disabled = (data.weight <= 0) ? true : false;

                // loop back to get new weight
                createCompost();
            })
            // error checker
            .catch(error => console.error(error));
    }

    // change the hidden element to show
    mixRequest = () => {
        alertMixDiv.hidden = false;
        addLayerForm.hidden = true;
    }

    // show only the div for top layer
    lastLayerFinish = () => {
        alertMixDiv.hidden = true;
        addLayerForm.hidden = true;
        topBrownLayerDiv.hidden = false;

        // request for weight
        createCompost();
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
                if (data.status == "error") return (console.error(data.message));

                // if there are brown and material consecutively, mix first before proceeding to next
                if (data.mix) return (mixRequest());

                // change to the next material name
                material.textContent = `Your material is: ${data.material}`;

                // if the material can be finish, show button, if can't then delete
                if (data.finish) finishCompost();
                if (finishButton && !data.finish) finishButton.remove();
            })
            // error checker
            .catch(error => console.error(error));
    });

    // if there is click to mix the button
    mixButton.addEventListener('click', () => {
        // make a request to mix layer
        fetch('../contexts/RequestLayerWaterMixProcess.php')
            // get response as json
            .then(response => response.json())
            // get objects from fetch
            .then(data => {
                // if the setting up of hot compost is error, output it in console
                if (data.status == "error") console.error(data.message);;

                // change the form to show to the user
                alertMixDiv.hidden = true;
                addLayerForm.hidden = false;

                // go back to check layering
                checkLayering();
            })
            // error checker
            .catch(error => console.error(error));
    })

    // process of adding a finish button
    finishCompost = () => {
        // create a button to finish and put it inside the form
        finishButton = document.createElement('button');
        finishButton.textContent = "Finish up compost by adding this green material and top most brown material";
        finishButton.type = "button";
        addLayerForm.appendChild(finishButton);
        
        // if there is click on the finish button
        finishButton.addEventListener('click', () => {

            // get the data from the form
            const addLayer = new FormData(addLayerForm);

            // create object for each data in addLayerForm
            const payload = Object.fromEntries(addLayer);

            // make a request to make the hot compost as in progress
            fetch('../contexts/CreateTopLayerProcess.php', {
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
                    // if the setting up of hot compost is error, output it in console
                    if (data.status == "error") console.error(data.message);;

                    // mix first before putting top layer
                    mixRequest();
                })
                // error checker
                .catch(error => console.error(error));
        })
    }

    // if there is click in the top brown layer button
    const topBrownLayerButton = document.getElementById("topBrownLayerButton");
    topBrownLayerButton.addEventListener('click', () => {
        // put the weight value into payload
        const payload = {input_topBrown: lastBrownWeight.value};

        // update database to give weight and ask for misting to database for top layer
        fetch('../contexts/UpdateTopLayerProcess.php', {
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
                // if the setting up of hot compost is error, output it in console
                if (data.status == "error") return (console.error(data.message));

                // go to dashboard if done
                window.location = './dashboard.html'
            })
            // error checker
            .catch(error => console.error(error));
    })
});