# Tuition-Management-system-
Tutoring Management System (PHP & MySQL)

This is a **Tutoring Management System** developed as an academic project.  
The system supports **role-based access** for **Teachers** and **Students** and provides features such as course management, announcements, calendars, assignments, quizzes, and grading.

This project is currently designed for **local deployment** using **XAMPP** and **phpMyAdmin**.

---

## 🚀 Features

### 👨‍🏫 Teacher Features
- Create and manage courses
- Post announcements
- Manage course calendar (events, deadlines)
- Create assignments and quizzes
- Upload quiz PDFs
- Provide external submission links (Google Forms)
- Enter and manage grades
- View enrolled students

### 👨‍🎓 Student Features
- Sign up and log in
- Enroll in courses using course code
- View teacher posts and announcements
- View course calendar
- Access quizzes during allowed time window
- Download quiz PDFs
- Submit responses via external links
- View grades

---

## 🛠️ Tech Stack

- **Frontend:** HTML, CSS, JavaScript
- **Backend:** PHP (PDO)
- **Database:** MySQL
- **Local Server:** XAMPP
- **Database Tool:** phpMyAdmin

## 📂 Project Structure

```

projectDB/
│
├── public/
│   ├── index.php
│   ├── login.php
│   ├── signup.php
│   ├── student/
│   └── teacher/
│
├── includes/
│   ├── dbh.inc.php
│   ├── config_session.inc.php
│
├── css/
├── js/
├── uploads/
│
├── managedb (1).sql
└── README.md

```

---

## ⚙️ Setup Instructions (Local)

Follow these steps to run the project locally.


### 1️⃣ Install XAMPP

Download and install XAMPP from:  
👉 https://www.apachefriends.org/

Start:
- **Apache**
- **MySQL**


### 2️⃣ Place Project Files

1. Copy the project folder  
2. Paste it inside:

C:\xampp\htdocs\

```

Example:
```

C:\xampp\htdocs\projectDB

```

---

### 3️⃣ Create Database

1. Open browser and go to:  
```

[http://localhost/phpmyadmin](http://localhost/phpmyadmin)

```

2. Create a new database named:
```

managedb

```

---

### 4️⃣ Import Database

1. Select the `managedb` database
2. Click **Import**
3. Choose the file:
```

managedb (1).sql

```
4. Click **Go**

This will create all required tables.

---

### 5️⃣ Configure Database Connection

Edit this file:
```

includes/dbh.inc.php

````

Example configuration:

```php
<?php
$host = "localhost";
$dbname = "managedb";
$username = "root";
$password = "";

$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

$pdo = new PDO($dsn, $username, $password, $options);
````

---

### 6️⃣ Run the Project

Open your browser and go to:

```
http://localhost/projectDB/public/
```

---

## 🔐 Authentication & Roles

* Users can sign up and log in
* Each user can have a **Teacher** or **Student** role
* Sessions are used for authentication and authorization
* Unauthorized access is restricted server-side

---

## 📌 Notes

* File uploads are stored locally in the `uploads/` folder
* Quiz submissions are handled via **external Google Form links**
* The project is optimized for **academic and learning purposes**
* Not currently deployed to a production server

---

## 📄 License

This project is developed for **educational purposes**.
You are free to study, modify, and improve it.

---

## 🙌 Author

**Navid Safwan**
3rd Year CSE Student
BRAC University

---

## 📧 Support

If you face issues while setting up the project:

* Check database import
* Verify database credentials
* Ensure Apache & MySQL are running
