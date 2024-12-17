# PlanGo---Final-Web-Tech-Project
PlanGo is a trip planning web application that allows users to create, update, and manage their trips seamlessly. With a clean interface, role-based access control, and real-time data management, PlanGo is designed to simplify travel planning. Built with HTML, CSS, JavaScript, PHP, and MySQL. 




# **PlanGo - Trip Planner Web App**

**PlanGo** is a web application designed to simplify trip planning and management. It allows users to create, update, and track trips seamlessly with role-based access control for enhanced security and organization.

---

## **Features**

- **User Authentication**: Secure login and sign-up functionality.
- **Trip Management**: Create, view, edit, and delete trips.
- **Role-Based Access Control**: 
   - Admins can manage users and trips.
   - Users can manage their own trips.
- **Dynamic Dashboard**: View upcoming and past trips.
- **Responsive Design**: Optimized for both desktop and mobile devices.

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
1. Open the `db.php` file in the root directory.
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
├── assets/             # CSS, JS, and image files
│   ├── css/            # Stylesheets
│   ├── js/             # JavaScript files
│   └── images/         # Images
│
├── db.php              # Database connection file
├── index.php           # Main landing page
├── login.php           # User login page
├── signup.php          # User registration page
├── dashboard.php       # User/Admin dashboard
├── manage_trips.php    # Trip management functionality
├── admin_users.php     # Admin user management
├── logout.php          # Logout functionality
│
└── plango_db.sql       # SQL file for database setup
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

## **Contributing**

Contributions are welcome! If you'd like to contribute:

1. Fork the repository.
2. Create a new branch:
   ```bash
   git checkout -b feature-name
   ```
3. Commit your changes:
   ```bash
   git commit -m "Add new feature"
   ```
4. Push the branch:
   ```bash
   git push origin feature-name
   ```
5. Open a Pull Request.

---

## **License**

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

---

## **Contact**

For any questions or feedback, please reach out to:  
**Your Name**  
Email: your-email@example.com  
GitHub: [YourUsername](https://github.com/your-username)

--- 

This README covers all necessary aspects of the project, from installation to usage and contribution. Let me know if you need further edits! 🚀
