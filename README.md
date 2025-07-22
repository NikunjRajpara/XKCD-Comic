# XKCD Comic Email Subscription System 📬

A PHP-based web application that allows users to register their email, verify it with a 6-digit code, and receive a random XKCD comic via email. The system includes an unsubscribe feature, and all emails are sent using Gmail SMTP. User data is stored in a simple text file — no database is used.

---

## ✨ Features

- 📧 Email registration and verification
- 🔑 6-digit email verification code system
- 📨 Sends random XKCD comics to verified users
- 🔗 Unsubscribe via email verification
- 🗂️ Email storage using `registered_emails.txt`

---


## ⚙️ How It Works

1. User submits their email via a form.
2. A 6-digit verification code is emailed via Gmail SMTP.
3. On successful verification, the email is saved to `registered_emails.txt`.
4. `send_xkcd.php` (can be run manually or with CRON) fetches a random XKCD comic and emails it to all verified users.
5. Each email includes an unsubscribe link. Clicking it triggers a confirmation process.

---

## 🔧 Tech Stack

- **PHP**
- **Gmail SMTP** (for sending HTML emails)
- **HTML** (for formatting)
- **XKCD API** (for fetching comic data)

---

## 🛠 Setup Instructions

1. Clone the repo:
   ```bash
   git clone https://github.com/NikunjRajpara/XKCD-Comic.git
   cd XKCD-Comic
2. Configure your Gmail SMTP credentials in functions.php.

3. Host the src/ folder on a PHP-supported server (e.g. XAMPP)

4. To send comics manually, run:

bash
php src/send_xkcd.php

---

📩 Made with ❤️ by Nikunj Rajpara

---

📜 License
This project is open-source and available under the MIT License.
