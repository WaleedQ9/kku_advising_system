# Smart Academic Advising System (SAAS)

![Laravel](https://img.shields.io/badge/Framework-Laravel_11-red.svg)
![TailwindCSS](https://img.shields.io/badge/Frontend-Tailwind_CSS-blue.svg)
![PHP](https://img.shields.io/badge/Language-PHP_8.2-777bb4.svg)
![License](https://img.shields.io/badge/License-MIT-green.svg)

An advanced, digitalized platform designed for **King Khalid University (KKU)** to streamline the academic advising process. This system consolidates student data, tracks academic risks, and documents advising sessions into a single, intuitive interface.

---

## 🌟 Key Features

- **Consolidated Student Profile:** A unified view of academic history, GPA trajectories, and credit hours to reduce cognitive load for advisors.
- **Smart Risk Indicators:** Visual alerts (Red/Amber) for students with low GPAs or high absence rates, enabling proactive intervention.
- **Dynamic Advising Notes:** A dedicated timeline for recording and categorizing sessions (Academic, Behavioral, or Attendance).
- **Dual-Language Interface:** Full support for Arabic (RTL) and English (LTR) with seamless switching.
- **Live Search & Filtering:** Instant search by student ID or name with optimized pagination.
- **Real-time Notifications:** Automated alerts for new appointments or critical academic drops.

---

## 🛠 Tech Stack

- **Backend:** Laravel 12 (PHP 8.2+)
- **Frontend:** Tailwind CSS & Blade Templating Engine
- **Database:** MySQL
- **Icons:** Font Awesome 6
- **Design Principles:** User-Centric Design (UCD) & Minimalist Dashboard UI

---

## ⚙️ Installation & Setup

Follow these steps to get the project running locally:

### 1. Clone the Repository

bash
git clone [https://github.com/WaleedQ9/kku_advising_system.git](https://github.com/WaleedQ9/kku_advising_system.git)
cd kku_advising_system

### 2. Install Dependencies

composer install
npm install && npm run dev

### 3. Environment Configuration

cp .env.example .env
php artisan key:generate

### 4. Database Setup

Run migrations and seed the database with 50 sample student records:

php artisan migrate --seed

### 5. Start the Application

php artisan serve

📂 Database Architecture
The system utilizes a relational database structure designed for scalability:

Users: Manages academic advisors and authentication.

Students: Stores core academic data, GPA, and enrollment status.

Advising Notes: Maintains a history of interactions linked to students and advisors (One-to-Many).

📊 Business Logic (Risk Engine)
The system automatically categorizes students based on the following thresholds:

🔴 Struggling: GPA < 2.0 or Absences > 15%

🟢 Regular: GPA >= 2.0 and stable attendance.

🔵 Graduated: Completed required credit hours.

📄 License
Distributed under the MIT License. See LICENSE for more information.

✉️ Contact
Waleed - [Waleed@wy.sa]

Project Link: https://github.com/WaleedQ9/kku_advising_system

---

### What's Next?

Since you've reached a major milestone by organizing your documentation and core features, we can move to the final "Professional Touch":

**The PDF Report Export:**
According to your documentation, the advisor needs to "Generate a Report." I can help you install the `dompdf` package and create a clean, university-branded PDF layout that includes:

- Student Academic Summary.
- Risk Level analysis.
- Chronological list of all advising notes.

**Would you like me to start the PDF export setup?**
