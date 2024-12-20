document.addEventListener("DOMContentLoaded", function () {
    // Utility Functions
    const isValidEmail = (email) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    const isValidName = (name) => /^[a-zA-Z\s]+$/.test(name);
    const isValidPassword = (password) =>
        /^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>]).{8,}$/.test(password);

    const displayError = (message) => {
        const errorContainer = document.getElementById("error-message");
        if (errorContainer) {
            errorContainer.textContent = message;
            errorContainer.style.color = "red";
        } else {
            alert(message); // Fallback for missing error container
        }
    };

    const clearError = () => {
        const errorContainer = document.getElementById("error-message");
        if (errorContainer) {
            errorContainer.textContent = ""; // Clear previous error messages
        }
    };

    // Registration Form Handling
    const handleRegisterForm = () => {
        const registerForm = document.querySelector("#register-form");
        if (registerForm) {
            registerForm.addEventListener("submit", function (event) {
                event.preventDefault();
                clearError();

                const fname = document.getElementById("fname").value.trim();
                const lname = document.getElementById("lname").value.trim();
                const email = document.getElementById("email").value.trim();
                const password = document.getElementById("password").value;
                const confirmPassword = document.getElementById("confirm-password").value;

                if (!fname || !isValidName(fname)) {
                    displayError("First name is required and must only contain letters.");
                    return;
                }
                if (!lname || !isValidName(lname)) {
                    displayError("Last name is required and must only contain letters.");
                    return;
                }
                if (!email || !isValidEmail(email)) {
                    displayError("Please enter a valid email address.");
                    return;
                }
                if (!isValidPassword(password)) {
                    displayError("Password must be at least 8 characters, include an uppercase letter, a digit, and a special character.");
                    return;
                }
                if (password !== confirmPassword) {
                    displayError("Passwords do not match.");
                    return;
                }

                const formData = new FormData(registerForm);
                fetch("php/register.php", {
                    method: "POST",
                    body: formData,
                })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            window.location.href = "login.html"; // Redirect on success
                        } else {
                            displayError(data.error);
                        }
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                        displayError("An unexpected error occurred. Please try again.");
                    });
            });
        }
    };

    // Login Form Handling
    const handleLoginForm = () => {
        const loginForm = document.querySelector("form[action='php/login.php']");
        if (loginForm) {
            loginForm.addEventListener("submit", function (event) {
                event.preventDefault();
                clearError();

                const email = document.getElementById("email").value.trim();
                const password = document.getElementById("password").value;

                if (!email || !isValidEmail(email)) {
                    displayError("Please enter a valid email address.");
                    return;
                }
                if (!password) {
                    displayError("Password is required.");
                    return;
                }

                const formData = new FormData(loginForm);
                fetch("php/login.php", {
                    method: "POST",
                    body: formData,
                })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            if (data.role === "Admin") {
                                window.location.href = "admin_dashboard.html"; // Redirect admin to admin dashboard
                            } else {
                                window.location.href = "dashboard.html"; // Redirect regular users to the user dashboard
                            }
                        } else {
                            displayError(data.error);
                        }
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                        displayError("An unexpected error occurred. Please try again.");
                    });
            });
        }
    };


    // Fetch and Update Dashboard Data
    const fetchDashboardData = () => {
        fetch("php/fetch_dashboard_data.php")
            .then((response) => response.json())
            .then((data) => {
                console.log(data); // Debug response
                if (data.success) {
                    document.getElementById("welcome-message").textContent = `Welcome To Your Dashboard, ${data.user.fname}!`;
                    document.getElementById("plans-count").textContent = data.stats.plansCreated;
                    document.getElementById("upcoming-trips").textContent = data.stats.upcomingTrips;
                    document.getElementById("saved-places").textContent = data.stats.savedPlaces;

                    const activityList = document.getElementById("activity-list");
                    activityList.innerHTML = ""; // Clear existing items
                    if (data.activities.length > 0) {
                        data.activities.forEach((activity) => {
                            const li = document.createElement("li");
                            li.textContent = activity;
                            activityList.appendChild(li);
                        });
                    } else {
                        activityList.innerHTML = "<li>No recent activities found.</li>";
                    }
                } else {
                    alert(data.error || "Failed to load dashboard data.");
                }
            })
            .catch((error) => {
                console.error("Error fetching dashboard data:", error);
            });
    };

    // Fetch and Update Saved Places
    const fetchSavedPlaces = () => {
        fetch("php/fetch_saved_places.php")
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    const savedPlacesList = document.getElementById("saved-places-list");
                    savedPlacesList.innerHTML = ""; // Clear existing items

                    if (data.success && data.places.length > 0) {
                        data.places.forEach((place) => {
                            const li = document.createElement("li");
                            li.innerHTML = `
                            <strong>${place.PlaceName}</strong> (${place.Location || "No Location"})<br>
                            Notes: ${place.Notes || "No Notes"}<br>
                            <button class="edit-btn" data-id="${place.PlaceID}" 
                                    data-name="${place.PlaceName}" 
                                    data-location="${place.Location}" 
                                    data-notes="${place.Notes}">
                                Edit
                            </button>
                            <button class="delete-btn" data-id="${place.PlaceID}">
                                Delete
                            </button>
                        `;
                            savedPlacesList.appendChild(li);
                        });

                        attachPlaceEventListeners(); // Attach events to newly rendered buttons
                    } else {
                        savedPlacesList.innerHTML = "<li>No saved places yet.</li>";
                    }
                } else {
                    displayError(data.error || "Failed to fetch saved places.");
                }
            })
            .catch((error) => {
                console.error("Error fetching saved places:", error);
                displayError("An error occurred while fetching saved places.");
            });
    };

    // Add Place Form Handling
    const handleAddPlaceForm = () => {
        const addPlaceForm = document.getElementById("add-place-form");
        if (addPlaceForm) {
            addPlaceForm.addEventListener("submit", (event) => {
                event.preventDefault();

                const formData = new FormData(addPlaceForm);
                fetch("php/save_place.php", {
                    method: "POST",
                    body: formData,
                })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            alert(data.message || "Place saved successfully!");
                            fetchSavedPlaces(); // Refresh saved places
                            fetchDashboardData(); // Refresh dashboard widgets
                            addPlaceForm.reset(); // Clear form fields
                        } else {
                            displayError(data.error || "Failed to save place.");
                        }
                    })
                    .catch((error) => {
                        console.error("Error saving place:", error);
                        displayError("An error occurred while saving the place.");
                    });
            });
        }
    };

    const updatePlace = (placeId, placeName, location, notes) => {
        const formData = new URLSearchParams();
        formData.append("placeId", placeId);
        formData.append("placeName", placeName);
        formData.append("location", location || "");
        formData.append("notes", notes || "");
    
        fetch("php/update_place.php", {
            method: "POST",
            body: formData,
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    alert(data.message || "Place updated successfully!");
                    fetchSavedPlaces(); // Refresh the saved places list
                } else {
                    alert(data.error || "Failed to update place.");
                }
            })
            .catch((error) => {
                console.error("Error updating place:", error);
                alert("An unexpected error occurred.");
            });
    };
    
    
    const deletePlace = (placeId) => {
        if (confirm("Are you sure you want to delete this place?")) {
            const formData = new URLSearchParams();
            formData.append("placeId", placeId);
    
            fetch("php/delete_place.php", {
                method: "POST",
                body: formData,
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        alert(data.message || "Place deleted successfully!");
                        fetchSavedPlaces(); // Refresh the saved places list
                    } else {
                        alert(data.error || "Failed to delete place.");
                    }
                })
                .catch((error) => {
                    console.error("Error deleting place:", error);
                    alert("An unexpected error occurred.");
                });
        }
    };
    

    // Attach Event Listeners for Edit and Delete
    const attachPlaceEventListeners = () => {
        document.querySelectorAll(".edit-btn").forEach((button) => {
            button.addEventListener("click", (event) => {
                const placeId = event.target.dataset.id;
                const placeName = event.target.dataset.name;
                const location = event.target.dataset.location;
                const notes = event.target.dataset.notes;

                // Open an edit form or handle inline editing
                const newName = prompt("Update Place Name:", placeName);
                const newLocation = prompt("Update Location:", location);
                const newNotes = prompt("Update Notes:", notes);

                if (newName !== null) {
                    updatePlace(placeId, newName, newLocation, newNotes);
                }
            });
        });

        document.querySelectorAll(".delete-btn").forEach((button) => {
            button.addEventListener("click", (event) => {
                const placeId = event.target.dataset.id;
                deletePlace(placeId); // Call the deletePlace function
            });
        });
    };
    
    

    // Handle Create Trip Form
    const handleCreateTripForm = () => {
        const createTripForm = document.getElementById("create-trip-form");

        if (createTripForm) {
            createTripForm.addEventListener("submit", (event) => {
                event.preventDefault(); // Prevent default form submission behavior

                const errorMessage = document.getElementById("error-message");
                errorMessage.textContent = ""; // Clear previous error messages

                const planName = document.getElementById("plan-name").value.trim();
                const destination = document.getElementById("destination").value.trim();
                const startDate = document.getElementById("start-date").value;
                const endDate = document.getElementById("end-date").value;
                const notes = document.getElementById("notes").value.trim();

                // Validate input
                if (!planName) {
                    errorMessage.textContent = "Trip Name is required.";
                    return;
                }
                if (!destination) {
                    errorMessage.textContent = "Destination is required.";
                    return;
                }
                if (!startDate || !endDate) {
                    errorMessage.textContent = "Both Start Date and End Date are required.";
                    return;
                }
                if (new Date(startDate) > new Date(endDate)) {
                    errorMessage.textContent = "Start Date cannot be later than End Date.";
                    return;
                }

                // Prepare form data
                const formData = new FormData();
                formData.append("plan_name", planName);
                formData.append("destination", destination);
                formData.append("start_date", startDate);
                formData.append("end_date", endDate);
                formData.append("notes", notes);

                // Submit form via AJAX
                fetch("php/create_trip.php", {
                    method: "POST",
                    body: formData,
                })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            alert("Trip created successfully!");
                            window.location.href = "dashboard.html"; // Redirect to dashboard
                        } else {
                            errorMessage.textContent = data.error || "Failed to create trip. Please try again.";
                        }
                    })
                    .catch((error) => {
                        console.error("Error creating trip:", error);
                        errorMessage.textContent = "An unexpected error occurred. Please try again.";
                    });
            });
        }
    };

    // Handle Update Trip Form
    const handleUpdateTrip = () => {
        const urlParams = new URLSearchParams(window.location.search);
        const tripId = urlParams.get("tripId");

        console.log("tripId from URL:", tripId); // Debugging line

        if (!tripId) {
            alert("No Trip ID provided.");
            return;
        }

        const updateTripForm = document.getElementById("update-trip-form");
        

        updateTripForm.addEventListener("submit", (event) => {
            event.preventDefault();

            const tripName = document.getElementById("trip-name").value.trim();
            const destination = document.getElementById("destination").value.trim();
            const startDate = document.getElementById("start-date").value;
            const endDate = document.getElementById("end-date").value;
            const description = document.getElementById("description").value.trim();

            // Validate input
            if (!tripName || !destination || !startDate || !endDate) {
                alert("All fields except description are required.");
                return;
            }

            if (new Date(startDate) > new Date(endDate)) {
                alert("Start Date cannot be later than End Date.");
                return;
            }

            // Prepare form data
            const formData = new FormData();
            formData.append("tripId", tripId);
            formData.append("tripName", tripName);
            formData.append("destination", destination);
            formData.append("startDate", startDate);
            formData.append("endDate", endDate);
            formData.append("description", description);

            // Submit form data via AJAX
            fetch("php/update_trip.php", {
                method: "POST",
                body: formData,
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        alert("Trip updated successfully!");
                        window.location.href = "dashboard.html";
                    } else {
                        alert(data.error || "Failed to update trip.");
                    }
                })
                .catch((error) => {
                    console.error("Error updating trip:", error);
                    alert("An unexpected error occurred. Please try again.");
                });
        });
    };


    // Fetch and Display Trips
    const fetchTrips = () => {
        const searchQuery = document.getElementById("search-bar").value.trim();
        const startDate = document.getElementById("filter-start-date").value;
        const endDate = document.getElementById("filter-end-date").value;

        // Construct the URL with parameters
        const params = new URLSearchParams({
            search: searchQuery,
            startDate: startDate || "",
            endDate: endDate || "",
        });

        // Ensure the `?` character is added before params
        fetch("php/fetch_trips.php?" + params.toString())
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    const upcomingTripsList = document.getElementById("upcoming-trips-list");
                    const previousTripsList = document.getElementById("previous-trips-list");

                    // Render Upcoming Trips
                    upcomingTripsList.innerHTML = "";
                    if (data.upcomingTrips.length > 0) {
                        data.upcomingTrips.forEach((trip) => {
                            const li = document.createElement("li");
                            li.innerHTML = `
                                <strong>${trip.TripName}</strong> (${trip.Destination || "No Destination"})<br>
                                ${trip.StartDate} - ${trip.EndDate}<br>
                                <button class="update-btn" data-id="${trip.TripID}">Update</button>
                                <button class="delete-btn" data-id="${trip.TripID}">Delete</button>`;
                            upcomingTripsList.appendChild(li);
                        });
                    } else {
                        upcomingTripsList.innerHTML = "<li>No upcoming trips found.</li>";
                    }

                    // Render Previous Trips
                    previousTripsList.innerHTML = "";
                    if (data.previousTrips.length > 0) {
                        data.previousTrips.forEach((trip) => {
                            const li = document.createElement("li");
                            li.innerHTML = `
                                <strong>${trip.TripName}</strong> (${trip.Destination || "No Destination"})<br>
                                ${trip.StartDate} - ${trip.EndDate}<br>
                                <button class="delete-btn" data-id="${trip.TripID}">Delete</button>`;
                            previousTripsList.appendChild(li);
                        });
                    } else {
                        previousTripsList.innerHTML = "<li>No previous trips found.</li>";
                    }

                    attachTripEventListeners(); // Attach events to buttons
                } else {
                    alert(data.error || "Failed to fetch trips.");
                }
            })
            .catch((error) => {
                console.error("Error fetching trips:", error);
            });
    };

    const attachTripEventListeners = () => {
        document.querySelectorAll(".update-btn").forEach((button) => {
            button.addEventListener("click", (event) => {
                const tripId = event.target.dataset.id;
                window.location.href = `update_trip.html?tripId=${tripId}`;
            });
        });

        document.querySelectorAll(".delete-btn").forEach((button) => {
            button.addEventListener("click", (event) => {
                const tripId = event.target.dataset.id;
                if (confirm("Are you sure you want to delete this trip?")) {
                    fetch("php/delete_trip.php", {
                        method: "POST",
                        body: new URLSearchParams({ tripId }),
                    })
                        .then((response) => response.json())
                        .then((data) => {
                            if (data.success) {
                                alert("Trip deleted successfully!");
                                fetchTrips();
                            } else {
                                alert(data.error || "Failed to delete trip.");
                            }
                        })
                        .catch((error) => {
                            console.error("Error deleting trip:", error);
                        });
                }
            });
        });
    };
    

    const initializeApp = () => {
        const currentPage = window.location.pathname.split("/").pop();

        if (currentPage === "dashboard.html") {
            fetchDashboardData();
            fetchSavedPlaces();
            handleAddPlaceForm();
        }

        if (currentPage === "trip_details.html") {
            fetchTrips();

            const applyFiltersButton = document.getElementById("apply-filters");

            if (applyFiltersButton) {
                applyFiltersButton.addEventListener("click", () => {
                    fetchTrips();
                });
            } else {
                console.error("apply-filters button not found on trip_details.html");
            }
        }

        if (currentPage === "register.html") {
            handleRegisterForm();
        }

        if (currentPage === "login.html") {
            handleLoginForm();
        }

        if (currentPage === "create_trip.html") {
            handleCreateTripForm();
        }

        if (currentPage === "update_trip.html") {
            handleUpdateTrip();
        }
    };

    initializeApp();
});
