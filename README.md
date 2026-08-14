# 🚗 Emerald Car Rental

> A full-stack car rental management system built with **PHP, MySQL, HTML5, CSS3 and JavaScript**.

Emerald Car Rental is a web-based vehicle rental platform designed to simplify the process of browsing vehicles, checking availability, making bookings and managing rental operations.

The system includes both a **customer-facing website** and a comprehensive **administrator dashboard** for managing vehicles, brands, bookings, customers, payments and testimonials.

---

## 🌐 Project Overview

Emerald Car Rental provides a complete rental workflow for customers and administrators.

### 👤 Customers can:

* Create an account and log in
* Browse available vehicles
* View vehicle details
* Check vehicle availability
* Make vehicle bookings
* Receive booking confirmations
* View booking history
* Generate rental receipts
* Receive email notifications
* View customer testimonials

### 🛠️ Administrators can:

* Securely access the admin dashboard
* Manage vehicles
* Add, edit and delete vehicles
* Manage vehicle brands
* Manage customer bookings
* Manage customers
* Manage payments
* Manage subscribers
* Manage testimonials
* View booking details
* Monitor vehicle availability
* Generate reports

---

# 📸 Screenshots

## 🏠 Homepage

![Emerald Car Rental Homepage](Screenshots/homepage.png)

---

## 🚘 Rental Cars

![Rental Cars](Screenshots/rental-cars.png)

---

## 📅 Booking System

![Booking System](Screenshots/booking.png)

---

## 💬 Customer Testimonials

![Customer Testimonials](Screenshots/testimonials.png)

---

## 🛠️ Admin Dashboard

![Admin Dashboard](Screenshots/admin-dashboard.png)

---

# ✨ Key Features

| Feature                  | Description                                 |
| ------------------------ | ------------------------------------------- |
| 🔐 Authentication        | Customer and administrator login systems    |
| 🚗 Vehicle Management    | Add, edit, delete and manage vehicles       |
| 🏷️ Brand Management     | Manage vehicle brands                       |
| 📅 Booking System        | Customers can reserve available vehicles    |
| 🔎 Availability Checking | Check vehicles based on rental dates        |
| 👤 Customer Management   | Manage registered customers                 |
| 💳 Payment Management    | Manage rental payment information           |
| 🧾 Receipt Generation    | Generate rental receipts                    |
| 📄 PDF Generation        | Generate PDF booking receipts               |
| 📧 Email Notifications   | Send booking-related email notifications    |
| 💬 Testimonials          | Customer testimonial management             |
| 📊 Reports               | Administrative rental and booking reports   |
| 📱 Responsive Design     | Website designed for different screen sizes |

---

# 💻 Technologies Used

### Frontend

* HTML5
* CSS3
* JavaScript
* AJAX

### Backend

* PHP
* MySQL

### Libraries & Tools

* PHPMailer
* FPDF
* PHP QR Code
* XAMPP
* Apache

---

# 🗂️ Project Structure

```text
emeraldcarrental/
│
├── admin/                  # Administrator panel
├── cars/                   # Vehicle images
├── categories/             # Vehicle categories
├── css/                    # Stylesheets
├── fpdf/                   # PDF generation
├── home/                   # Homepage-related files
├── includes/               # Shared PHP files
├── js/                     # JavaScript files
├── PHPMailer/              # Email functionality
├── phpqrcode/              # QR code generation
├── users/                  # Customer functionality
├── Screenshots/            # Project screenshots
│   ├── homepage.png
│   ├── rental-cars.png
│   ├── booking.png
│   ├── testimonials.png
│   └── admin-dashboard.png
│
├── index.php               # Main entry point
├── login.php               # User login
├── signup.php              # User registration
├── contact.php             # Contact page
├── forgot-password.php     # Password recovery
├── reset.php               # Password reset
├── logout.php              # Logout
└── README.md               # Project documentation
```

---

# 🗄️ Database

The application uses **MySQL** to store and manage important rental information, including:

* Users
* Vehicles
* Vehicle brands
* Bookings
* Payments
* Testimonials
* Subscribers

The database is connected to the PHP backend using MySQL database queries.

---

# ⚙️ Installation & Setup

## Requirements

Before running the project, install:

* XAMPP
* Apache
* MySQL
* PHP
* A modern web browser

## 1. Clone the Repository

```bash
git clone https://github.com/kudzaichimidzi/emeraldcarrental.git
```

## 2. Move the Project

Place the project inside your XAMPP `htdocs` directory:

```text
C:\xampp\htdocs\emeraldcarrental
```

## 3. Start XAMPP

Open XAMPP and start:

* Apache
* MySQL

## 4. Create the Database

Open **phpMyAdmin** and create the required MySQL database.

Import the project's database SQL file.

## 5. Configure the Database

Update the database configuration file with your local MySQL credentials.

## 6. Open the Website

Open your browser and visit:


http://localhost/emeraldcarrental




# 🔄 Rental Workflow


Customer
   │
   ▼
Create Account / Login
   │
   ▼
Browse Vehicles
   │
   ▼
Check Availability
   │
   ▼
Select Vehicle
   │
   ▼
Enter Booking Details
   │
   ▼
Confirm Booking
   │
   ▼
Generate Receipt
   │
   ▼
Email Notification


# 🔐 Security

The project includes several security-related features such as:

* Password hashing
* Session-based authentication
* Separate customer and administrator access
* Database queries using prepared statements
* Form validation
* Access control for administrative pages



# 📌 Project Status

**Completed and available for demonstration.**

The system was developed as a full-stack PHP/MySQL car rental management project.


# 👨‍💻 Author

## Kudzai Chimidzi

**BCA Student | Aspiring AI & Software Developer**

Interested in:

* Artificial Intelligence
* Software Development
* Web Development
* Database Systems
* Full-Stack Development


## ⭐ Support

If you find this project interesting, consider giving the repository a ⭐ on GitHub.

**Emerald Car Rental — Making vehicle rental management simpler. 🚗💚**
