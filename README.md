# Library API

A robust Laravel-based API for managing a library system. This project includes features like **GraphQL**, **Event Sourcing**, **Multi-Language Support**, and more.

---

## Table of Contents

1. [Features](#features)
2. [Installation](#installation)
3. [API Endpoints](#api-endpoints)
4. [GraphQL](#graphql)
5. [Event Sourcing](#event-sourcing)
6. [Multi-Language Support](#multi-language-support)
7. [Testing](#testing)
8. [Contributing](#contributing)
9. [License](#license)

---

## Features

- **REST API**: Manage books with CRUD operations.
- **GraphQL**: Flexible querying for books and related data.
- **Event Sourcing**: Track changes to books using events.
- **Multi-Language Support**: Supports English and Arabic with automatic translation.
- **Export Data**: Export books to Excel.
- **Google Books Integration**: Fetch book data from Google Books API.
- **Recommendations**: Get book recommendations based on genre.
- **Health Check**: A simple endpoint to check API health.

---

## Installation

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/your-username/library-api.git
   cd library-api
   ```

2. **Install Dependencies**:
   ```bash
   composer install
   ```

3. **Set Up Environment**:
   - Copy `.env.example` to `.env`:
     ```bash
     cp .env.example .env
     ```
   - Generate an application key:
     ```bash
     php artisan key:generate
     ```
   - Configure your database in `.env`:
     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=library
     DB_USERNAME=root
     DB_PASSWORD=
     ```

4. **Run Migrations**:
   ```bash
   php artisan migrate
   ```

5. **Seed the Database (Optional)**:
   ```bash
   php artisan db:seed
   ```

6. **Start the Development Server**:
   ```bash
   php artisan serve
   ```

---

## API Endpoints

### REST API

| Method   | URI                              | Description                     |
|----------|----------------------------------|---------------------------------|
| GET      | `/api/v1/books`                  | List all books                  |
| POST     | `/api/v1/books`                  | Create a new book               |
| GET      | `/api/v1/books/{book}`           | Get a single book               |
| PUT      | `/api/v1/books/{book}`           | Update a book                   |
| DELETE   | `/api/v1/books/{book}`           | Delete a book                   |
| POST     | `/api/v1/books/{id}/restore`     | Restore a deleted book          |
| GET      | `/api/v1/books/export`           | Export books to Excel           |
| GET      | `/api/v1/books/fetch-from-google`| Fetch books from Google Books   |
| GET      | `/api/v1/books/{book}/recommend` | Get book recommendations        |
| GET      | `/api/v1/health`                 | Health check endpoint           |

---

## GraphQL

The API supports GraphQL for flexible querying. Use the `/graphql` endpoint to interact with the GraphQL API.

### Example Queries

1. **Get All Books**:
   ```graphql
   query {
       books {
           id
           title
           author
       }
   }
   ```

2. **Get a Single Book**:
   ```graphql
   query {
       book(id: 1) {
           id
           title
           author
       }
   }
   ```

3. **Create a Book**:
   ```graphql
   mutation {
       createBook(input: {
           title: "Laravel API",
           author: "John Doe",
           publication_year: 2024,
           description: "A book about Laravel APIs."
       }) {
           id
           title
       }
   }
   ```

---

## Event Sourcing

The API uses Event Sourcing to track changes to books. All changes are stored as events, and the current state is rebuilt by replaying these events.

### Example Events

- **BookCreated**: Emitted when a new book is created.
- **BookUpdated**: Emitted when a book is updated.
- **BookDeleted**: Emitted when a book is deleted.

### Example Usage

```php
$bookAggregate = BookAggregate::retrieve($uuid);
$bookAggregate->createBook([
    'title' => 'Laravel API',
    'author' => 'John Doe',
])->persist();
```

---

## Multi-Language Support

The API supports English and Arabic. Translations are stored in the `lang` directory.

### Example Translations

1. **English** (`lang/en/messages.php`):
   ```php
   return [
       'welcome' => 'Welcome to our application!',
       'book' => 'Book',
   ];
   ```

2. **Arabic** (`lang/ar/messages.php`):
   ```php
   return [
       'welcome' => 'مرحبًا بكم في تطبيقنا!',
       'book' => 'كتاب',
   ];
   ```

### Automatic Translation

The API integrates with **Google Translate** for automatic translation between English and Arabic.

```php
use Stichoza\GoogleTranslate\GoogleTranslate;

$translator = new GoogleTranslate();
$translator->setSource('en')->setTarget('ar');

$translatedText = $translator->translate('Welcome to our application!');
echo $translatedText; // Outputs "مرحبًا بكم في تطبيقنا!"
```

---

## Testing

Run the tests using Pest:

```bash
php artisan test
```

---

## Contributing

1. Fork the repository.
2. Create a new branch (`git checkout -b feature/YourFeature`).
3. Commit your changes (`git commit -m 'Add some feature'`).
4. Push to the branch (`git push origin feature/YourFeature`).
5. Open a pull request.

---

## License

This project is open-source and available under the [MIT License](LICENSE).

---
