// Load js if HTML is done
document.addEventListener('DOMContentLoaded', function () {
    // get global variables to be manipulated
    const weightValue = document.getElementById("weightValue");
    const submitFormButton = document.getElementById("submitFormButton");
    const submitFormButtonDisabled = document.getElementById("submitFormButtonDisabled");
    const topBrownLayerButton = document.getElementById("topBrownLayerButton");
    const topBrownLayerButtonDisabled = document.getElementById("topBrownLayerButtonDisabled");
    const alertMistDiv = document.getElementById("alertMistDiv");
    const finishButton = document.getElementById("finishButton");
    const finishButtonDisabled = document.getElementById("finishButtonDisabled");
    const topBrownLayerDiv = document.getElementById("topBrownLayerDiv");
    const lastBrownWeight = document.getElementById("lastBrownWeight");

    // variables for green and brown current weight before adding
    var currentBrownWeight;
    var currentGreenWeight;
    var currentMaterial;

    // ratio per volume in the bucket
    var brownVolume = 1100;
    var greenVolume = 3850;
    var lowRatio = greenVolume / (2 * brownVolume);
    var highRatio = greenVolume / (3 * brownVolume);

    // if there is click in logo, go to dashboard
    const logoButton = document.getElementById("logoButton");
    logoButton.addEventListener('click', () => {
        window.location = '../pages/dashboard.html';
    })

    // function to be repeated in initial and after water and mix
    function checkLayering() {
        // get if there is a need to use weight sensor to create hotcompost
        fetch('../contexts/GetWeightUseProcess.php')
            // get response as json
            .then(response => response.json())
            // get objects from fetch
            .then(data => {
                // if there is compost in progress, redirect to dashboard
                if (data.message == "In Progress") return (window.location = './dashboard.html');

                // if esp32 needs to process, wait for esp32
                if (data.ESP32Process) return (waitESP32());

                // if there are brown and material consecutively, mist first before proceeding to next
                if (data.mist) return (mistRequest());

                // get the brown and green ratio
                let brownRatio = data.brownWeight > 0 ? 1 : 0;
                let greenRatio = (data.brownWeight) > 0 ? Number(data.greenWeight / data.brownWeight).toLocaleString() : data.greenWeight;

                // get the variables to put the current ratio
                const currentBrownRatio = document.getElementById("currentBrownRatio");
                const currentGreenRatio = document.getElementById("currentGreenRatio");
                currentBrownRatio.textContent = brownRatio;
                currentGreenRatio.textContent = greenRatio;

                // get the materials for global variables to be used in updating every read
                currentBrownWeight = data.brownWeight;
                currentGreenWeight = data.greenWeight;
                currentMaterial = data.material;

                // change the material name and color to be seen by the user depending on material
                material.textContent = currentMaterial;
                if (currentMaterial == "Brown") {
                    material.style.color = "#ffa500"
                    brownMisting.style.display = "flex";
                    greenMistingFinishup.style.display = "none";
                }
                else {
                    material.style.color = "#00ff00";
                    brownMisting.style.display = "none";
                    greenMistingFinishup.style.display = "flex";
                }

                // if the server requests for top most layer, add last layer to finish
                if (data.topLayer) return (lastLayerFinish());

                // show text for adding divs
                waitDiv.hidden = true;
                inputDiv.style.display = "flex";

                // if there is no current in progress, create
                createCompost();
            })
            // error checker
            .catch(error => {
                console.error(error);
                // loop back to check layering if there is error
                setTimeout(checkLayering, 1000);
            });
    }

    // go to initial function
    checkLayering();

    // this is process of waiting for esp32
    waitESP32 = () => {
        waitDiv.hidden = false;
        alertMistDiv.hidden = true;
        inputDiv.style.display = "none";

        // loop back to check layering
        setTimeout(checkLayering, 1000);
    }

    // this is the process of making the hot compost pile
    function createCompost() {
        // make a request to esp32 to get weight
        fetch('../contexts/GetWeightProcess.php')
            // get response as json
            .then(response => response.json())
            // get objects from fetch
            .then(data => {
                // if the request data is error, go back to dashboard
                if (data.status == "error") return (window.location = './dashboard.html');

                // get the data.weight for verification if lower than 0, get 0 instead
                let inputWeight = data.weight < 0 ? 0 : data.weight;

                // if the data status is success, output the weight values in weightValue and lastbrown weight
                weightValue.value = inputWeight;
                lastBrownWeight.value = inputWeight;

                // get the brown and green ratio
                let brownRatio = Number(currentBrownWeight > 0 ? 1 : inputWeight);
                let greenRatio;

                // if brown, calculate by dividing the weight to green
                if (currentMaterial == "Brown") {
                    // get the next green ratio
                    greenRatio = Number(currentGreenWeight / (
                        (inputWeight == 0 && currentBrownWeight == 0) ?
                            1 : (inputWeight + currentBrownWeight)
                    )
                    ).toLocaleString();
                }

                // if green, calculate by adding the weight to green
                else if (currentMaterial == "Green") {
                    greenRatio = Number((currentGreenWeight + inputWeight) / currentBrownWeight).toLocaleString(); // limit the decimal
                }

                // prompt the ratio to the web
                const afterBrownRatio = document.getElementById("afterBrownRatio");
                const afterGreenRatio = document.getElementById("afterGreenRatio");
                afterBrownRatio.textContent = (brownRatio < 100) ? brownRatio : "+99";
                afterGreenRatio.textContent = greenRatio;

                // hide buttons and show unclickable button if input weight is 0
                if (inputWeight == 0) {
                    submitFormButtonDisabled.hidden = false;
                    topBrownLayerButtonDisabled.hidden = false;
                    submitFormButton.hidden = true;
                    topBrownLayerButton.hidden = true;
                }
                else {
                    submitFormButtonDisabled.hidden = true;
                    topBrownLayerButtonDisabled.hidden = true;
                    submitFormButton.hidden = false;
                    topBrownLayerButton.hidden = false;
                }

                // // get ratio needed for last
                // let finalLowBrownWeight = Number(((currentGreenWeight + inputWeight) / lowRatio) - currentBrownWeight).toLocaleString();
                let finalHighBrownWeight = Number(((currentGreenWeight + inputWeight) / highRatio) - currentBrownWeight).toLocaleString();

                // check if finish should be clickable or not
                if (finalHighBrownWeight < 100 || inputWeight == 0) {
                    finishButton.style.display = "none";
                    finishButtonDisabled.style.display = "flex";
                }
                else {
                    finishButton.style.display = "flex";
                    finishButtonDisabled.style.display = "none";
                }

                // loop back to get new weight
                setTimeout(createCompost, 1000);
            })
            // error checker
            .catch(error => {
                console.error(error);
                // loop back to check layering if there is error
                setTimeout(checkLayering, 1000);
            });
    }

    // change the hidden element to show
    mistRequest = () => {
        alertMistDiv.hidden = false;
        inputDiv.style.display = "none";

        // loop back to check layering
        setTimeout(checkLayering, 1000);
    }

    // show only the div for top layer
    lastLayerFinish = () => {
        alertMistDiv.hidden = true;
        bottomLayerDiv.hidden = true;
        inputDiv.style.display = "flex";
        topBrownLayerDiv.hidden = false;
        waitDiv.hidden = true;

        // request for weight
        createCompost();
    }

    // if there is click on the submit button, proceed to creating the layer
    submitFormButton.addEventListener('click', (ev) => {
        // prevent website from loading
        ev.preventDefault();

        // if there is no weight, exit
        if (weightValue.value < 0) return;

        // create object as payload to put into database
        const payload = { input_weightValue: weightValue.value };

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

                // go back to void of initial load to check new ratio etc
                checkLayering();
            })
            // error checker
            .catch(error => console.error(error));
    });

    // if there is click to done button
    const startMistButton = document.getElementById("startMistButton");
    startMistButton.addEventListener('click', () => {
        // make a request to mist layer
        fetch('../contexts/UpdateBottomTopNotWateredToRequest.php')
            // get response as json
            .then(response => response.json())
            // get objects from fetch
            .then(data => {
                // if the setting up of hot compost is error, output it in console
                if (data.status == "error") console.error(data.message);;

                // go back to check layering
                checkLayering();
            })
            // error checker
            .catch(error => console.error(error));
    })

    // if there is click on the finish button
    finishButton.addEventListener('click', (ev) => {
        // prevent website from loading
        ev.preventDefault();

        // if there is no weight, exit
        if (weightValue.value < 0) return;

        // create object as payload to put into database
        const payload = { input_weightValue: weightValue.value };

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

                // go back to check layering
                checkLayering();
            })
            // error checker
            .catch(error => console.error(error));
    })

    // if there is click in the top brown layer button
    topBrownLayerButton.addEventListener('click', () => {
        // put the weight value into payload
        const payload = { input_topBrown: lastBrownWeight.value };

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

                // go back to check layering
                checkLayering();
            })
            // error checker
            .catch(error => console.error(error));
    })

    // get the buttons for misting
    const brownMisting = document.getElementById("brownMisting");
    const greenMisting = document.getElementById("greenMisting");

    // if there is click on misting, go to misting options
    brownMisting.addEventListener('click', () => {
        mistingOptions();
    })
    greenMisting.addEventListener('click', () => {
        mistingOptions();
    })

    // make a request and show necessary buttons
    mistingOptions = () => {
        // get the value of each misting element
        const useMistBrown = document.getElementById("useMistBrown");
        const stopMistBrown = document.getElementById("stopMistBrown");
        const useMistGreen = document.getElementById("useMistGreen");
        const stopMistGreen = document.getElementById("stopMistGreen");

        let payload = {};

        // toggle what to show when clicking the mist button
        if (useMistBrown.style.display == "none") {
            payload = { request: "Weight" };
            useMistBrown.style.display = "inline-block";
            useMistGreen.style.display = "flex";
            stopMistBrown.style.display = "none";
            stopMistGreen.style.display = "none";
        }

        else {
            payload = { request: "WeightMistRequest" };
            useMistBrown.style.display = "none";
            useMistGreen.style.display = "none";
            stopMistBrown.style.display = "inline-block";
            stopMistGreen.style.display = "flex";
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
    }
});