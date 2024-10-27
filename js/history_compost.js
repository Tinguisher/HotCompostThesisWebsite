// Load js if HTML is done
document.addEventListener('DOMContentLoaded', function () {

    // get the data of the sensor for this compost id
    fetch(`../contexts/GetCompostHistoryProcess.php`)
        // get response as json
        .then(response => response.json())

        // get objects from fetch
        .then(data => {
            // if passing of data is success
            if (data.status == "success") {
                // go to creating the compost summary of this id
                createCompostRows(data.composts);
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

    // process of creating row for each compost 
    createCompostRows = (composts) => {
        // get the container for the sensors to be input and clear it
        const compostContainer = document.querySelector("[data-compost-container]");
        compostContainer.innerHTML = "";

        // get the reading
        composts.forEach(compost => {
            // get the template for sensor and clone it
            const compostTemplate = document.querySelector("[data-compost-template]");
            const row = compostTemplate.content.cloneNode(true).children[0];

            // get the template child that data can be inserted
            const compostID = row.querySelector("[data-compostID]");
            const status = row.querySelector("[data-status]");
            const createdAt = row.querySelector("[data-createdAt]");

            // place the data got from the fetch  
            compostID.textContent = compost.id;
            status.textContent = compost.status;
            createdAt.textContent = compost.createdAt;

            // put each made row inside sensorContainer
            compostContainer.appendChild(row);
        });
    }
});
