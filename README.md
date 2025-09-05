# Library Management System

A comprehensive library management system built with Laravel, Inertia.js, Vue.js, and TypeScript. This modern system provides a complete solution for managing books, users, borrowing, reservations, and library operations with advanced search capabilities, role-based access control, and real-time notifications.

[![Laravel Version](https://img.shields.io/badge/Laravel-11.x-orange.svg)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-purple.svg)](https://php.net)
[![Vue.js](https://img.shields.io/badge/Vue.js-3.x-green.svg)](https://vuejs.org)
[![TypeScript](https://img.shields.io/badge/TypeScript-5.x-blue.svg)](https://www.typescriptlang.org)
[![Inertia.js](https://img.shields.io/badge/Inertia.js-1.x-purple.svg)](https://inertiajs.com)
[![Tests](https://img.shields.io/badge/Tests-Passing-brightgreen.svg)](https://github.com/AdilAzhari/library-api-12)

## 🚀 Features

### Core Functionality
- **📚 Book Management**: Complete CRUD operations with metadata, cover images, and categorization
- **👥 User Management**: Role-based access control (Admin, Librarian, Member, User)
- **📖 Borrowing System**: Full borrowing workflow with due dates, renewals, and automated notifications
- **🔖 Reservation System**: Book reservation with automatic fulfillment and queue management
- **💰 Fine Management**: Automated fine calculation, payment tracking, and grace periods
- **⭐ Review System**: Book reviews, ratings, and recommendation engine
- **📝 Reading Lists**: Personal collections and curated book lists

### Advanced Features
- **🔍 Intelligent Search**: Powered by Typesense for fast, typo-tolerant search
- **🌐 RESTful API**: Complete API with Sanctum authentication and rate limiting
- **📊 Analytics & Reports**: Comprehensive reporting and usage analytics
- **🔔 Notifications**: Real-time alerts for due dates, reservations, and overdue items
- **📤 Export Features**: Generate reports in PDF and Excel formats
- **🌍 Multi-language Ready**: Internationalization support with translation management
- **📱 Responsive Design**: Mobile-first design optimized for all devices

### Technical Excellence
- **🏗️ Clean Architecture**: SOLID principles with DTOs, Services, Actions, and Events
- **⚡ High Performance**: Optimized queries, caching strategies, and lazy loading
- **🧪 Comprehensive Testing**: 48 passing tests with 162 assertions covering all functionality
- **🔐 Security First**: Input validation, CSRF protection, and secure authentication
- **📈 Scalable Design**: Queue-based processing and event-driven architecture

---

## 📋 Table of Contents

1. [🛠 Requirements](#-requirements)
2. [📦 Installation](#-installation)
3. [⚙️ Configuration](#-configuration)
4. [🎯 Usage](#-usage)
5. [🏗️ Architecture](#-architecture)
6. [📚 API Documentation](#-api-documentation)
7. [🖥️ Frontend Features](#-frontend-features)
8. [🧪 Testing](#-testing)
9. [🚀 Development](#-development)
10. [🌐 Deployment](#-deployment)
11. [🤝 Contributing](#-contributing)
12. [📄 License](#-license)

---

## 🛠 Requirements

### System Requirements
- **PHP**: 8.2 or higher
- **Composer**: 2.0 or higher
- **Node.js**: 18.0 or higher
- **NPM/Yarn**: Latest stable version
- **Database**: MySQL 8.0+ or PostgreSQL 13+
- **Redis**: 6.0+ (for caching and queues)
- **Typesense**: 0.25.0+ (for search functionality)

### PHP Extensions Required
- OpenSSL
- PDO
- Mbstring
- Tokenizer
- XML
- Ctype
- JSON
- BCMath
- Fileinfo
- Redis (for caching)

---

## 📦 Installation

### 1. Clone Repository
```bash
git clone https://github.com/AdilAzhari/library-api-12.git
cd library-api-12
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Database Setup
```bash
# Configure database in .env file
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=library_management
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Run migrations and seeders
php artisan migrate
php artisan db:seed
```

### 5. Search Engine Setup (Optional)
```bash
# Install and configure Typesense
# Add to .env:
SCOUT_DRIVER=typesense
TYPESENSE_API_KEY=your-api-key
TYPESENSE_HOST=localhost
TYPESENSE_PORT=8108
TYPESENSE_PROTOCOL=http
```

### 6. Build Frontend Assets
```bash
# Development build
npm run dev

# Production build
npm run build
```

### 7. Start the Application
```bash
# Start Laravel server
php artisan serve

# Start Vite dev server (separate terminal)
npm run dev
```

Visit `http://localhost:8000` to access the application.

### 8. Default Admin Account
```
Email: admin@library.com
Password: password
```

---

## ⚙️ Configuration

### Environment Variables

Key configuration options in `.env`:

```env
# Application Settings
APP_NAME="Library Management System"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=library_management
DB_USERNAME=root
DB_PASSWORD=

# Queue Configuration
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=localhost
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null

# Search Configuration (Typesense)
SCOUT_DRIVER=typesense
TYPESENSE_API_KEY=your-api-key
TYPESENSE_HOST=localhost
TYPESENSE_PORT=8108
TYPESENSE_PROTOCOL=http

# Library-Specific Settings
LIBRARY_NAME="Central Library"
LIBRARY_DEFAULT_BORROW_DAYS=14
LIBRARY_MAX_BORROWS_PER_USER=5
LIBRARY_MAX_RENEWALS=2
LIBRARY_DAILY_LATE_FEE=0.50
LIBRARY_GRACE_PERIOD_DAYS=1
```

### Library Configuration

Edit `config/library.php` for detailed library settings:

```php
return [
    'settings' => [
        'max_loans_per_user' => env('LIBRARY_MAX_LOANS', 5),
        'loan_period_days' => env('LIBRARY_LOAN_PERIOD', 14),
        'max_renewals' => env('LIBRARY_MAX_RENEWALS', 2),
        'grace_period_days' => env('LIBRARY_GRACE_PERIOD', 1),
        'daily_late_fee' => env('LIBRARY_DAILY_LATE_FEE', 0.50),
        'max_reservation_days' => env('LIBRARY_MAX_RESERVATION_DAYS', 7),
    ],
    'features' => [
        'enable_reservations' => true,
        'enable_fines' => true,
        'enable_reviews' => true,
        'enable_reading_lists' => true,
        'enable_notifications' => true,
    ],
];
```

---

## 🎯 Usage

### User Roles & Permissions

#### 🔴 Admin
- Complete system access and configuration
- User management and role assignment
- System settings and library policies
- Comprehensive analytics and reporting
- Database maintenance and backups

#### 🟡 Librarian
- Book catalog management
- Borrowing and return processing
- Fine management and waivers
- User assistance and support
- Inventory management

#### 🟢 Member/User
- Browse and search book catalog
- Place and manage reservations
- Borrow and return books
- Write reviews and ratings
- Create personal reading lists
- View borrowing history and fines

### Key Workflows

#### 📚 Book Management
1. **Add Books**: Librarians can add books with complete metadata
2. **Search & Filter**: Advanced search with multiple criteria
3. **Availability Check**: Real-time availability status
4. **Recommendations**: AI-powered book suggestions

#### 📖 Borrowing Process
1. **Browse Catalog**: Users search and filter books
2. **Check Availability**: View current status
3. **Make Reservation**: Reserve unavailable books
4. **Borrow Book**: Check out available books
5. **Renewal**: Extend borrowing period
6. **Return Process**: Complete borrowing cycle

#### 🔖 Reservation System
1. **Place Reservation**: Queue for popular books
2. **Notification**: Alert when book becomes available
3. **Auto-fulfillment**: Automatic borrowing from reservation
4. **Queue Management**: Fair distribution system

---

## 🏗️ Architecture

### System Overview

```
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│   Frontend      │    │   Backend API    │    │   Database      │
│   (Vue.js +     │◄───│   (Laravel +     │◄───│   (MySQL +      │
│   Inertia.js)   │    │   Sanctum)       │    │   Redis)        │
└─────────────────┘    └──────────────────┘    └─────────────────┘
         │                       │                       │
         │              ┌────────┼────────┐             │
         │              │                 │             │
         │              ▼                 ▼             │
         │    ┌─────────────────┐ ┌─────────────────┐   │
         │    │   Search Engine │ │   Queue System  │   │
         └───►│   (Typesense)   │ │   (Redis)       │◄──┘
              └─────────────────┘ └─────────────────┘
```

### Directory Structure

```
library-api-12/
├── app/
│   ├── Actions/          # Business action classes
│   ├── DTO/              # Data Transfer Objects
│   ├── Enum/             # Enumeration classes
│   ├── Events/           # Event classes
│   ├── Http/
│   │   ├── Controllers/  # HTTP controllers
│   │   ├── Middleware/   # Custom middleware
│   │   └── Requests/     # Form request validation
│   ├── Models/           # Eloquent models
│   ├── Services/         # Business logic services
│   └── Traits/           # Reusable traits
├── database/
│   ├── migrations/       # Database schema
│   ├── factories/        # Model factories for testing
│   └── seeders/          # Database seeders
├── resources/
│   ├── js/
│   │   ├── Components/   # Vue components
│   │   ├── Layouts/      # Layout components
│   │   ├── Pages/        # Page components
│   │   └── Types/        # TypeScript definitions
│   └── views/            # Blade templates
├── routes/               # Route definitions
├── tests/                # Test files
└── docs/                 # Documentation
```

### Design Patterns Used

- **Repository Pattern**: Data access abstraction
- **Service Layer**: Business logic separation
- **DTO Pattern**: Data transfer standardization
- **Observer Pattern**: Model event handling
- **Factory Pattern**: Object creation
- **Strategy Pattern**: Flexible algorithms

---

## 📚 API Documentation

### Authentication

All API endpoints require authentication using Laravel Sanctum tokens:

```bash
# Login and get token
POST /api/auth/login
{
  "email": "user@example.com",
  "password": "password"
}

# Use token in subsequent requests
Authorization: Bearer your-token-here
```

### Core Endpoints

#### Books API
```bash
# List all books with filtering and pagination
GET /api/public/v1/books
Query Parameters:
- search: Text search in title, author, description
- genre: Filter by genre ID
- status: available, borrowed, reserved
- author: Filter by author name
- year_from: Publication year from
- year_to: Publication year to
- sort: title_asc, title_desc, year_asc, year_desc
- per_page: Results per page (default: 15)

# Get book details
GET /api/public/v1/books/{id}

# Create new book (Admin/Librarian only)
POST /api/public/v1/books
{
  "title": "Book Title",
  "author": "Author Name",
  "isbn": "978-1234567890",
  "publication_year": 2024,
  "genre_id": 1,
  "description": "Book description",
  "cover_image": "file"
}

# Update book (Admin/Librarian only)
PUT /api/public/v1/books/{id}

# Delete book (Admin/Librarian only)
DELETE /api/public/v1/books/{id}

# Get book recommendations
GET /api/books/recommendations
```

#### Borrowing API
```bash
# Borrow a book
POST /api/public/v1/borrow
{
  "book_id": 1,
  "user_id": 1
}

# Return a book
POST /api/public/v1/return
{
  "book_id": 1,
  "user_id": 1
}

# Get active borrows
GET /api/borrows/active

# Get borrowing history
GET /api/borrows/history

# Renew a book
POST /api/borrows/{borrow_id}/renew
```

#### Reservations API
```bash
# Make a reservation
POST /api/reservations
{
  "book_id": 1,
  "user_id": 1,
  "expires_at": "2024-12-31"
}

# List user reservations
GET /api/reservations

# Cancel reservation
DELETE /api/reservations/{id}
```

#### Search API
```bash
# Advanced book search
GET /api/search/books
Query Parameters:
- q: Search query
- filters[genre]: Genre filter
- filters[status]: Status filter
- filters[year_range]: Year range filter
- sort: Sorting option
```

### Response Format

All API responses follow this format:

```json
{
  "success": true,
  "message": "Operation successful",
  "data": {
    // Response data
  },
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 100,
    "last_page": 7
  }
}
```

### Error Responses

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "field_name": ["Error message"]
  }
}
```

---

## 🖥️ Frontend Features

### User Interface

#### Dashboard
- **User Dashboard**: Personal library overview with active borrows, reservations, and recommendations
- **Admin Dashboard**: System analytics, recent activities, and management shortcuts
- **Librarian Dashboard**: Daily operations overview with pending tasks and statistics

#### Book Catalog
- **Grid/List View**: Toggle between visual and detailed list views
- **Advanced Search**: Multi-criteria search with filters and sorting
- **Book Details**: Comprehensive book information with availability status
- **Related Books**: AI-powered recommendations based on genre and popularity

#### Borrowing Interface
- **One-Click Borrowing**: Streamlined checkout process
- **Renewal System**: Easy renewal with eligibility checking
- **Return Process**: Quick return with fine calculation
- **History Tracking**: Complete borrowing history with export options

#### Reservation Management
- **Queue Position**: Show position in reservation queue
- **Notification System**: Alerts for available books
- **Auto-Cancellation**: Automatic cleanup of expired reservations
- **Reservation History**: Track all past reservations

### Administrative Features

#### User Management
- **Role Assignment**: Flexible role-based permissions
- **User Statistics**: Borrowing patterns and engagement metrics
- **Account Status**: Active, suspended, or restricted account management
- **Communication Tools**: Send notifications and messages to users

#### Reporting System
- **Usage Analytics**: Library usage statistics and trends
- **Financial Reports**: Fine collection and revenue tracking
- **Inventory Reports**: Book availability and popularity metrics
- **Export Options**: PDF and Excel export capabilities

#### System Configuration
- **Library Settings**: Configure borrowing policies and limits
- **Fine Structure**: Set up fine calculations and grace periods
- **Notification Templates**: Customize email and system notifications
- **Feature Toggles**: Enable/disable system features

---

## 🧪 Testing

The application includes comprehensive test coverage with 48 passing tests covering 162 assertions.

### Test Categories

#### Unit Tests
- **Models**: Test model relationships, scopes, and methods
- **Services**: Test business logic and calculations
- **DTOs**: Test data transformation and validation
- **Actions**: Test individual business actions

#### Feature Tests
- **API Endpoints**: Test all API endpoints with various scenarios
- **Authentication**: Test login, registration, and token management
- **Borrowing Workflows**: Test complete borrowing processes
- **Reservation System**: Test reservation creation and fulfillment

#### Browser Tests
- **User Flows**: End-to-end testing of user interactions
- **Admin Workflows**: Test administrative functions
- **UI Components**: Test frontend component behavior
- **Cross-browser Compatibility**: Ensure consistent behavior across browsers

### Running Tests

```bash
# Run all tests
php artisan test

# Run with coverage report
php artisan test --coverage

# Run specific test suite
php artisan test tests/Feature/BookControllerTest.php

# Run tests in parallel
php artisan test --parallel

# Run browser tests
php artisan dusk

# Generate test coverage HTML report
php artisan test --coverage-html coverage-report
```

### Test Database

Tests use a separate SQLite database for speed and isolation:

```bash
# Configure test database in phpunit.xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

---

## 🚀 Development

### Development Environment Setup

```bash
# Start development servers
php artisan serve &
npm run dev &

# Start queue worker
php artisan queue:work

# Start scheduler (for production)
php artisan schedule:work
```

### Code Quality Tools

#### PHP Code Standards
```bash
# Format code with Laravel Pint
./vendor/bin/pint

# Analyze code with PHPStan
./vendor/bin/phpstan analyse

# Run Rector for automated refactoring
./vendor/bin/rector process --dry-run
```

#### Frontend Code Standards
```bash
# Format JavaScript/TypeScript
npm run lint:fix

# Type checking
npm run type-check

# Build for production
npm run build
```

### Development Workflow

1. **Feature Development**
   - Create feature branch from main
   - Follow conventional commit messages
   - Write tests for new functionality
   - Ensure code quality standards

2. **Code Review Process**
   - Submit pull request with detailed description
   - Automated tests must pass
   - Code review by team members
   - Address feedback and re-test

3. **Deployment Pipeline**
   - Merge to main branch
   - Automated deployment to staging
   - Manual testing and approval
   - Production deployment

### Debugging Tools

#### Laravel Telescope
Monitor application performance and debug issues:
```bash
php artisan telescope:install
php artisan migrate
```
Access at `/telescope`

#### Laravel Debugbar
Development debugging toolbar:
```bash
composer require barryvdh/laravel-debugbar --dev
```

#### Vue DevTools
Browser extension for Vue.js debugging:
- Install Vue DevTools extension
- Debug components, state, and events

---

## 🌐 Deployment

### Production Environment

#### Server Requirements
- **Web Server**: Nginx or Apache with PHP-FPM
- **PHP**: 8.2+ with required extensions
- **Database**: MySQL 8.0+ or PostgreSQL 13+
- **Cache**: Redis 6.0+
- **Search**: Typesense server
- **Queue**: Redis or database-backed queues

#### Environment Configuration

```bash
# Production environment variables
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database optimization
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_DATABASE=library_production
DB_USERNAME=production_user
DB_PASSWORD=secure_password

# Cache configuration
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Performance optimization
OCTANE_SERVER=swoole  # Optional: Laravel Octane for performance
```

#### Deployment Steps

```bash
# 1. Clone and setup
git clone https://github.com/AdilAzhari/library-api-12.git
cd library-api-12

# 2. Install dependencies
composer install --optimize-autoloader --no-dev
npm ci

# 3. Configure environment
cp .env.production .env
php artisan key:generate

# 4. Database migration
php artisan migrate --force
php artisan db:seed --class=ProductionSeeder

# 5. Optimize application
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 6. Build frontend assets
npm run build

# 7. Set permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 755 storage bootstrap/cache
```

#### Queue Workers

Set up supervisor for queue workers:

```ini
# /etc/supervisor/conf.d/library-worker.conf
[program:library-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --queue=high,default,low --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
numprocs=4
redirect_stderr=true
stdout_logfile=/var/log/library-worker.log
stopwaitsecs=3600
```

#### Nginx Configuration

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name your-domain.com;
    root /path/to/library-api-12/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

#### SSL Configuration with Certbot

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx

# Obtain SSL certificate
sudo certbot --nginx -d your-domain.com

# Auto-renewal
sudo crontab -e
# Add: 0 12 * * * /usr/bin/certbot renew --quiet
```

### Docker Deployment

Use the included Docker configuration:

```bash
# Build and start containers
docker-compose up -d --build

# Run migrations in container
docker-compose exec app php artisan migrate --seed

# View logs
docker-compose logs -f app
```

### Performance Monitoring

#### Application Performance
```bash
# Install Laravel Horizon for queue monitoring
composer require laravel/horizon
php artisan horizon:install
php artisan migrate
```

#### Server Monitoring
- **Uptime Monitoring**: Use services like Pingdom or UptimeRobot
- **Error Tracking**: Integrate with Sentry or Bugsnag
- **Performance**: Monitor with New Relic or DataDog
- **Log Management**: Use ELK stack or Fluentd

---

## 🤝 Contributing

We welcome contributions! Please follow these guidelines:

### Getting Started
1. Fork the repository on GitHub
2. Clone your fork locally
3. Create a feature branch: `git checkout -b feature/amazing-feature`
4. Make your changes and commit them
5. Push to your fork and submit a pull request

### Development Guidelines

#### Code Standards
- Follow PSR-12 coding standards for PHP
- Use Laravel best practices and conventions
- Write meaningful commit messages
- Include tests for new functionality
- Update documentation as needed

#### Commit Messages
Use conventional commit format:
```
type(scope): description

feat(auth): add two-factor authentication
fix(api): resolve book search pagination issue
docs(readme): update installation instructions
test(borrowing): add integration tests for renewal process
```

#### Pull Request Process
1. **Description**: Provide clear description of changes
2. **Testing**: Ensure all tests pass
3. **Documentation**: Update relevant documentation
4. **Screenshots**: Include UI changes screenshots
5. **Breaking Changes**: Document any breaking changes

### Areas for Contribution

#### High Priority
- 🐛 Bug fixes and stability improvements
- 🔒 Security enhancements
- ⚡ Performance optimizations
- 📱 Mobile responsiveness improvements
- 🌍 Internationalization and localization

#### Medium Priority
- 🎨 UI/UX improvements
- 📊 Additional reporting features
- 🔌 Third-party integrations
- 📚 Documentation enhancements
- 🧪 Test coverage expansion

#### New Features
- 📧 Email notification system
- 💳 Payment gateway integration
- 📱 Mobile app development
- 🤖 AI-powered recommendations
- 📈 Advanced analytics dashboard

### Code Review Criteria

#### Technical Requirements
- Code follows established patterns and standards
- Adequate test coverage (aim for >80%)
- No security vulnerabilities
- Performance impact considered
- Backward compatibility maintained

#### Documentation Requirements
- Code is well-documented with PHPDoc
- README updated if needed
- API documentation updated
- User-facing changes documented

---

## 📄 License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

### MIT License Summary
- ✅ Commercial use allowed
- ✅ Modification allowed
- ✅ Distribution allowed
- ✅ Private use allowed
- ❌ No warranty provided
- ❌ No liability assumed

---

## 🙏 Acknowledgments

### Technology Stack
- **[Laravel](https://laravel.com)** - The PHP framework for web artisans
- **[Inertia.js](https://inertiajs.com)** - Modern monolith architecture
- **[Vue.js](https://vuejs.org)** - Progressive JavaScript framework
- **[TypeScript](https://www.typescriptlang.org)** - Typed JavaScript at scale
- **[Tailwind CSS](https://tailwindcss.com)** - Utility-first CSS framework
- **[Typesense](https://typesense.org)** - Fast, typo-tolerant search engine

### Contributors
- **Adil Azhari** - Project Creator & Maintainer
- **Community Contributors** - Thank you for your contributions!

### Inspiration
This project was inspired by the need for modern, efficient library management systems that can scale with growing institutions while providing excellent user experience.

---

## 📞 Support & Contact

### Getting Help
- **📋 Issues**: [GitHub Issues](https://github.com/AdilAzhari/library-api-12/issues) - Bug reports and feature requests
- **💬 Discussions**: [GitHub Discussions](https://github.com/AdilAzhari/library-api-12/discussions) - Community discussions
- **📧 Email**: [adil@example.com](mailto:adil@example.com) - Direct support

### Community
- **Discord**: Join our Discord server for real-time chat
- **Twitter**: Follow [@LibraryMS](https://twitter.com/LibraryMS) for updates
- **Blog**: Read our [development blog](https://blog.libraryms.com) for insights

### Professional Services
- **Custom Development**: Tailored features and integrations
- **Training & Support**: Staff training and technical support
- **Hosting & Maintenance**: Managed hosting solutions
- **Consulting**: Library digitization and automation consulting

---

**📍 Project Information**
- **Version**: 2.0.0
- **Status**: Active Development
- **Last Updated**: January 2025
- **Maintainer**: Adil Azhari
- **License**: MIT
- **Repository**: https://github.com/AdilAzhari/library-api-12

---

*Built with ❤️ for libraries worldwide*
