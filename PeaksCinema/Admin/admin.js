function areYouSure(whatToDelete, IDtoDelete, nameToDelete, daterangeProperToDelete, theaterID) {
    const areYouSureScreenContainer = document.createElement('div');
    areYouSureScreenContainer.id = 'areYouSureScreenContainer';
    areYouSureScreenContainer.style.display = "flex";
    const areYouSureScreen = document.createElement('div');
    areYouSureScreen.id = 'areYouSureScreen';

    if (whatToDelete === "movie") {
        areYouSureScreen.textContent = "Do you really want to delete " + nameToDelete + "?";
    } else if (whatToDelete === "daterange") {
        areYouSureScreen.textContent = "Do you really want to delete this date range?";
        const cloneDaterangeProper = daterangeProperToDelete.cloneNode(true);
        cloneDaterangeProper.classList.add('daterangeProper');
        areYouSureScreen.append(cloneDaterangeProper);
    } else if (whatToDelete === "theater") {
        areYouSureScreen.textContent = "Do you really want to delete " + nameToDelete + "?";
    }

    const areYouSureScreenButtons = document.createElement('div');
    areYouSureScreenButtons.classList.add('areYouSureScreenButtons');

    const theBackButton = document.createElement('button');
    theBackButton.classList.add('generalAdminButton');
    theBackButton.id = 'theBackButton';
    theBackButton.textContent = "Back";

    theBackButton.addEventListener("click", function() {
        areYouSureScreenContainer.remove();
    })
    areYouSureScreenButtons.append(theBackButton);

    const deleteFinalButton = document.createElement('button');
    deleteFinalButton.classList.add('deleteDaterange');
    deleteFinalButton.textContent = "Yes, Delete";

    deleteFinalButton.addEventListener("click", function() {
        if (whatToDelete === "movie") {
            fetch(`http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=movie/${IDtoDelete}`, {
                method: "DELETE"
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                window.location.href = "admin_movie-gallery.php";
            })
            .catch(error => {
                console.error(error);
            })
        } else if (whatToDelete === "daterange") {
            fetch(`http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=daterange/${IDtoDelete}`, {
                method: "DELETE"
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                getDateranges(theaterID);
                areYouSureScreenContainer.remove();
            })
            .catch(error => {
                console.error(error);
            })
        } else if (whatToDelete === "theater") {
            fetch(`http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=theater/${IDtoDelete}`, {
                method: "DELETE"
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                window.location.href = "admin_theater-gallery.php";
            })
            .catch(error => {
                console.error(error);
            })
        }
    })
    areYouSureScreenButtons.append(deleteFinalButton);
    areYouSureScreen.append(areYouSureScreenButtons);
    areYouSureScreenContainer.append(areYouSureScreen);
    document.getElementById('content').append(areYouSureScreenContainer);
}

