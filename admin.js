document.addEventListener('DOMContentLoaded', () => {
    // Get the current page name from the URL
    const currentPage = window.location.pathname.split('/').pop();
    
    // Initialize different functionality based on the current page
    switch (currentPage) {
        case 'admin_dashboard.html':
            initializeAdminDashboard();
            break;
        case 'admin_users.html':
            initializeUserManagement();
            break;
        case 'admin_trips.html':
            initializeTripManagement();
            break;
    }
});

// Function to load admin dashboard statistics
const loadAdminStats = async () => {
    try {
        const response = await fetch('php/get_admin_stats.php');
        const data = await response.json();

        if (data.success) {
            // Update statistics cards
            document.getElementById('total-users').textContent = data.data.total_users;
            document.getElementById('popular-destination').textContent = data.data.popular_destination;
            document.getElementById('average-budget').textContent = `$${data.data.average_budget}`;
        } else {
            console.error('Error loading stats:', data.error);
        }
    } catch (error) {
        console.error('Error:', error);
    }
};

// Function to load admin info
const loadAdminInfo = async () => {
    try {
        const response = await fetch('php/get_admin_info.php');
        const data = await response.json();

        if (data.success) {
            const adminName = document.getElementById('admin-name');
            if (adminName) {
                adminName.textContent = `${data.admin.FirstName} ${data.admin.LastName}`;
            }
        } else {
            console.error('Error loading admin info:', data.error);
        }
    } catch (error) {
        console.error('Error:', error);
    }
};

// Initialize admin dashboard
const initializeAdminDashboard = () => {
    loadAdminStats();
    loadAdminInfo();
    // Set current date
    const currentDate = document.getElementById('current-date');
    if (currentDate) {
        const date = new Date();
        currentDate.textContent = date.toLocaleDateString('en-US', { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
    }
};

// User Management Functions
const initializeUserManagement = () => {
    loadUserList(); // Initial load
    
    const createUserForm = document.getElementById('create-user-form');
    if (createUserForm) {
        createUserForm.addEventListener('submit', handleCreateUser);
    }
    
    // Add refresh button functionality
    const refreshButton = document.getElementById('refresh-users');
    if (refreshButton) {
        refreshButton.addEventListener('click', loadUserList);
    }
    
    const roleFilter = document.getElementById('filter-role');
    const searchUser = document.getElementById('search-user');
    
    if (roleFilter) {
        roleFilter.addEventListener('change', filterUsers);
    }
    
    if (searchUser) {
        let debounceTimer;
        searchUser.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                console.log('Search input:', searchUser.value);
                loadUserList();
            }, 300);
        });
    }
};

const filterUsers = () => {
    const roleFilter = document.getElementById('filter-role').value.toLowerCase();
    const searchQuery = document.getElementById('search-user').value.toLowerCase();
    const userCards = document.querySelectorAll('.user-card');

    userCards.forEach(card => {
        const role = card.querySelector('.user-info p:nth-child(3)').textContent.toLowerCase();
        const userText = card.textContent.toLowerCase();
        
        const matchesRole = !roleFilter || role.includes(roleFilter);
        const matchesSearch = !searchQuery || userText.includes(searchQuery);
        
        card.style.display = matchesRole && matchesSearch ? 'flex' : 'none';
    });
};

const loadUserList = async () => {
    try {
        const response = await fetch('php/get_users.php');
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('User list response:', data); // Debug log
        
        if (data.success) {
            const userList = document.getElementById('user-list');
            if (!userList) {
                console.error('User list container not found');
                return;
            }
            
            userList.innerHTML = ''; // Clear existing list
            
            data.users.forEach(user => {
                const userCard = document.createElement('div');
                userCard.className = 'user-card';
                userCard.setAttribute('data-userid', user.UserID);
                userCard.innerHTML = `
                    <div class="user-info">
                        <h3>${user.FirstName} ${user.LastName}</h3>
                        <p>Email: ${user.Email}</p>
                        <p>Role: ${user.Role}</p>
                        <p>Created: ${new Date(user.CreatedAt).toLocaleDateString()}</p>
                    </div>
                    <div class="user-actions">
                        <button onclick="editUser(${user.UserID})" class="edit-btn">Edit</button>
                        <button onclick="deleteUser(${user.UserID})" class="delete-btn">Delete</button>
                    </div>
                `;
                userList.appendChild(userCard);
            });

            // Apply any existing filters
            filterUsers();
        } else {
            console.error('Failed to load users:', data.error);
        }
    } catch (error) {
        console.error('Error loading user list:', error);
    }
};

