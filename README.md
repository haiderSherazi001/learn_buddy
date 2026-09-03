# Study Cohort Dashboard 🚀

A high-performance, real-time study cohort platform designed to help users join study groups, track daily standups, manage resources, and maintain learning streaks.

Built with a clean SaaS-like UI/UX, this application uses an **HTML-over-WebSockets architecture** to deliver a fast, SPA-like experience using Laravel Blade and Vanilla JavaScript—without requiring a heavy frontend framework.

---

## ✨ Core Features

### Real-Time Lobby & Matchmaking

Users can join a queue for automatically matched cohort rooms or create private custom rooms using invite links. Room cards update instantly for all connected users through WebSockets.

### Live Sidebar Navigation

A sleek sticky sidebar with four main sections:

* Chat
* Members
* Stash
* Activity Log

This keeps information organized without creating unnecessary vertical clutter.

### Instant HTML Synchronization

When users join or leave rooms, earn streaks, submit standups, or post resources, the server renders updated Blade components and broadcasts HTML directly to connected clients using Laravel Reverb.

### Collapsible Standup Threads

Daily standup updates support client-side collapsing functionality with **View More** and **View Less** controls, keeping discussions clean even when many updates are posted.

### Automated Streak Tracking

Cohort streaks are automatically calculated when all members complete their daily standups.

The system then:

* Updates the cohort streak automatically
* Refreshes the room header
* Broadcasts updates to connected users
* Adds milestone celebrations to the Activity Log

### Unread Notifications

Background activity automatically triggers animated notification badges on hidden sections, such as the Activity Log.

---

## 🛠️ Architecture & Tech Stack

### Backend

* Laravel 12
* PHP 8.x
* MySQL or PostgreSQL

### Frontend

* Laravel Blade
* Vanilla JavaScript
* Tailwind CSS
* Vite

### Real-Time Communication

* Laravel Reverb
* Laravel Echo
* WebSockets

### Architecture Pattern

**HTML-over-WebSockets**

Instead of sending only JSON data and rebuilding the interface on the client, the Laravel server renders updated Blade components and broadcasts HTML directly to connected users.

This provides a reactive, SPA-like experience while keeping Laravel Blade as the primary rendering layer.

---

## 💻 Local Development Setup

### 1. Clone the Repository

```bash
git clone <your-repository-url>
cd <your-project-folder>
```

### 2. Install Dependencies

Install PHP dependencies:

```bash
composer install
```

Install frontend dependencies:

```bash
npm install
```

---

## ⚙️ Configure Environment

Copy the example environment file:

```bash
cp .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

Configure your database credentials inside the `.env` file.

Make sure broadcasting is configured for Reverb:

```env
BROADCAST_CONNECTION=reverb
```

Also configure your Reverb credentials according to your Laravel environment.

---

## 🗄️ Run Database Migrations

```bash
php artisan migrate
```

---

## 🚀 Run the Application

Because this application uses real-time WebSockets, you need to run three separate processes during local development.

### Terminal 1 — Laravel Application

```bash
php artisan serve
```

### Terminal 2 — Vite Development Server

```bash
npm run dev
```

### Terminal 3 — Laravel Reverb WebSocket Server

```bash
php artisan reverb:start
```

Once all three processes are running, open the Laravel application in your browser.

---

# 🌍 Production Deployment

Deploying a WebSocket application requires additional configuration compared to a traditional Laravel application because the WebSocket server must remain running continuously.

---

## Option A — Laravel Forge

Laravel Forge provides a simpler deployment experience.

### Steps

1. Connect your repository to the server through Laravel Forge.
2. Configure your domain.
3. Enable SSL certificates.
4. Configure your production environment variables.
5. Run database migrations.
6. Create a daemon for Laravel Reverb.

The daemon command should be:

```bash
php artisan reverb:start
```

This ensures the WebSocket server continues running and can automatically restart if needed.

During deployment, make sure your deployment process also builds frontend assets:

```bash
npm run build
```

---

## Option B — Manual VPS Deployment

For a manual VPS deployment using providers such as DigitalOcean or AWS, additional server configuration is required.

### 1. Configure SSL

Production WebSocket connections should use secure WebSockets:

```text
wss://
```

Configure an SSL certificate for your domain using Let's Encrypt or another certificate provider.

---

### 2. Run Reverb with Supervisor

Laravel Reverb should run continuously in production.

Install and configure Supervisor to manage:

```bash
php artisan reverb:start
```

Supervisor can automatically restart the WebSocket process if it crashes.

---

### 3. Build Frontend Assets

Do not run:

```bash
npm run dev
```

in production.

Instead, build the production assets:

```bash
npm run build
```

---

### 4. Configure Nginx

Configure Nginx to proxy WebSocket connections to the Laravel Reverb server.

The default Reverb port is commonly:

```text
8080
```

Ensure your server configuration correctly forwards WebSocket upgrade requests to the Reverb process.

---

## 📁 Development Commands

### Start Laravel

```bash
php artisan serve
```

### Start Vite

```bash
npm run dev
```

### Start Reverb

```bash
php artisan reverb:start
```

### Run Migrations

```bash
php artisan migrate
```

### Build Production Assets

```bash
npm run build
```

---

## 🎯 Project Goal

Study Cohort Dashboard aims to create a focused collaborative learning environment where users can:

* Join study cohorts
* Participate in daily standups
* Track learning consistency
* Maintain streaks
* Share useful learning resources
* Interact with cohort members in real time
* Stay accountable through collaborative learning

The project explores how modern real-time experiences can be built using Laravel's server-side rendering capabilities without relying on large frontend frameworks.

---

## 🧠 Key Learning Areas

This project demonstrates practical experience with:

* Laravel WebSockets
* Laravel Reverb
* Laravel Echo
* Real-time event broadcasting
* Server-side rendering
* Blade component architecture
* Vanilla JavaScript DOM manipulation
* HTML-over-WebSockets architecture
* Real-time collaborative applications
* Database relationships
* Authentication and authorization
* Tailwind CSS
* Vite

---

## 📝 License

This project is open-source and available under the MIT License.

See the `LICENSE` file for more information.
