
# 💊 Prescription Quotation Management System

A simple web-based system built with PHP and MySQL that allows users to upload prescriptions and receive quotations from pharmacies. It supports role-based logins for users and pharmacies, prescription uploads, quotation handling, and secure session-based authentication.

---

## 🚀 Features

### 👤 User Module
- Register and login as a user
- Upload prescriptions (image or file)
- View quotation status
- Accept or reject quotations
- View uploaded prescription history

### 🏥 Pharmacy Module
- Login as a pharmacy
- View unquoted prescriptions
- Prepare and send quotations
- View submitted quotations
- Pharmacy profile management

### 🔐 Authentication
- Session-based login system
- Role-based access control (User / Pharmacy)
- Logout functionality

---

## 🛠️ Tech Stack

| Layer         | Technology          |
|---------------|---------------------|
| Backend       | PHP (Vanilla)       |
| Database      | MySQL               |
| Frontend      | HTML, CSS (inline)  |
| Server        | Apache (XAMPP/WAMP) |

---

## 🗂️ Folder Structure

```
prescription_system/
│
├── user/                  # User dashboard, profile, upload, view quotation
├── pharmacy/              # Pharmacy dashboard, profile, quotation sending
├── config/
│   └── db.php             # Database connection
├── uploads/               # Uploaded prescription files
├── index.php              # Home page or redirection
├── login.php              # Login page
├── register.php           # Registration page
└── README.md
```

---

## 📦 Database Setup

1. Import the SQL schema using phpMyAdmin or MySQL CLI:
    ```sql
    CREATE DATABASE prescription_system;
    USE prescription_system;

    -- Then import your prepared SQL file, e.g., prescription_system.sql
    ```

2. Update the database connection in `config/db.php`:

```php
<?php
$host = 'localhost';
$db   = 'prescription_system';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$pdo = new PDO($dsn, $user, $pass);
?>
```

---

## 🧪 How to Run Locally

1. Clone the repo:
   ```bash
   git clone https://github.com/arwijesuriya/prescription_system.git
   ```

2. Place it inside your XAMPP or WAMP `htdocs` directory.

3. Start Apache and MySQL from the control panel.

4. Navigate to:
   ```
   http://localhost/prescription_system/
   ```

5. Register as a user or pharmacy to begin.

---