const handleCreateUser = async (e) => {
    e.preventDefault();
    
    const formData = {
        firstname: document.getElementById('firstname').value,
        lastname: document.getElementById('lastname').value,
        email: document.getElementById('email').value,
        password: document.getElementById('password').value,
        role: document.getElementById('role').value
    };

    try {
        const response = await fetch('php/create_user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();
        
        if (data.success) {
            alert('User created successfully!');
            e.target.reset();
            await loadUserList(); // Refresh the user list immediately
        } else {
            alert(data.error || 'Failed to create user');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to create user. Please try again.');
    }
};

// Delete user function
const deleteUser = async (userId) => {
    if (!confirm('Are you sure you want to delete this user?')) {
        return;
    }

    try {
        const response = await fetch('php/delete_user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ userId: userId })
        });

        const data = await response.json();
        
        if (data.success) {
            alert('User deleted successfully!');
            await loadUserList(); // Refresh the list after deletion
        } else {
            alert(data.error || 'Failed to delete user');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to delete user. Please try again.');
    }
};

// Edit user function
const editUser = async (userId) => {
    const userCard = document.querySelector(`.user-card[data-userid="${userId}"]`);
    const userInfo = userCard.querySelector('.user-info');
    
    // Get current values
    const firstName = userInfo.querySelector('h3').textContent.split(' ')[0];
    const lastName = userInfo.querySelector('h3').textContent.split(' ')[1];
    const email = userInfo.querySelector('p:nth-child(2)').textContent.replace('Email: ', '');
    const role = userInfo.querySelector('p:nth-child(3)').textContent.replace('Role: ', '');
    
    // Create edit form
    const editForm = document.createElement('form');
    editForm.className = 'edit-user-form';
    editForm.innerHTML = `
        <div class="form-group">
            <input type="text" name="firstname" value="${firstName}" required>
        </div>
        <div class="form-group">
            <input type="text" name="lastname" value="${lastName}" required>
        </div>
        <div class="form-group">
            <input type="email" name="email" value="${email}" required>
        </div>
        <div class="form-group">
            <select name="role" required>
                <option value="Admin" ${role === 'Admin' ? 'selected' : ''}>Admin</option>
                <option value="User" ${role === 'User' ? 'selected' : ''}>User</option>
            </select>
        </div>
        <div class="form-actions">
            <button type="submit" class="save-btn">Save</button>
            <button type="button" class="cancel-btn" onclick="cancelEdit(${userId})">Cancel</button>
        </div>
    `;
    
    // Replace user info with edit form
    userInfo.style.display = 'none';
    userCard.insertBefore(editForm, userCard.querySelector('.user-actions'));
    
    // Handle form submission
    editForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = {
            userId: userId,
            firstname: editForm.firstname.value,
            lastname: editForm.lastname.value,
            email: editForm.email.value,
            role: editForm.role.value
        };
        
        try {
            const response = await fetch('php/edit_user.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            });
            
            const data = await response.json();
            
            if (data.success) {
                alert('User updated successfully!');
                await loadUserList(); // Refresh the list
            } else {
                alert(data.error || 'Failed to update user');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Failed to update user. Please try again.');
        }
    });
};

// Cancel edit function
const cancelEdit = (userId) => {
    const userCard = document.querySelector(`.user-card[data-userid="${userId}"]`);
    const editForm = userCard.querySelector('.edit-user-form');
    const userInfo = userCard.querySelector('.user-info');
    
    if (editForm) {
        editForm.remove();
        userInfo.style.display = 'block';
    }
};

// Trip Management Functions
const initializeTripManagement = () => {
    loadTrips(); // Initial load
    loadUserSelect();
    
    // Initialize filters
    const filterUser = document.getElementById('filter-user');
    const searchTrip = document.getElementById('search-trip');
    const createTripForm = document.getElementById('create-trip-form');

    if (filterUser) {
        filterUser.addEventListener('change', () => {
            console.log('Filter changed:', filterUser.value);
            loadTrips();
        });
    }

    if (searchTrip) {
        let debounceTimer;
        searchTrip.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                console.log('Search input:', searchTrip.value);
                loadTrips();
            }, 300);
        });
    }

    if (createTripForm) {
        createTripForm.addEventListener('submit', handleCreateTrip);
    }
};

