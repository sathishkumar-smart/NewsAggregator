
# 🗞️ News Aggregator API

A Laravel-powered RESTful API that aggregates news articles from multiple sources (News API, The Guardian, NY Times).  
Users can register, log in, and set preferences to receive personalized feeds.

---

## 🚀 Features

- 🧾 User registration & authentication (Laravel Sanctum)
- 📄 Swagger/OpenAPI documentation (auto-generated)
- 🔄 Aggregates articles from external APIs (NewsAPI, Guardian, NYT)
- ❤️ User preferences (sources, authors, categories)
- 💡 Personalized news feed based on preferences
- 📦 Dockerized environment
- 🧪 PHPUnit feature tests

---

## 🛠️ Tech Stack

- PHP 8.x  
- Laravel 10.x  
- MySQL 8.0  
- Sanctum (API token auth)  
- Docker + Docker Compose  
- Swagger / OpenAPI (L5-Swagger)  
- PHPUnit (Testing)  

---

## 🐋 Docker Setup

### 1. Clone & Build

```bash
git clone https://github.com/your-username/news-aggregator-api.git
cd news-aggregator-api
cp .env.example .env
```

### 2. Docker Compose

```bash
docker-compose up -d --build
```

### 3. Run Laravel Setup

```bash
docker exec -it news-api-app composer install
docker exec -it news-api-app php artisan key:generate
docker exec -it news-api-app php artisan migrate
docker exec -it news-api-app php artisan db:seed
```

---

## ⚙️ Environment Variables

Ensure `.env` [For Docker `.env.docker` ] has:

```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=news_aggregator
DB_USERNAME=user
DB_PASSWORD=root
```


For Docker, you can also use a separate `.env.docker` and link it in `docker-compose.yml`.

# 🔑 API Keys & External Services Configuration
To integrate with third-party news sources, set the following environment variables in your .env or .env.docker file:

# NewsAPI
NEWS_API_KEY=your_registered_key
NEWS_API_BASE_URL=https://newsapi.org/v2

# New York Times
NEWYORK_TIMES_API_KEY=your_registered_key
NEWYORK_TIMES_BASE_URL=https://api.nytimes.com/svc/search/v2/articlesearch.json

# The Guardian
GUARDIAN_API_KEY=your_registered_key
GUARDIAN_API_BASE_URL=https://content.guardianapis.com

---
## 📰 News Aggregation Command

> ⚡ **This section is very important**: The application includes a custom Laravel Artisan command that fetches and aggregates news articles from external APIs.  
> 🕒 **Currently set to run hourly via scheduler** – but it's configurable based on your needs.

---

### 🔧 Usage

```bash
# This command triggers the full news aggregation from all sources
php artisan articles:fetch
```

## 📚 API Documentation

Generate Swagger docs:

```bash
docker exec -it news-api-app php artisan l5-swagger:generate
```

View at: [http://localhost:8000/api/documentation](http://localhost:8000/api/documentation)

---

## 🧪 Running Tests

```bash
php artisan test
# or
./vendor/bin/phpunit

#Run a specific test

php artisan test --filter=test_user_can_register_and_login
```
But inside Docker, this needs to be run like:

```bash
docker exec -it news-api-app php artisan test

#To run a specific test:
docker exec -it news-api-app php artisan test --filter=test_user_can_register_and_login
```

SQLite is used for fast isolated testing via in-memory database.

---

## 🧠 Implementation Highlights

- **Docker**: Full environment with Nginx, PHP-FPM, MySQL.
- **Caching**: (If implemented) Laravel cache for article responses.
- **Swagger**: Auto-generated docs with `@OA` annotations in controllers.
- **Testing**: SQLite-based PHPUnit feature tests.
- **Architecture**: Controllers → Services → Repositories → External APIs

---

## 🧰 PhpMyAdmin

If needed, you can run PhpMyAdmin with:

```yaml
phpmyadmin:
  image: phpmyadmin/phpmyadmin
  container_name: phpmyadmin
  ports:
    - "8081:80"
  environment:
    PMA_HOST: db
    MYSQL_ROOT_PASSWORD: root
  depends_on:
    - db
```

Access: [http://localhost:8081](http://localhost:8081)

Base API URL: [http://localhost:8000/api]
---

## 📁 Project Structure

- `app/Http/Controllers`: API Controllers
- `app/Services`: Business logic layer
- `app/Repositories`: API integrations & data access
- `routes/api.php`: Route definitions
- `app/Http/Requests`: Form Requests for validation
- `app/Models`: Eloquent Models
- `app/Swagger`: OpenAPI annotations

---

## 🔐 Environment Configuration

Some settings are configured via environment variables. Make sure to set the following in your `.env` or `.env.docker` file as needed:

### 📧 Mail Configuration
To enable password reset and other email-based functionality, set the following in your `.env` file:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=no-reply@example.com
MAIL_FROM_NAME="News Aggregator"
```

## 🚀 Features & Technical Implementation

This project is a News Aggregator API built using **Laravel 10**, designed to aggregate and serve news articles from multiple sources. Below is a summary of key features and technical decisions:

### 📰 Features

- **User Authentication**
  - Registration, login, logout via Laravel Sanctum
  - Password reset via email

- **Article Aggregation**
  - Articles are fetched from multiple third-party news APIs (e.g., NewsAPI, NYTimes, The Guardian)
  - Supports keyword, category, source, and date-based filtering
  - Articles are stored locally and cached for improved performance

- **User Preferences**
  - Users can save their preferred sources, authors, and categories
  - Personalized feed endpoint returns articles matching preferences

- **Swagger Documentation**
  - Interactive API docs generated with `l5-swagger`
  - Visit: [`/api/documentation`](http://localhost:8000/api/documentation)

- **Rate Limiting**
  - Per-user rate limits using Laravel’s `throttle` middleware
  - Custom throttling group for heavy endpoints

- **Testing**
  - PHPUnit tests for critical features like registration and preference saving
  - Uses in-memory SQLite for fast test execution

### 🧠 Technical Highlights

- **Caching**: API responses are cached to reduce redundant external API calls.
- **Form Requests**: Centralized validation using Laravel's custom request classes.
- **Service Layer**: Business logic is abstracted into services for better separation of concerns.
- **Repository Pattern**: Abstracts data source logic; allows easier unit testing and API switching.
- **Dockerized**: Full environment runs in Docker using `docker-compose`.
- **PHP 8+**: Modern syntax and features like attributes and typed properties used throughout.

---

📌 Note: SRP isn’t about one method per class — it’s about one reason to change. All the services,controllers have one reason: their specific domain responsibility.

## 👤 Author
🔗 GitHub Profile: https://github.com/sathishkumar-smart
📧 lingampellisathishkumar@gmail.com
Made with ❤️ by Sathish-Kumar
🕓 July 2025

---

## 🪪 License

MIT License. See `LICENSE` for more details.

---

Happy Coding! 🚀
