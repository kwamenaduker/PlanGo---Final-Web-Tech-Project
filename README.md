<img width="1512" alt="Screenshot 2024-12-20 at 2 36 30 AM" src="https://github.com/user-attachments/assets/fe219b22-14eb-47f7-b919-254afa461bca" /># **PlanGo - Trip Planner Web App**

PlanGo is a comprehensive trip planning web application designed to simplify travel organization for users. It includes features like budget management, itinerary planning, role-based access control, and analytics dashboards for both users and admins. Built with HTML, CSS, JavaScript, PHP, and MySQL, PlanGo ensures secure user authentication and dynamic trip and budget tracking.

---

## **Features**

### **For Users:**

- **User Authentication:** Secure login and sign-up functionality.
- **Trip Management:** Create, view, edit, and delete trips with detailed information.
- **Budget Management:** Track expenses by category and visualize spending with pie charts.
- **Itinerary Planner:** Schedule day-by-day activities with detailed time slots and notes.
- **Analytics Dashboard:** Access statistics like total trips, total budget, and budget breakdowns.

### **For Admins:**

- **Manage Users:** View, create, edit, and delete user accounts.
- **Manage Trips:** Access and manage all user trips.
- **Analytics Dashboard:** View metrics like total users, popular destinations, and average trip budgets.

---

## **Technologies Used**

- **Frontend:** HTML, CSS, JavaScript (Chart.js for analytics visualization)
- **Backend:** PHP
- **Database:** MySQL

---

## **Installation Instructions**

Follow these steps to set up PlanGo on your local machine:

### **1. Clone the Repository**

```bash
git clone https://github.com/your-username/PlanGo.git
cd PlanGo
```

### **2. Set Up the Database**

1. Open your MySQL client (e.g., phpMyAdmin).
2. Create a new database named `plango_db`.
3. Import the `plango_db.sql` file from the `db` directory into the database.

### **3. Configure Database Connection**

1. Open the `config.php` file in the `php` directory.
2. Update the following with your database credentials:
   ```php
   $host = "localhost";
   $user = "root";
   $password = "";
   $database = "plango_db";
   ```

### **4. Run the Application**

1. Place the project folder in your web server's root directory (e.g., `htdocs` for XAMPP).
2. Start your local server (XAMPP, WAMP, or similar).
3. Open your browser and go to:
   ```
   http://localhost/PlanGo
   ```

---

## **Folder Structure**

```
PlanGo/
│
├── css/                       # Stylesheets
│   ├── style.css              # Main styles
│
├── js/                        # JavaScript files
│   ├── admin.js               # Admin functionality
│   ├── script.js              # Main functionality
│   ├── script_itinerary.js    # Itinerary functionality
│   ├── script_budget.js       # Budget management functionality
│   ├── analytics.js           # Analytics functionality
│
├── images/                    # Images used in the project
│
├── db/                        # Database File
│   └── PlanGo.sql          # Database structure and sample data
│
├── php/                       # PHP backend scripts
│   ├── config.php             # Database configuration
│   ├── fetch_analytics.php    # Fetch analytics data
│   ├── fetch_users.php        # Fetch users data (Admin)
│   ├── update_user.php        # Update user functionality
│   ├── delete_user.php        # Delete user functionality
│   ├── fetch_all_trips.php    # Fetch trips data (Admin)
│   ├── update_trip_admin.php  # Update trips functionality (Admin)
│   ├── delete_trip_admin.php  # Delete trips functionality (Admin)
|   ............
│
├── index.html                 # Landing page
├── login.html                 # Login page
├── register.html              # Signup page
├── dashboard.html             # User dashboard
├── create_trip.html           # Create trip page
├── trip_details.html          # Trip details page
├── itinerary.html             # Itinerary planning page
├── budget_management.html     # Budget management page
├── admin_dashboard.html       # Admin dashboard
├── admin_users.html           # Manage users page (Admin)
├── admin_trips.html           # Manage trips page (Admin)
├── user_analytics.html        # User analytics dashboard
├── error.html                 # Error page for access denied
└── README.md                  # Project documentation
```

---

## **Usage**

### **For Users:**

1. **Sign Up:** Create a new account.
2. **Log In:** Access the dashboard with your credentials.
3. **Create Trips:** Add new trips with details like destination, dates, and notes.
4. **Manage Budgets:** Track expenses by category and view charts for analysis.
5. **Plan Itineraries:** Schedule activities for your trips.
6. **View Analytics:** Monitor trip and budget statistics.

### **For Admins:**

1. **Manage Users:** View, create, update, or delete user accounts.
2. **Manage Trips:** Edit or delete trips.
3. **View Analytics:** Access metrics on users and trip data.

---

## **Screenshots**

### Landing Page
<img width="1512" alt="Screenshot 2024-12-20 at 2 36 30 AM" src="https://github.com/user-attachments/assets/6685dacf-9f83-4b2c-9686-862238e92ff5" />


### User Dashboard
<img width="1512" alt="Screenshot 2024-12-20 at 3 36 58 AM" src="https://github.com/user-attachments/assets/0c6c2d6c-08d7-4a37-a6b6-50cc364b2d71" />




### Budget Management
<img width="1512" alt="Screenshot 2024-12-20 at 3 24 00 AM" src="https://github.com/user-attachments/assets/d7417f50-101f-4e11-a171-6215b6ebf1b2" />



### Itinerary Planner
<img width="1512" alt="Screenshot 2024-12-20 at 3 34 01 AM" src="https://github.com/user-attachments/assets/51e9acc2-d820-4a0b-8199-f5dfca7edcc5" />




### Admin Dashboard
<img width="1512" alt="Screenshot 2024-12-20 at 3 36 32 AM" src="https://github.com/user-attachments/assets/604f88d1-bd4f-408a-b219-207cf9ad50f0" />




---

## **Contact**

For any questions or feedback, please reach out to:\
**Kwamena Duker**\
Email: [kwamena.duker@ashesi.edu.gh](mailto\:kwamena.duker@ashesi.edu.gh)\
GitHub: [kwamenaduker](https://github.com/kwamenaduker)

---