const handleCreateTrip = async (e) => {
    e.preventDefault();
    
    const formData = {
        userId: document.getElementById('user-select').value,
        tripName: document.getElementById('trip-name').value,
        destination: document.getElementById('destination').value,
        startDate: document.getElementById('start-date').value,
        endDate: document.getElementById('end-date').value,
        budget: parseFloat(document.getElementById('budget').value),
        description: document.getElementById('description').value
    };

    // Validate dates
    const startDate = new Date(formData.startDate);
    const endDate = new Date(formData.endDate);
    
    if (endDate < startDate) {
        alert('End date cannot be before start date');
        return;
    }

    // Validate budget
    if (formData.budget <= 0) {
        alert('Budget must be greater than 0');
        return;
    }

    try {
        const response = await fetch('php/create_trip.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();
        
        if (data.success) {
            alert('Trip created successfully!');
            e.target.reset();
            await loadTrips(); // Refresh the trip list
            await loadUserSelect(); // Refresh user select in case it was updated
        } else {
            alert(data.error || 'Failed to create trip');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to create trip. Please try again.');
    }
};

const loadUserSelect = async () => {
    try {
        const response = await fetch('php/get_users.php');
        const data = await response.json();
        
        if (data.success) {
            const userSelect = document.getElementById('user-select');
            const filterUser = document.getElementById('filter-user');
            
            // Clear existing options
            userSelect.innerHTML = '<option value="">Select User</option>';
            filterUser.innerHTML = '<option value="">All Users</option>';
            
            data.users.forEach(user => {
                const option = `<option value="${user.UserID}">${user.FirstName} ${user.LastName} (${user.Email})</option>`;
                userSelect.insertAdjacentHTML('beforeend', option);
                filterUser.insertAdjacentHTML('beforeend', option);
            });
        }
    } catch (error) {
        console.error('Error loading users:', error);
    }
};

const loadTrips = async () => {
    try {
        const filterUser = document.getElementById('filter-user');
        const searchTrip = document.getElementById('search-trip');
        const selectedUser = filterUser ? filterUser.value : '';
        const searchQuery = searchTrip ? searchTrip.value : '';

        let url = 'php/manage_trips.php?action=list';
        if (selectedUser) {
            url += `&user_id=${selectedUser}`;
        }
        if (searchQuery) {
            url += `&search=${encodeURIComponent(searchQuery)}`;
        }

        const response = await fetch(url);
        const data = await response.json();
        
        if (data.success) {
            const tripList = document.getElementById('trip-list');
            tripList.innerHTML = '';
            
            data.trips.forEach(trip => {
                const tripCard = document.createElement('div');
                tripCard.className = 'trip-card';
                tripCard.setAttribute('data-tripid', trip.TripID);
                tripCard.setAttribute('data-userid', trip.UserID);
                tripCard.setAttribute('data-description', trip.Description || '');
                tripCard.innerHTML = `
                    <h4>${trip.TripName}</h4>
                    <div class="trip-details">
                        <div class="trip-detail-item">
                            <span class="trip-detail-label">User</span>
                            <span class="trip-detail-value">${trip.FirstName} ${trip.LastName}</span>
                        </div>
                        <div class="trip-detail-item">
                            <span class="trip-detail-label">Destination</span>
                            <span class="trip-detail-value">${trip.Destination}</span>
                        </div>
                        <div class="trip-detail-item">
                            <span class="trip-detail-label">Dates</span>
                            <span class="trip-detail-value">${formatDate(trip.StartDate)} - ${formatDate(trip.EndDate)}</span>
                        </div>
                        <div class="trip-detail-item">
                            <span class="trip-detail-label">Budget</span>
                            <span class="trip-detail-value">$${parseFloat(trip.Budget).toFixed(2)}</span>
                        </div>
                    </div>
                    <div class="trip-actions">
                        <button class="trip-action-btn edit-btn" onclick="editTrip(${trip.TripID})">Edit</button>
                        <button class="trip-action-btn delete-btn" onclick="deleteTrip(${trip.TripID})">Delete</button>
                    </div>
                `;
                tripList.appendChild(tripCard);
            });
        }
    } catch (error) {
        console.error('Error loading trips:', error);
    }
};

const formatDate = (dateString) => {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

// Edit trip function
const editTrip = async (tripId) => {
    const tripCard = document.querySelector(`.trip-card[data-tripid="${tripId}"]`);
    const tripDetails = tripCard.querySelector('.trip-details');
    
    // Get current values
    const tripName = tripCard.querySelector('h4').textContent;
    const userFullName = tripDetails.querySelector('.trip-detail-item:nth-child(1) .trip-detail-value').textContent;
    const destination = tripDetails.querySelector('.trip-detail-item:nth-child(2) .trip-detail-value').textContent;
    const dates = tripDetails.querySelector('.trip-detail-item:nth-child(3) .trip-detail-value').textContent.split(' - ');
    const budget = tripDetails.querySelector('.trip-detail-item:nth-child(4) .trip-detail-value').textContent.replace('$', '');
    
    // Create edit form
    const editForm = document.createElement('form');
    editForm.className = 'edit-trip-form';
    editForm.innerHTML = `
        <div class="form-group">
            <label>Trip Name</label>
            <input type="text" name="tripName" value="${tripName}" required>
        </div>
        <div class="form-group">
            <label>User</label>
            <select name="userId" required>
                <!-- Will be populated by loadUserSelect -->
            </select>
        </div>
        <div class="form-group">
            <label>Destination</label>
            <input type="text" name="destination" value="${destination}" required>
        </div>
        <div class="form-group">
            <label>Start Date</label>
            <input type="date" name="startDate" value="${dates[0]}" required>
        </div>
        <div class="form-group">
            <label>End Date</label>
            <input type="date" name="endDate" value="${dates[1]}" required>
        </div>
        <div class="form-group">
            <label>Budget</label>
            <input type="number" name="budget" value="${parseFloat(budget)}" step="0.01" required>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" required>${tripCard.dataset.description || ''}</textarea>
        </div>
        <div class="form-actions">
            <button type="submit" class="save-btn">Save Changes</button>
            <button type="button" class="cancel-btn" onclick="cancelTripEdit(${tripId})">Cancel</button>
        </div>
    `;
    
    // Hide trip details and show edit form
    tripDetails.style.display = 'none';
    tripCard.insertBefore(editForm, tripCard.querySelector('.trip-actions'));
    
    // Populate user select
    const userSelect = editForm.querySelector('select[name="userId"]');
    await populateUserSelect(userSelect, tripCard.dataset.userid);
    
    // Handle form submission
    editForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = {
            tripId: tripId,
            tripName: editForm.tripName.value,
            userId: editForm.userId.value,
            destination: editForm.destination.value,
            startDate: editForm.startDate.value,
            endDate: editForm.endDate.value,
            budget: parseFloat(editForm.budget.value),
            description: editForm.description.value
        };
        
        try {
            const response = await fetch('php/edit_trip.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            });
            
            const data = await response.json();
            
            if (data.success) {
                alert('Trip updated successfully!');
                await loadTrips(); // Refresh the list
            } else {
                alert(data.error || 'Failed to update trip');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Failed to update trip. Please try again.');
        }
    });
};

// Cancel trip edit function
const cancelTripEdit = (tripId) => {
    const tripCard = document.querySelector(`.trip-card[data-tripid="${tripId}"]`);
    const editForm = tripCard.querySelector('.edit-trip-form');
    const tripDetails = tripCard.querySelector('.trip-details');
    
    if (editForm) {
        editForm.remove();
        tripDetails.style.display = 'block';
    }
};

// Helper function to populate user select
const populateUserSelect = async (selectElement, selectedUserId) => {
    try {
        const response = await fetch('php/get_users.php');
        const data = await response.json();
        
        if (data.success) {
            selectElement.innerHTML = '<option value="">Select User</option>';
            
            data.users.forEach(user => {
                const option = document.createElement('option');
                option.value = user.UserID;
                option.textContent = `${user.FirstName} ${user.LastName}`;
                option.selected = user.UserID === selectedUserId;
                selectElement.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error loading users:', error);
    }
};

// Delete trip
async function deleteTrip(tripId) {
    if (!confirm('Are you sure you want to delete this trip?')) {
        return;
    }

    try {
        const response = await fetch('php/delete_trip.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ tripId }),
        });

        const data = await response.json();

        if (data.success) {
            // Refresh the trip list
            loadTrips();
        } else {
            alert('Error deleting trip: ' + data.error);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error deleting trip');
    }
}

// Initial load
initializeTripManagement();
