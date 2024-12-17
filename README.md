# **PlanGo - Trip Planner Web App**

PlanGo is a feature-rich trip planning web application that allows users to create, update, and manage their trips seamlessly. With a clean interface, role-based access control, and real-time data management, PlanGo is designed to simplify travel planning. Built with HTML, CSS, JavaScript, PHP, and MySQL, it offers secure user authentication and dynamic trip tracking for upcoming and past trips.

---

## **Features**

- **User Authentication**: Secure login and sign-up functionality.
- **Trip Management**: Create, view, edit, and delete trips.
- **Role-Based Access Control**: 
   - Admins can manage users and trips.
   - Users can manage their own trips.
- **Dynamic Dashboard**: View upcoming and past trips.

---

## **Technologies Used**

- **Frontend**: HTML, CSS, JavaScript
- **Backend**: PHP
- **Database**: MySQL

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
3. Import the `plango_db.sql` file from the project root directory into the database.

### **3. Configure Database Connection**
1. Open the `config.php` file in the root directory.
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
│   ├── style.css              # All styles
│
├── js/                        # JavaScript files
│   └── script.js              # Main JavaScript functionality
│
├── images/                    # Images used in the project
│
├── db/                        # Database File
│   └── plango_db.sql          # Database connection file
│
├── PHP/                       # PHP backend scripts
│
├── index.html                 # Landing page
├── login.html                 # Login page (HTML)
├── signup.html                # Signup page (HTML)
├── dashboard.html             # User dashboard (HTML)
├── create_trip.html           # Create trip page (HTML)
├── view_trips.html            # View trips page (HTML)
├── manage_trips.html          # Manage trips page (HTML)
├── manage_users.html          # Manage users page (HTML)
└── README.md                  # Project documentation

```

---

## **Usage**

1. **Sign Up**: Create a new account as a user.
2. **Log In**: Access the dashboard with your credentials.
3. **Create Trips**: Add new trips with details like destination, dates, and notes.
4. **Manage Trips**: Edit or delete trips as needed.
5. **Admin Access**: Admin users can manage all trips and users.

---

## **Screenshots**

### Login Page
![Login Page](assets/images/login.png)

### Dashboard
![Dashboard](assets/images/dashboard.png)


---

## **Contact**

For any questions or feedback, please reach out to:  
**Kwamena Duker**  
Email: kwamena.duker@ashesi.edu.gh
GitHub: [kwamenaduker](https://github.com/kwamenaduker)

--- 
