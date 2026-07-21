<div align="center">

# 🎓 UnsikaHub

**A student portfolio web application built with native PHP, MySQL, and PDO**

![PHP](https://img.shields.io/badge/PHP-8%2B-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-MariaDB-4479A1?style=flat-square&logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/license-MIT-green?style=flat-square)
![No Framework](https://img.shields.io/badge/framework-none-orange?style=flat-square)

### [🌐 Live Demo](http://unsikahub.site.je/)

</div>

---

## 📖 About

**UnsikaHub** is a student portfolio web application built with native PHP, MySQL, and PDO — no framework. It supports authentication (login/register/logout) with hashed passwords, two user roles (admin & user), portfolio CRUD organized by category (Website, Design, Photography, Business, etc.), cover and proof-of-work uploads, and search by keyword, category, student name, title, or description.

A solid reference project for learning native PHP with clean, secure code practices (prepared statements, upload validation, protected upload folder). Its design draws inspiration from modern portfolio platforms like Behance, Dribbble, Awwwards, and Framer — without copying their assets, branding, or specific layouts.

## 📑 Table of Contents

- [Tech Stack](#-tech-stack)
- [Features](#-features)
- [Folder Structure](#-folder-structure)
- [Getting Started](#-getting-started)
- [Default Admin Account](#-default-admin-account)
- [Security Notes](#-security-notes)
- [License](#-license)

## 🛠 Tech Stack

| Layer      | Technology                  |
|------------|------------------------------|
| Backend    | PHP (native, no framework)  |
| Database   | MySQL / MariaDB via PDO     |
| Frontend   | HTML, CSS, vanilla JavaScript |

## ✨ Features

- 🔐 Login, logout, and registration, with a required profile photo upload at registration
- 👥 Two separate roles: `admin` and `user`
- 🛡️ Admin can manage users and change user roles
- 🔒 Passwords are hashed with `password_hash()` and verified with `password_verify()`
- 🗂️ CRUD for portfolio categories: Website, Design, Photography, Business, and more
- 📁 CRUD for student portfolios, organized by category
- 🖼️ Cover image and proof-of-work uploads
- 🔍 Search by keyword, category, student name, title, or description

## 📂 Folder Structure

```text
unsikahub/
├── admin/
│   ├── types.php
│   └── users.php
├── assets/
│   ├── css/style.css
│   ├── img/
│   └── js/app.js
├── config/
│   └── database.php
├── database/
│   └── unsikahub.sql
├── includes/
│   ├── auth.php
│   ├── footer.php
│   ├── functions.php
│   └── header.php
├── uploads/
│   ├── portfolio/
│   └── profile/
├── dashboard.php
├── index.php
├── login.php
├── logout.php
├── portfolio_create.php
├── portfolio_delete.php
├── portfolio_edit.php
├── portfolio_view.php
├── portfolios.php
└── register.php
```

## 🚀 Getting Started

**Requirements:** PHP 8+, MySQL/MariaDB, and a web server (XAMPP or Laragon work great).

1. **Clone the repository** into your web server's root folder, e.g. `htdocs/unsikahub` (XAMPP):
   ```bash
   git clone https://github.com/<username>/unsikahub.git
   ```
2. **Create a database** named `unsikahub`, then import the schema from `database/unsikahub.sql`.
3. **Configure the database connection** in `config/database.php` (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`) and set `BASE_URL` to match your project folder.
4. **Set folder permissions** so `uploads/portfolio` and `uploads/profile` are writable by the web server.
5. **Open the app** in your browser, e.g. `http://localhost/unsikahub`.

## 🔑 Default Admin Account

```text
Email    : admin@unsikahub.test
Password : admin12345
```

> This account comes from the seed data in `database/unsikahub.sql`. Change the password immediately after your first login, especially before deploying to a public server.

Once logged in, the admin can go to the **Users** menu to manage roles, and the **Types** menu to manage portfolio categories.

## 🔒 Security Notes

- Passwords are never stored in plain text
- All database queries use PDO prepared statements
- Users can only edit or delete their own portfolio entries
- Admins can manage users, update roles, manage portfolio categories, and oversee all portfolios
- File uploads are restricted by extension and file size
- The `uploads/` folder includes an `.htaccess` rule that blocks PHP execution

## 📄 License

This project is licensed under the [MIT License](LICENSE).
