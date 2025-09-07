# Pishkhanak (پیشخوانک)

<p align="center">
  <!-- It's good to have a logo here if you have one! -->
  <!-- <img src="link_to_your_logo.png" width="400" alt="Pishkhanak Logo"> -->
</p>

<p align="center">
  <!-- Add relevant badges here, e.g., build status, version, license -->
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
  <!-- Add other badges as needed -->
</p>

## About Pishkhanak

Pishkhanak is a comprehensive web application designed to simplify a wide range of financial, administrative, and public services for users. Inspired by the "Pishkhan 24" mobile application, this platform aims to provide a centralized hub for managing various inquiries and payments.

The project leverages the power and elegance of the Laravel framework to deliver a robust and user-friendly experience.

## Key Features

Pishkhanak offers (or aims to offer) a multitude of services, including but not limited to:

*   **Financial Services:**
    *   Management of justice shares and cash subsidies.
    *   Bill payments (gas, electricity, water, phone, taxes).
    *   Mobile top-ups and internet package purchases.
*   **Administrative & Public Services:**
    *   Social security inquiries (insurance records, payroll slips).
    *   Postal package tracking.
    *   Passport tracking.
    *   SIM card registration inquiries.
*   **Content & Information:**
    *   A blog for articles and updates.
    *   Standard informational pages (About, Services, Contact).
*   **User Management:**
    *   Secure user authentication and authorization.
    *   Potential for social logins (e.g., Discord).
*   **Advanced Capabilities:**
    *   Real-time notifications and updates (via Laravel Reverb and WebSockets).
    *   Integration with AI services (OpenAI, Midjourney) for potential advanced features.
    *   YouTube API integration for video-related functionalities.
    *   Administrative dashboard (likely built with Filament).

## Tech Stack

Pishkhanak is built with a modern and powerful technology stack:

### Backend
*   **Framework:** Laravel 11
*   **PHP Version:** ^8.1 || ^8.2
*   **Key Packages:**
    *   Filament: For building beautiful admin panels.
    *   Spatie Laravel Suite: (Media Library, Tags, Permissions, Sitemap, Sluggable, Translatable) for various common application needs.
    *   Laravel Reverb: For real-time WebSocket communication.
    *   Laravel Socialite: For OAuth authentication.
    *   Laravel Telescope: For debugging and inspection.
    *   OpenAI PHP Laravel: For integrating OpenAI services.
    *   Ediasoft Midjourney API PHP: For integrating Midjourney services.
    *   Google API Client (YouTube): For YouTube integration.
    *   Intervention Image: For image manipulation.
    *   Predis: For Redis client.
*   **Database:** (Not specified, but Laravel supports MySQL, PostgreSQL, SQLite, SQL Server)

### Frontend
*   **Build Tool:** Vite
*   **CSS:** Tailwind CSS, Flowbite (Tailwind components)
*   **JavaScript:** Alpine.js, Laravel Echo, Pusher-JS
*   **UI Frameworks/Libraries:** Blade UI Kit, various icon sets.

### Python Components
*   The project includes an `index.py` file and a `bots/` directory. These Python components might be used for:
    *   Specialized backend tasks or microservices.
    *   Data scraping, processing, or automation scripts.
    *   Chatbot development and integration (e.g., Telegram bots).
    *   Machine learning model serving or interaction.
    *(Please elaborate on their specific roles if you wish to include more detail here.)*

## Getting Started

### Prerequisites
*   PHP (version as specified in `composer.json`)
*   Composer
*   Node.js and npm/yarn
*   A database server (e.g., MySQL, PostgreSQL)
*   Redis (for caching, queues, Reverb)

### Installation
1.  **Clone the repository:**
    ```bash
    git clone <your-repository-url>
    cd pishkhanak # Or your project's root directory
    ```

2.  **Install PHP dependencies:**
    ```bash
    composer install
    ```

3.  **Install frontend dependencies:**
    ```bash
    npm install
    # or
    yarn install
    ```

4.  **Set up your environment file:**
    *   Copy the example environment file:
        ```bash
        cp .env.example .env
        ```
    *   Generate your application key:
        ```bash
        php artisan key:generate
        ```
    *   Configure your database connection, `APP_NAME` (e.g., Pishkhanak), `APP_URL`, mail, queue, Reverb, and other services in the `.env` file.
        *   `APP_NAME=Pishkhanak`
        *   `REVERB_APP_ID`, `REVERB_APP_KEY`, `REVERB_APP_SECRET`
        *   Database credentials (`DB_CONNECTION`, `DB_HOST`, etc.)
        *   Credentials for external APIs (OpenAI, Google, Midjourney, Socialite providers, etc.)

5.  **Run database migrations (and seeders if available):**
    ```bash
    php artisan migrate --seed # Add --seed if you have seeders
    ```

6.  **Link storage directory:**
    ```bash
    php artisan storage:link
    ```

7.  **Build frontend assets:**
    ```bash
    npm run dev # For development with hot reloading
    # or
    npm run build # For production
    ```

8.  **Start the Reverb server (for real-time features):**
    Ensure your Reverb configuration is correct in `.env` and `config/reverb.php`.
    ```bash
    php artisan reverb:start
    ```
    You might also need to configure a queue worker:
    ```bash
    php artisan queue:work
    ```

9.  **Serve the application:**
    ```bash
    php artisan serve
    ```
    Access the application at the URL provided (usually `http://localhost:8000`).

## Contributing

Thank you for considering contributing to Pishkhanak! Please follow standard Laravel community guidelines and consider creating an issue to discuss significant changes before development.

(You can add more specific contribution guidelines here if you have them.)

## Security Vulnerabilities

If you discover a security vulnerability within Pishkhanak, please send an e-mail to [your-email@example.com](mailto:your-email@example.com). All security vulnerabilities will be promptly addressed. (Replace with your actual security contact email, or refer to Laravel's security policy if appropriate).

## License

The Pishkhanak project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
