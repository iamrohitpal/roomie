# Roomie - Split Expenses, Not Friendships 🏠💸

<p align="center">
  <img src="public/images/mockup.png" alt="Roomie App Mockup" width="600">
</p>

**Roomie** is a modern, group-centric expense tracking and splitting application built with **Laravel 12** and **NativePHP**. Designed for roommates who want a stress-free way to manage shared costs, Roomie simplifies everything from daily groceries to monthly utility bills with real-time notifications and offline support.

---

## 🌟 Key Features

- **👥 Group Collaboration**: Scope expenses, roommates, and settlements to specific groups for better organization.
- **🔔 Real-Time Notifications**: Integrated with **Firebase Cloud Messaging (FCM) V1** for instant alerts on new expenses.
- **📱 Native Mobile Experience**: Leverages NativePHP for a smooth, app-like feel on Android devices.
- **📶 PWA & Offline Support**: Service worker integration for reliable performance even without a connection.
- **💸 Smart Expense Splitting**: Split costs equally or by custom amounts with just a few taps.
- **⏳ Settlement Tracking**: Keep track of who owes what and mark debts as settled instantly.
- **👤 Profile & Settings**: Personalize your profile and manage notification permissions.
- **🧹 Group Maintenance**: Securely clear group data or export financial reports to CSV.

---

## 🛠️ Tech Stack

- **Framework**: [Laravel 12.x](https://laravel.com)
- **Runtime**: [NativePHP](https://nativephp.com)
- **Notifications**: [Firebase Cloud Messaging (V1)](https://firebase.google.com/docs/cloud-messaging)
- **Frontend**: [Vite](https://vitejs.dev), [Vanilla CSS](https://developer.mozilla.org/en-US/docs/Web/CSS), [Blade](https://laravel.com/docs/blade)
- **Database**: SQLite (default for NativePHP)

---

## 🚀 Quick Start

### Prerequisites

- PHP 8.2+
- Composer
- Node.js & NPM

### Installation

1. **Clone the repository**:
   ```bash
   git clone https://github.com/iamrohitpal/roomie.git
   cd roomie
   ```

2. **Install dependencies**:
   ```bash
   composer install
   npm install
   ```

3. **Configure Environment**:
   Copy `.env.example` to `.env` and configure your Firebase credentials:
   ```env
   FIREBASE_API_KEY=your_key
   FIREBASE_PROJECT_ID=your_id
   FIREBASE_MESSAGING_SENDER_ID=your_sender_id
   FIREBASE_APP_ID=your_app_id
   FIREBASE_VAPID_KEY=your_vapid_key
   ```

4. **Firebase Service Account**:
   Download your service account JSON from Firebase Console and save it as:
   `storage/app/firebase-auth.json`

5. **Run the setup**:
   ```bash
   php artisan key:generate
   php artisan migrate
   npm run build
   ```

6. **Start the development server**:
   ```bash
   composer run dev
   ```

---

## 📦 Building for Android

To generate a release build for Android:

```bash
php artisan native:run android --build=release
```

---

## 📜 License

Distributed under the MIT License. See `LICENSE` for more information.

---

**Built with ❤️ for roommates everywhere.**
