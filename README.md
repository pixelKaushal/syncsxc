# SyncSXC 🚀
**The Unified Administrative Portal for SXC Clubs**

SyncSXC is a secure, web-based platform designed for St. Xavier's College to streamline club management, student registrations, and inter-club activity synchronization.

---

## ✨ Key Features
* **Institutional Security:** Domain-restricted registration (only `@sxc.edu.np` emails allowed).
* **Secure Authentication:** Password hashing using `bcrypt` (PASSWORD_DEFAULT).
* **Club-Specific Access:** Users are categorized by their specific clubs (Physics, Computer, Magis, etc.).
* **Modern UI:** Responsive design using the "Outfit" typography and institutional color palette.

## 🛠️ Tech Stack
* **Frontend:** HTML5, CSS3 (Flexbox/Grid), FontAwesome, Google Fonts.
* **Backend:** PHP (Procedural & Prepared Statements).
* **Database:** MySQL.
* **Authentication:** Session-based PHP authentication.

---

## 🚀 Getting Started

### 1. Prerequisites
* A local server environment (XAMPP, WAMP, or Laragon).
* PHP 8.0 or higher.
* MySQL Database.

### 2. Installation
1.  **Clone the repository:**
    ```bash
    git clone [https://github.com/pixelKaushal/syncsxc.git](https://github.com/pixelKaushal/syncsxc.git)
    ```
2.  **Database Setup:**
    * Create a database named `syncsxc`.
    * Import the `database.sql` (if available) or create a `users` table with columns: `id`, `primary_email`, `recovery_email`, `password`, `role`, and `club_id`.
3.  **Configuration:**
    * Open `includes/data.php` (or your connection file).
    * Update your database credentials:
    ```php
    $conn = new mysqli("localhost", "root", "your_password", "syncsxc");
    ```

---

## 📂 Project Structure
* `/admin` - Backend logic for registration and logins.
* `/assets` - CSS stylesheets, brand images, and Javascript.
* `/public` - Publicly accessible files like Terms and Conditions.
* `index.php` - The main landing page/dashboard entry point.

## 🔐 Security Roadmap
- [x] Prepared Statements for SQL Injection prevention.
- [x] Password Hashing.
- [ ] Implement CSRF Tokens for forms.
- [ ] Add `.htaccess` to prevent directory listing.
- [ ] Enable SSL/HTTPS.

---

## 🤝 Contributing
1. Fork the Project.
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`).
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`).
4. Push to the Branch (`git pull origin feature/AmazingFeature`).
5. Open a Pull Request.

---
**Developed for St. Xavier's College students.**
