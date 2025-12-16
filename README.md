# Laravel Sales Management System

## 🎯 Project Overview

A comprehensive **Sales Management System** built with **Laravel 9** for managing franchise operations, quotations, orders, and customer relationships. This enterprise-level application demonstrates full-stack PHP development capabilities with complex business logic implementation.

## 👨‍💻 About This Project

This is a **real-world production system** I developed and maintained as a full-stack PHP developer. The project showcases my ability to:

- Build scalable enterprise applications with Laravel
- Design and implement complex database architectures
- Create intuitive user interfaces with responsive design
- Integrate third-party APIs (Freee Accounting, AWS S3, Slack)
- Write maintainable, well-documented code following PSR standards

## 🚀 Key Features

### Core Functionality
- **Franchise Management**: Multi-tenant system supporting franchise operations
- **Customer Relationship Management**: Lead tracking, customer segmentation, and history
- **Quotation System**: Generate detailed quotations with PDF export
- **Order Management**: Complete order lifecycle from creation to fulfillment
- **Invoice & Payment Processing**: Automated billing and payment tracking
- **Analytics & Reporting**: Business intelligence dashboard with insights
- **User & Role Management**: Granular permission system for different user types

### Technical Highlights
- **RESTful API Design**: Clean, well-structured endpoints
- **Database Optimization**: Efficient queries with proper indexing
- **File Management**: AWS S3 integration for document storage
- **Email Automation**: Automated notifications and reminders
- **Real-time Notifications**: Slack integration for team communication
- **Responsive Design**: Mobile-first approach using Bootstrap 4
- **Security**: CSRF protection, XSS prevention, input validation

## 🛠 Technology Stack

### Backend
- **PHP 8.0+** - Modern PHP with strict typing
- **Laravel 9** - Elegant MVC framework
- **MySQL** - Relational database
- **Composer** - Dependency management

### Frontend
- **Bootstrap 4** - Responsive UI framework
- **jQuery** - DOM manipulation and AJAX
- **JavaScript ES6+** - Modern JavaScript features
- **Sass** - CSS preprocessing

### DevOps & Tools
- **Docker** - Containerization for development
- **Git** - Version control
- **PHPUnit** - Unit testing
- **Laravel Dusk** - Browser testing

### Third-party Integrations
- **AWS S3** - Cloud file storage
- **Freee Accounting API** - Accounting system integration
- **Slack API** - Team notifications
- **DomPDF** - PDF generation

## 📊 Database Architecture

The system features a robust database design with **47+ tables** including:
- Users & Authentication
- Contacts & Customers
- Products & Inventory
- Quotations & Orders
- Transactions & Payments
- Analytics & Logs

## 💼 Skills Demonstrated

### PHP & Laravel Expertise
- ✅ MVC architecture implementation
- ✅ Eloquent ORM and Query Builder
- ✅ Database migrations and seeders
- ✅ Service providers and dependency injection
- ✅ Custom middleware and request validation
- ✅ Job queues and scheduled tasks
- ✅ API development and integration

### Database & Performance
- ✅ Complex SQL queries and relationships
- ✅ Database indexing and optimization
- ✅ N+1 query prevention
- ✅ Caching strategies

### Frontend Development
- ✅ Responsive design implementation
- ✅ AJAX and asynchronous operations
- ✅ Form validation and UX optimization
- ✅ Component-based architecture

### Software Engineering
- ✅ Clean code principles
- ✅ SOLID design patterns
- ✅ Test-driven development (TDD)
- ✅ Code documentation
- ✅ Git workflow and version control

## 🔧 Installation & Setup

### Option A: Docker development environment (recommended)
The repository ships with a complete Docker Compose stack so that anyone can run the project without installing PHP, MySQL, or Node locally.

**Prerequisites**
- Docker Desktop (or Docker Engine + Docker Compose)

**Steps**
1. **Clone the repository**
    ```bash
    git clone <repository-url>
    cd sales-management
    ```
2. **Create your environment file**
    ```bash
    cp .env.example .env
    ```
3. **Boot the containers**
    ```bash
    docker compose up -d --build
    ```
4. **Install PHP dependencies inside the app container**
    ```bash
    docker compose exec app composer install
    ```
5. **Install Node dependencies and build the frontend**
    ```bash
    docker compose exec app npm install
    docker compose exec app npm run dev
    ```
6. **Generate the application key and run the migrations**
    ```bash
    docker compose exec app php artisan key:generate
    docker compose exec app php artisan migrate --seed
    ```

The application is now available at `http://localhost:8080` (change the host port by exporting `APP_PORT` before running Docker Compose). MySQL is exposed on port `3306` (override with `FORWARD_DB_PORT`) and Redis on `6379` (`FORWARD_REDIS_PORT`). To stop everything run `docker compose down`; add `-v` if you also want to clear the database volume.

### Option B: Manual setup (no Docker)

**Prerequisites**
- PHP >= 8.0
- Composer
- MySQL >= 5.7
- Node.js & NPM

**Steps**
1. **Clone the repository**
    ```bash
    git clone <repository-url>
    cd sales-management
    ```
2. **Install PHP dependencies**
    ```bash
    composer install
    ```
3. **Install Node dependencies**
    ```bash
    npm install
    ```
4. **Environment configuration**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
5. **Configure database**  
    Edit `.env` file with your database credentials:
    ```
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_database
    DB_USERNAME=your_username
    DB_PASSWORD=your_password
    ```
6. **Run migrations**
    ```bash
    php artisan migrate
    ```
7. **Seed database (optional)**
    ```bash
    php artisan db:seed
    ```
8. **Build frontend assets**
    ```bash
    npm run dev
    ```
9. **Start development server**
    ```bash
    php artisan serve
    ```

Visit: `http://localhost:8000`

## 📁 Project Structure

```
├── app/
│   ├── Http/Controllers/    # Business logic controllers
│   ├── Models/              # Eloquent models
│   ├── Mail/                # Email templates
│   └── helpers.php          # Custom helper functions
├── database/
│   ├── migrations/          # Database schema
│   └── seeds/               # Sample data
├── resources/
│   ├── views/               # Blade templates
│   └── sass/                # SCSS files
├── routes/
│   ├── web.php             # Web routes
│   └── api.php             # API routes
├── tests/                   # PHPUnit & Dusk tests
└── config/                  # Configuration files
```

## 🧪 Testing

Run unit tests:
```bash
php artisan test
```

Run browser tests:
```bash
php artisan dusk
```

## 📝 Code Quality

This project follows:
- **PSR-2** coding standards
- **PSR-4** autoloading
- Laravel best practices
- Clean code principles

## 🌟 Why This Project Stands Out

1. **Production-Ready**: This is not a tutorial project - it's a real system handling actual business operations
2. **Complex Business Logic**: Implements sophisticated workflows for franchise management
3. **Scalable Architecture**: Designed to handle growing data and user base
4. **Modern PHP**: Uses PHP 8.0+ features and best practices
5. **Well-Documented**: Clean, readable code with comprehensive documentation

## 📧 Contact

**Available for:**
- Full-stack Laravel development
- PHP backend development
- API development and integration
- Database design and optimization
- Legacy code refactoring
- Technical consulting

**Upwork Profile**: [Your Upwork Profile URL]

---

## 📄 License

This is a portfolio project. All proprietary business logic and sensitive information have been removed or anonymized.

---

⭐ **Looking for a skilled Laravel developer?** Check out my other projects or reach out for collaboration!
