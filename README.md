# 🐾 Find My Pet
### A Lost & Found Pet Recovery Platform

![Project Status](https://img.shields.io/badge/status-active-success.svg)
![PHP](https://img.shields.io/badge/PHP-8.0%2B-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?logo=bootstrap&logoColor=white)

**Find My Pet** is a web-based application designed to bridge the communication gap between pet owners and the community. By replacing slow, traditional methods like paper posters with a centralized digital hub, we help reunite lost pets with their families faster.

---

## 📖 Table of Contents
- [Problem Statement](#-problem-statement)
- [Solution](#-solution)
- [Key Features](#-key-features)
- [Screenshots](#-screenshots)
- [Tech Stack](#-tech-stack)
- [Installation & Setup](#-installation--setup)
- [Future Roadmap](#-future-roadmap)

---

## 🚩 Problem Statement
Every year, countless pets go missing. Owners often rely on fragmented methods like social media posts or physical posters, which are slow and geographically limited. Furthermore, people who find lost pets often lack an immediate, reliable way to contact the owner or report the location.

## 💡 Solution
**Find My Pet** offers a centralized platform where:
1.  **Owners** can register their pets and manage profiles.
2.  **Finders** can instantly report a found pet (simulating a QR code scan) with auto-captured GPS location.
3.  **Community** members can view nearby lost pets and access veterinary services.

---

## ✨ Key Features

### 👤 For Pet Owners
-   **Secure Authentication:** User registration and login system.
-   **Pet Dashboard:** Manage multiple pet profiles with photos and medical details.
-   **One-Click Alerts:** Mark a pet as "Lost" to instantly alert the community.
-   **Profile Management:** Edit personal details and update profile pictures.

### 🔍 For Finders & Community
-   **Lost & Found Feed:** View a list of missing pets in the area.
-   **Quick Reporting:** Submit a "Found Pet" report including photo and automatic geolocation—no login required.
-   **Nearby Services:** Interactive map showing nearby Veterinary Clinics and Pet Shops (powered by Google Maps API).

---

## 📸 Screenshots

| Dashboard | Edit Profile |
|:---:|:---:|
| <img src="screenshots/dashboard.png" width="400" alt="Dashboard"> | <img src="screenshots/edit_profile.png" width="400" alt="Edit Profile"> |

| Nearby Services | Reporting Form |
|:---:|:---:|
| <img src="screenshots/map.png" width="400" alt="Map"> | <img src="screenshots/report_form.png" width="400" alt="Form"> |

*(Note: Replace `screenshots/filename.png` with the actual paths to your images)*

---

## 🛠 Tech Stack

**Frontend:**
* HTML5 & CSS3
* **Bootstrap 5** (Responsive Grid & Components)
* **Nunito Font** (Google Fonts)
* JavaScript (ES6+)

**Backend:**
* **PHP** (Server-side logic & Session management)
* **MySQL** (Relational Database)

**APIs & Libraries:**
* **Google Maps JavaScript API** (Maps integration)
* **Google Places API** (Nearby search for Vets/Shops)
* **Geolocation API** (Auto-capturing user location)

---

## ⚙️ Installation & Setup

To run this project locally, follow these steps:

### Prerequisites
* A local server environment (e.g., [XAMPP](https://www.apachefriends.org/), WAMP, or MAMP).
* A Google Maps API Key.

### Steps
1.  **Clone the Repository**
    ```bash
    git clone [https://github.com/yourusername/find-my-pet.git](https://github.com/yourusername/find-my-pet.git)
    ```
2.  **Move to Server Directory**
    * Move the project folder into your `htdocs` (XAMPP) or `www` (WAMP) folder.

3.  **Database Setup**
    * Open **phpMyAdmin** (`http://localhost/phpmyadmin`).
    * Create a new database named `find_my_pet`.
    * Import the `database.sql` file located in the `sql/` folder of this repo.

4.  **Configure Connection**
    * Open `includes/db.php`.
    * Update the database credentials if necessary:
        ```php
        $servername = "localhost";
        $username = "root";
        $password = ""; // Default XAMPP password is empty
        $dbname = "find_my_pet";
        ```

5.  **Add API Key**
    * Open `nearby_services.php`.
    * Replace `YOUR_GOOGLE_MAPS_API_KEY` with your actual key.

6.  **Run the App**
    * Open your browser and navigate to: `http://localhost/find-my-pet/login.php`

---

## 🚀 Future Roadmap
* 📱 **Mobile App:** Native iOS/Android application for push notifications.
* 🧠 **AI Matching:** Image recognition to auto-match "Found" photos with "Lost" profiles.
* 💬 **In-App Chat:** Real-time messaging between owners and finders.

---

## 🤝 Contributing
Contributions are welcome! Please fork this repository and submit a pull request.

---

## 📝 License
This project is licensed under the MIT License.

---

<p align="center">Made with ❤️ for pets everywhere.</p>
