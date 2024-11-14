// Load js if HTML is done
document.addEventListener('DOMContentLoaded', function () {
    // get global variables to be manipulated
    const material = document.getElementById("material");
    const addLayerForm = document.getElementById("addLayerForm");
    const submitFormButton = document.getElementById("submitFormButton");
    const finishButton = document.getElementById("finishButton");
    const currentRatio = document.getElementById("currentRatio");
    const ratioAfterAdding = document.getElementById("ratioAfterAdding");
    const alertMixDiv = document.getElementById("alertMixDiv");
    const waitDiv = document.getElementById("waitDiv");
    const mixButton = document.getElementById("mixButton");
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

                // if esp32 needs to process, wait for esp32
                if (data.ESP32Process) return (waitESP32());

                // if there are brown and material consecutively, mix first before proceeding to next
                if (data.mix || data.mistButton) return (mixRequest());
                
                // get the brown and green ratio
                let brownRatio = data.brownWeight > 0 ? 1 : 0;
                let greenRatio = (data.brownWeight) > 0 ? Number(data.greenWeight / data.brownWeight).toLocaleString() : data.greenWeight;

                // output the current ratio
                currentRatio.textContent = `Your current ratio is: ${brownRatio} : ${greenRatio}`;

                // get the materials for global variables to be used in updating every read
                currentBrownWeight = data.brownWeight;
                currentGreenWeight = data.greenWeight;
                currentMaterial = data.material;

                // if the server requests for top most layer, add last layer to finish
                if (data.topLayer) return (lastLayerFinish());

                // show text for adding divs
                waitDiv.hidden = true;
                addLayerForm.hidden = false;
                currentRatio.hidden = false;
                ratioAfterAdding.hidden = false;

                // change the material name to be seen by the user
                material.textContent = `Your material is: ${currentMaterial}`;

                // if compost can be finish unhide the button
                finishButton.hidden = data.finish ? false : true;

                // if there is no current in progress, create
                createCompost();
            })
            // error checker
            .catch(error => {
                console.error(error);
                // loop back to check layering if there is error
                setTimeout(function () {
                    checkLayering();
                }, 1000);
            });
    }

    // go to initial function
    checkLayering();

    // this is process of waiting for esp32
    waitESP32 = () => {
        waitDiv.hidden = false;
        alertMixDiv.hidden = true;
        addLayerForm.hidden = true;
        currentRatio.hidden = true;
        ratioAfterAdding.hidden = true;

        // go to initial function
        checkLayering();
    }

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

                // get the brown and green ratio
                let brownRatio = Number(currentBrownWeight > 0 ? 1 : 0);
                let greenRatio;

                // if brown, calculate by dividing the weight to green
                if (currentMaterial == "Brown") {
                    // get the next green ratio
                    greenRatio = Number(currentGreenWeight / (
                        (data.weight == 0 && currentBrownWeight == 0) ?
                            1 : (data.weight + currentBrownWeight)
                        )
                    ).toLocaleString();
                }
                
                // if green, calculate by adding the weight to green
                else if (currentMaterial == "Green") {
                    greenRatio = Number( (currentGreenWeight + data.weight) / currentBrownWeight).toLocaleString(); // limit the decimal
                }
                
                // prompt the ratio to the web
                ratioAfterAdding.textContent = `Your current ratio after adding is: ${brownRatio} : ${greenRatio}`;

                // check if the submit buttons must be clickable or not depending on weight value and ratio
                submitFormButton.disabled = (data.weight <= 0) ? true : false;
                topBrownLayerButton.disabled = (data.weight <= 0) ? true : false;
                if (finishButton) {
                    // get the value of ratio depending on the high of ratio or lowness
                    let finalHighBrownWeight = Number(( (currentGreenWeight + data.weight) / lowRatio) - currentBrownWeight).toLocaleString();
                    let finalLowBrownWeight = Number(( (currentGreenWeight + data.weight) / highRatio) - currentBrownWeight).toLocaleString();

                    // check ratios and weight if finish button should be done or not
                    finishButton.disabled = (data.weight <= 0 || greenRatio < lowRatio || finalLowBrownWeight < 100) ? true : false;
                    
                    finishButton.textContent = `Finish up compost by adding this ${data.weight} green material and ${finalLowBrownWeight} to ${finalHighBrownWeight} top most brown material`;




                    // // check the next ratio
                    // let finalBrownWeight = Number((3 * (data.weight + currentGreenWeight)) - currentBrownWeight).toLocaleString();

                    // // check ratios and weight if finish button should be done or not
                    // finishButton.disabled = (data.weight <= 0 || brownRatio >= 3 || finalBrownWeight < 100) ? true : false;
                    // finishButton.textContent = `Finish up compost by adding this ${data.weight} green material and ${finalBrownWeight} top most brown material`
                };

                // loop back to get new weight
                createCompost();
            })
            // error checker
            .catch(error => {
                console.error(error);
                // loop back to check layering if there is error
                setTimeout(function () {
                    checkLayering();
                }, 1000);
            });
    }

    // change the hidden element to show
    mixRequest = () => {
        alertMixDiv.hidden = false;
        addLayerForm.hidden = true;
        currentRatio.hidden = true;
        ratioAfterAdding.hidden = true;
        topBrownLayerDiv.hidden = true;
    }

    // show only the div for top layer
    lastLayerFinish = () => {
        alertMixDiv.hidden = true;
        waitDiv.hidden = true;
        addLayerForm.hidden = true;
        topBrownLayerDiv.hidden = false;
        currentRatio.hidden = false;
        ratioAfterAdding.hidden = false;

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

                // go back to void of initial load to check new ratio etc
                checkLayering();
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
                currentRatio.hidden = false;
                ratioAfterAdding.hidden = false;

                // go back to check layering
                checkLayering();
            })
            // error checker
            .catch(error => console.error(error));
    })

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

    // if there is click in the top brown layer button
    const topBrownLayerButton = document.getElementById("topBrownLayerButton");
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
});