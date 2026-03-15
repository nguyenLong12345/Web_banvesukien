# Ticket Events - Online Event Booking System

## 🌟 Overview
**Ticket Events** is a modern, full-stack web application designed for browsing and booking tickets for various events such as music concerts, art exhibitions, and sports across major cities in Vietnam. 

The project was successfully refactored from a Legacy Plain PHP codebase to **Laravel 12**, implementing professional design patterns and modern integrations.

---

## 🚀 Tech Stack
- **Backend:** PHP 8.2+ / Laravel 12 (Service Layer Architecture)
- **Frontend:** Blade Templates, Vanilla CSS, JavaScript (ES6+)
- **Database:** MySQL
- **Integrations:**
    - **VNPay:** Online payment gateway integration.
    - **Firebase Auth:** Social login (Google) and secure authentication.
    - **Google Gemini AI:** Intelligent customer support chatbot.
    - **AJAX:** Real-time seat selection and dynamic search suggestions.

---

## ✨ Key Features
### 1. User Experience
- **Advanced Event Discovery:** Search and filter events by type, location, and date.
- **Real-time Seat Selection:** Interactive seat map powered by AJAX for a seamless booking experience.
- **AI Support Assistant:** Integrated Gemini AI Chatbot to handle user inquiries instantly.
- **E-Ticket Management:** Users can view their purchased tickets and booking history.

### 2. Payments & Security
- **Secure Payments:** Full integration with VNPay for domestic bank and QR code payments.
- **Multi-factor Auth:** Support for traditional email/password and Firebase Social Login.
- **Role-based Access:** Dedicated portals for Users and Administrators.

### 3. Administrator Portal (Dashboard)
- **Event Management:** Create, update, and delete events.
- **Automated Operations:** Auto-generate seat layouts for new events.
- **Order Tracking:** Real-time monitoring of ticket sales and revenue history.
- **User Management:** Control user accounts and system permissions.

---

## 🛠️ Installation & Setup

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd ticket_events/laravel-app
   ```

2. **Install dependencies:**
   ```bash
   composer install
   npm install
   ```

3. **Configure Environment:**
   ```bash
   cp .env.example .env
   # Update database, VNPay, Firebase, and Gemini AI keys in .env
   php artisan key:generate
   ```

4. **Run Database Migrations:**
   ```bash
   php artisan migrate --seed
   ```

5. **Start the application:**
   ```bash
   php artisan serve
   ```

---

## 📈 Future Improvements
- [ ] Push notifications for event reminders.
- [ ] Multi-language support (English/Vietnamese).
- [ ] Mobile app integration using Laravel Sanctum API.

---
*Developed by Long*
