document.addEventListener("DOMContentLoaded", () => {
    const tripSelector = document.getElementById("trip-selector");
    const addItineraryForm = document.getElementById("add-itinerary-form");
    const itineraryList = document.getElementById("itinerary-list");
    let selectedTripId = null;

    // Fetch Trips and Populate Dropdown
    const fetchTrips = () => {
        fetch("php/fetch_trips_for_budget.php") // Reuse the fetch trips endpoint
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    tripSelector.innerHTML = '<option value="" disabled selected>Select a trip</option>';
                    data.trips.forEach((trip) => {
                        const option = document.createElement("option");
                        option.value = trip.TripID;
                        option.textContent = trip.TripName;
                        tripSelector.appendChild(option);
                    });
                } else {
                    alert(data.error || "Failed to fetch trips.");
                }
            })
            .catch((error) => {
                console.error("Error fetching trips:", error);
            });
    };

    // Fetch Itinerary for Selected Trip
    const fetchItinerary = (tripId) => {
        fetch(`php/fetch_itinerary.php?tripId=${tripId}`)
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    itineraryList.innerHTML = "";
                    if (data.itineraries.length > 0) {
                        data.itineraries.forEach((activity) => {
                            const li = document.createElement("li");
                            li.innerHTML = `
                                <div>
                                    <strong>${activity.Activity}</strong> (${activity.ActivityDate})<br>
                                    ${activity.StartTime} - ${activity.EndTime}<br>
                                    Notes: ${activity.Notes || "No notes"}
                                </div>
                                <div>
                                    <button class="update-btn" data-id="${activity.ItineraryID}">Update</button>
                                    <button class="delete-btn" data-id="${activity.ItineraryID}">Delete</button>
                                </div>
                            `;
                            itineraryList.appendChild(li);
                        });
                        attachItineraryEventListeners();
                    } else {
                        itineraryList.innerHTML = "<li>No activities added yet.</li>";
                    }
                } else {
                    alert(data.error || "Failed to fetch itinerary.");
                }
            })
            .catch((error) => {
                console.error("Error fetching itinerary:", error);
            });
    };

    // Attach Event Listeners for Itinerary Actions
    const attachItineraryEventListeners = () => {
        document.querySelectorAll(".update-btn").forEach((button) => {
            button.addEventListener("click", (event) => {
                const itineraryId = event.target.dataset.id;
                const newActivity = prompt("Enter new activity:");
                const newDate = prompt("Enter new date (YYYY-MM-DD):");
                const newStartTime = prompt("Enter new start time (HH:MM):");
                const newEndTime = prompt("Enter new end time (HH:MM):");
                const newNotes = prompt("Enter new notes:");

                if (newActivity && newDate && newStartTime && newEndTime) {
                    updateItinerary(itineraryId, newActivity, newDate, newStartTime, newEndTime, newNotes);
                } else {
                    alert("All fields except notes are required.");
                }
            });
        });

        document.querySelectorAll(".delete-btn").forEach((button) => {
            button.addEventListener("click", (event) => {
                const itineraryId = event.target.dataset.id;
                if (confirm("Are you sure you want to delete this activity?")) {
                    deleteItinerary(itineraryId);
                }
            });
        });
    };

    // Add Itinerary Activity
    const handleAddItinerary = () => {
        addItineraryForm.addEventListener("submit", (event) => {
            event.preventDefault();

            const formData = new FormData(addItineraryForm);
            formData.append("tripId", selectedTripId);

            fetch("php/add_itinerary.php", {
                method: "POST",
                body: formData,
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        alert("Activity added successfully!");
                        fetchItinerary(selectedTripId); // Refresh itinerary
                        addItineraryForm.reset(); // Clear form
                    } else {
                        alert(data.error || "Failed to add activity.");
                    }
                })
                .catch((error) => {
                    console.error("Error adding activity:", error);
                });
        });
    };

    // Update Itinerary Activity
    const updateItinerary = (itineraryId, activity, activityDate, startTime, endTime, notes) => {
        fetch("php/update_itinerary.php", {
            method: "POST",
            body: new URLSearchParams({
                itineraryId,
                activity,
                activityDate,
                startTime,
                endTime,
                notes,
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    alert("Activity updated successfully!");
                    fetchItinerary(selectedTripId); // Refresh itinerary
                } else {
                    alert(data.error || "Failed to update activity.");
                }
            })
            .catch((error) => {
                console.error("Error updating activity:", error);
            });
    };

    // Delete Itinerary Activity
    const deleteItinerary = (itineraryId) => {
        fetch("php/delete_itinerary.php", {
            method: "POST",
            body: new URLSearchParams({ itineraryId }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    alert("Activity deleted successfully!");
                    fetchItinerary(selectedTripId); // Refresh itinerary
                } else {
                    alert(data.error || "Failed to delete activity.");
                }
            })
            .catch((error) => {
                console.error("Error deleting activity:", error);
            });
    };

    // Handle Trip Selection
    tripSelector.addEventListener("change", (event) => {
        selectedTripId = event.target.value;
        fetchItinerary(selectedTripId);
    });

    fetchTrips();
    handleAddItinerary();
});
