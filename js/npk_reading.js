// Load js if HTML is done
document.addEventListener('DOMContentLoaded', function () {
    // if there is click in back button, go to dashboard
    const backButton = document.getElementById("backButton");
    backButton.addEventListener('click', () => {
        window.location = '../pages/dashboard.html';
    })

    // if there is click in logo, go to dashboard
    const logoButton = document.getElementById("logoButton");
    logoButton.addEventListener('click', () => {
        window.location = '../pages/dashboard.html';
    })

    // make a request of NPK to ESP32
    fetch('../contexts/GetNPKUseProcess.php')
        // get response as json
        .then(response => response.json())
        // get objects from fetch
        .then(data => {
            // go to request npk again if there is data error
            if (data.status == "error") return (console.error(data.message));

            // proceed on getting NPK Content
            getNPKContent();
        })
        // error checker
        .catch(error => console.error(error));

    // this is process of getting NPK from database
    getNPKContent = () => {
        // get npk values from database
        fetch('../contexts/GetNPKValuesProcess.php')
            // get response as json
            .then(response => response.json())
            // get objects from fetch
            .then(data => {
                // get the ids of the input NPK content
                const nitrogenPercent = document.getElementById("nitrogenPercent");
                const phosphorusPercent = document.getElementById("phosphorusPercent");
                const potassiumPercent = document.getElementById("potassiumPercent");

                // put the values of NPK content
                nitrogenPercent.textContent = `${data.nitrogen}%`;
                phosphorusPercent.textContent = `${data.phosphorus}%`;
                potassiumPercent.textContent = `${data.potassium}%`;

                // loop back to get new NPK
                getNPKContent();
            })
            // error checker
            .catch(error => {
                console.error(error);
                // loop back to check layering if there is error
                setTimeout(function () {
                    getNPKContent();
                }, 1000);
            });
    }
});