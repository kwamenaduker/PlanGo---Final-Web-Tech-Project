document.addEventListener("DOMContentLoaded", () => {
    const tripSelector = document.getElementById("trip-selector");
    const addBudgetForm = document.getElementById("add-budget-form");
    const addBudgetBtn = document.getElementById("add-budget-btn");
    const budgetList = document.getElementById("budget-list");
    let selectedTripId = null;

    // Fetch and Populate Trips Dropdown
    const fetchTrips = () => {
        fetch("php/fetch_trips_for_budget.php")
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

    // Fetch Budgets for Selected Trip
    const fetchBudgets = (tripId) => {
        fetch(`php/fetch_budgets.php?tripId=${tripId}`)
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    budgetList.innerHTML = ""; // Clear list

                    data.budgets.forEach((budget) => {
                        const li = document.createElement("li");
                        li.innerHTML = `
                            ${budget.Category}: $${budget.Amount}
                            <button class="update-btn" data-id="${budget.BudgetID}">Update</button>
                            <button class="delete-btn" data-id="${budget.BudgetID}">Delete</button>
                        `;
                        budgetList.appendChild(li);
                    });

                    attachBudgetEventListeners();
                } else {
                    budgetList.innerHTML = "<li>No budgets added yet.</li>";
                }
            })
            .catch((error) => {
                console.error("Error fetching budgets:", error);
            });
    };

    // Attach Event Listeners for Budget Actions
    const attachBudgetEventListeners = () => {
        document.querySelectorAll(".update-btn").forEach((button) => {
            button.addEventListener("click", (event) => {
                const budgetId = event.target.dataset.id;
                const newCategory = prompt("Enter new category:");
                const newAmount = prompt("Enter new amount:");

                if (newCategory && newAmount) {
                    updateBudget(budgetId, newCategory, newAmount);
                } else {
                    alert("Both category and amount are required.");
                }
            });
        });

        document.querySelectorAll(".delete-btn").forEach((button) => {
            button.addEventListener("click", (event) => {
                const budgetId = event.target.dataset.id;
                if (confirm("Are you sure you want to delete this budget?")) {
                    deleteBudget(budgetId);
                }
            });
        });
    };

    // Add Budget
    const handleAddBudget = () => {
        addBudgetForm.addEventListener("submit", (event) => {
            event.preventDefault();

            const formData = new FormData(addBudgetForm);
            formData.append("tripId", selectedTripId);

            fetch("php/add_budget.php", {
                method: "POST",
                body: formData,
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        alert("Budget added successfully!");
                        fetchBudgets(selectedTripId); // Refresh budgets
                        addBudgetForm.reset(); // Clear form
                    } else {
                        alert(data.error || "Failed to add budget.");
                    }
                })
                .catch((error) => {
                    console.error("Error adding budget:", error);
                });
        });
    };

    // Update Budget
    const updateBudget = (budgetId, category, amount) => {
        fetch("php/update_budget.php", {
            method: "POST",
            body: new URLSearchParams({ budgetId, category, amount }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    alert("Budget updated successfully!");
                    fetchBudgets(selectedTripId); // Refresh budgets
                } else {
                    alert(data.error || "Failed to update budget.");
                }
            })
            .catch((error) => {
                console.error("Error updating budget:", error);
            });
    };

    // Delete Budget
    const deleteBudget = (budgetId) => {
        fetch("php/delete_budget.php", {
            method: "POST",
            body: new URLSearchParams({ budgetId }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    alert("Budget deleted successfully!");
                    fetchBudgets(selectedTripId); // Refresh budgets
                } else {
                    alert(data.error || "Failed to delete budget.");
                }
            })
            .catch((error) => {
                console.error("Error deleting budget:", error);
            });
    };

    // Handle Trip Selection
    tripSelector.addEventListener("change", (event) => {
        selectedTripId = event.target.value;
        addBudgetBtn.disabled = false;
        fetchBudgets(selectedTripId);
    });

    fetchTrips();
    handleAddBudget();
});