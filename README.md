n# 🚗 DriveEase - Car Rental Management System

DriveEase is a web-based Car Rental Management System developed using **PHP** and **MySQL**. It allows customers to browse available cars, book rentals online, manage their profiles, and view booking history. Administrators can manage vehicles, customers, bookings, payments, and reports through a dedicated dashboard.

---

# 📌 Features

## 👤 Customer Features

- User Registration & Login
- Secure Authentication
- Browse Available Cars
- Search & Filter Cars
- View Car Details
- Book a Car
- Cancel Booking
- View Booking History
- Manage Profile
- Leave Ratings & Reviews

---

## 🔧 Admin Features

- Admin Dashboard
- Manage Cars
- Manage Categories
- Manage Customers
- Manage Bookings
- Approve / Reject Bookings
- Manage Payments
- Generate Reports
- View Customer Reviews

---

# 🛠️ Technology Stack

| Technology | Purpose |
|------------|---------|
| HTML5 | Structure |
| CSS3 | Styling |
| Bootstrap 5 | Responsive UI |
| JavaScript | Client-side Functionality |
| PHP | Backend Development |
| MySQL | Database |
| XAMPP | Local Development Server |
| phpMyAdmin | Database Management |

---

# 📂 Project Structure

```
car-rental-system/
│
├── admin/
│   ├── dashboard.php
│   ├── cars.php
│   ├── bookings.php
│   ├── users.php
│   ├── reports.php
│
├── customer/
│   ├── dashboard.php
│   ├── cars.php
│   ├── booking.php
│   ├── profile.php
│
├── includes/
│   ├── db.php
│   ├── auth.php
│   ├── header.php
│   ├── footer.php
│
├── uploads/
│   └── cars/
│
├── css/
├── js/
├── images/
│
├── index.php
├── login.php
├── register.php
├── logout.php
├── database.sql
└── README.md
```

---

# ✨ Modules

### Authentication

- Register
- Login
- Logout
- Session Management

---

### Customer Module

- View Cars
- Search Cars
- Filter Cars
- Book Cars
- Booking History
- Cancel Booking
- Profile Management

---

### Admin Module

- Dashboard
- Car Management
- Category Management
- Booking Management
- User Management
- Payment Management
- Reports

---

# 🗄️ Database Tables

- users
- cars
- categories
- bookings
- payments
- reviews
- contacts

---

# 🚀 Installation

## 1. Clone the Repository

```bash
git clone https://github.com/yourusername/car-rental-system.git
```

---

## 2. Move Project

Copy the project folder into your **htdocs** directory.

Example:

```
C:\xampp\htdocs\car-rental-system
```

---

## 3. Start XAMPP

Start the following services:

- Apache
- MySQL

---

## 4. Create Database

Open phpMyAdmin

Create a database named

```
car_rental
```

---

## 5. Import Database

Import

```
database.sql
```

into the newly created database.

---

## 6. Configure Database Connection

Open

```
includes/db.php
```

Update your database credentials.

```php
$host = "localhost";
$user = "root";
$password = "";
$database = "car_rental";
```

---

## 7. Run the Project

Open your browser.

```
http://localhost/car-rental-system/
```

---

# 📸 Screens (Suggested)

- Home Page
- Login
- Register
- Car Listing
- Car Details
- Booking Page
- Customer Dashboard
- Admin Dashboard
- Reports

---

# 🔒 User Roles

## Customer

- Browse Cars
- Book Cars
- Cancel Booking
- Review Cars
- Update Profile

---

## Admin

- Manage Cars
- Manage Users
- Manage Categories
- Manage Bookings
- Manage Payments
- Generate Reports

---

# 🎯 Future Improvements

- Online Payment Gateway
- Email Verification
- Password Reset
- Google Maps Integration
- Driver Booking
- Vehicle Tracking
- AI Car Recommendations
- SMS Notifications
- QR Code Pickup
- Mobile Application

---

# 📈 Learning Outcomes

This project demonstrates:

- PHP CRUD Operations
- MySQL Database Design
- Authentication & Authorization
- Session Management
- File Uploads
- Form Validation
- Booking Management Logic
- Responsive Web Design
- Admin Dashboard Development

---

# 🤝 Contributing

Contributions are welcome

If you have ideas for improvements or find any bugs, feel free to fork the repository and submit a pull request.

---

# 📄 License

This project is created for educational and learning purposes.

You are free to use and modify it for personal, academic, and portfolio projects.

---

# 👨‍💻 Author

**Daniel Patel**

Made with ❤️ using PHP & MySQL.

---

## ⭐ If you like this project

Please consider giving it a **Star ⭐** on GitHub to support the project.33