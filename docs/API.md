# Library Management System API Documentation

Complete API reference for the Library Management System. This RESTful API provides comprehensive access to all library operations including books, borrowing, reservations, users, and administration.

## Table of Contents

1. [Authentication](#authentication)
2. [API Response Format](#api-response-format)
3. [Error Handling](#error-handling)
4. [Rate Limiting](#rate-limiting)
5. [Books API](#books-api)
6. [Borrowing API](#borrowing-api)
7. [Reservations API](#reservations-api)
8. [Users API](#users-api)
9. [Reviews API](#reviews-api)
10. [Search API](#search-api)
11. [Admin API](#admin-api)
12. [WebHooks](#webhooks)

## Authentication

The API uses Laravel Sanctum for token-based authentication. All endpoints (except registration and login) require an authenticated user.

### Login
```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "user@example.com",
      "role": "User"
    },
    "token": "1|laravel_sanctum_token_here"
  }
}
```

### Register
```http
POST /api/auth/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "user@example.com",
  "password": "password",
  "password_confirmation": "password"
}
```

### Logout
```http
POST /api/auth/logout
Authorization: Bearer {token}
```

### Using the Token
Include the token in all subsequent requests:
```http
Authorization: Bearer 1|laravel_sanctum_token_here
```

## API Response Format

All API responses follow a consistent format:

### Success Response
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
    "last_page": 7,
    "from": 1,
    "to": 15
  }
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field_name": ["Validation error message"]
  }
}
```

## Error Handling

### HTTP Status Codes

| Status Code | Meaning |
|-------------|---------|
| 200 | OK - Request successful |
| 201 | Created - Resource created successfully |
| 204 | No Content - Resource deleted successfully |
| 400 | Bad Request - Invalid request data |
| 401 | Unauthorized - Authentication required |
| 403 | Forbidden - Insufficient permissions |
| 404 | Not Found - Resource not found |
| 422 | Unprocessable Entity - Validation errors |
| 429 | Too Many Requests - Rate limit exceeded |
| 500 | Internal Server Error - Server error |

### Common Error Responses

#### Validation Error (422)
```json
{
  "success": false,
  "message": "The given data was invalid.",
  "errors": {
    "title": ["The title field is required."],
    "author": ["The author field is required."]
  }
}
```

#### Authentication Error (401)
```json
{
  "success": false,
  "message": "Unauthenticated."
}
```

#### Authorization Error (403)
```json
{
  "success": false,
  "message": "This action is unauthorized."
}
```

#### Not Found Error (404)
```json
{
  "success": false,
  "message": "Resource not found."
}
```

## Rate Limiting

API requests are rate-limited to prevent abuse:

- **Authenticated users**: 1000 requests per hour
- **Guest users**: 100 requests per hour
- **Search endpoints**: 500 requests per hour

Rate limit information is included in response headers:
```
X-RateLimit-Limit: 1000
X-RateLimit-Remaining: 999
X-RateLimit-Reset: 1640995200
```

## Books API

### List Books
```http
GET /api/public/v1/books
Authorization: Bearer {token}
```

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| search | string | Search in title, author, description |
| genre | integer | Filter by genre ID |
| status | string | Filter by status (available, borrowed, reserved) |
| author | string | Filter by author name |
| year_from | integer | Publication year from |
| year_to | integer | Publication year to |
| sort | string | Sort order (title_asc, title_desc, year_asc, year_desc, rating_desc) |
| per_page | integer | Results per page (default: 15, max: 100) |
| page | integer | Page number |

**Example Request:**
```http
GET /api/public/v1/books?search=javascript&genre=1&per_page=20&sort=title_asc
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "Books retrieved successfully",
  "data": [
    {
      "id": 1,
      "title": "JavaScript: The Good Parts",
      "author": "Douglas Crockford",
      "isbn": "978-0596517748",
      "publication_year": 2008,
      "description": "A comprehensive guide to JavaScript programming...",
      "genre": {
        "id": 1,
        "name": "Programming"
      },
      "status": "available",
      "cover_image": "https://example.com/covers/js-good-parts.jpg",
      "reviews_count": 15,
      "average_rating": 4.5,
      "created_at": "2024-01-01T00:00:00Z",
      "updated_at": "2024-01-01T00:00:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 20,
    "total": 1,
    "last_page": 1
  }
}
```

### Get Book Details
```http
GET /api/public/v1/books/{id}
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "Book retrieved successfully",
  "data": {
    "id": 1,
    "title": "JavaScript: The Good Parts",
    "author": "Douglas Crockford",
    "isbn": "978-0596517748",
    "publication_year": 2008,
    "description": "A comprehensive guide to JavaScript programming...",
    "genre": {
      "id": 1,
      "name": "Programming"
    },
    "status": "available",
    "cover_image": "https://example.com/covers/js-good-parts.jpg",
    "reviews_count": 15,
    "average_rating": 4.5,
    "availability": {
      "is_available": true,
      "current_borrower": null,
      "due_date": null,
      "reservation_queue": 0
    },
    "reviews": [
      {
        "id": 1,
        "rating": 5,
        "comment": "Excellent book for understanding JavaScript fundamentals.",
        "user": {
          "id": 2,
          "name": "Jane Doe"
        },
        "created_at": "2024-01-01T00:00:00Z"
      }
    ],
    "related_books": [
      {
        "id": 2,
        "title": "Eloquent JavaScript",
        "author": "Marijn Haverbeke"
      }
    ]
  }
}
```

### Create Book
```http
POST /api/public/v1/books
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Required Permissions:** Admin or Librarian

**Request Body:**
```json
{
  "title": "New Book Title",
  "author": "Author Name",
  "isbn": "978-1234567890",
  "publication_year": 2024,
  "genre_id": 1,
  "description": "Book description",
  "cover_image": "file" // Optional image file
}
```

**Response:**
```json
{
  "success": true,
  "message": "Book created successfully",
  "data": {
    "id": 2,
    "title": "New Book Title",
    // ... other book fields
  }
}
```

### Update Book
```http
PUT /api/public/v1/books/{id}
Authorization: Bearer {token}
Content-Type: application/json
```

**Required Permissions:** Admin or Librarian

**Request Body:** Same as create book (all fields optional)

### Delete Book
```http
DELETE /api/public/v1/books/{id}
Authorization: Bearer {token}
```

**Required Permissions:** Admin or Librarian

**Response:**
```json
{
  "success": true,
  "message": "Book deleted successfully"
}
```

### Get Book Recommendations
```http
GET /api/books/recommendations
Authorization: Bearer {token}
```

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| book_id | integer | Get recommendations based on specific book |
| limit | integer | Number of recommendations (default: 10, max: 20) |

**Response:**
```json
{
  "success": true,
  "message": "Recommendations retrieved successfully",
  "data": [
    {
      "id": 3,
      "title": "You Don't Know JS",
      "author": "Kyle Simpson",
      "reason": "Similar genre and high rating",
      "similarity_score": 0.85
    }
  ]
}
```

## Borrowing API

### Borrow Book
```http
POST /api/public/v1/borrow
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "book_id": 1,
  "user_id": 1  // Optional: defaults to authenticated user
}
```

**Response:**
```json
{
  "success": true,
  "message": "Book borrowed successfully",
  "data": {
    "id": 1,
    "book_id": 1,
    "user_id": 1,
    "borrowed_at": "2024-01-01T10:00:00Z",
    "due_date": "2024-01-15T10:00:00Z",
    "returned_at": null,
    "renewal_count": 0,
    "book": {
      "id": 1,
      "title": "JavaScript: The Good Parts",
      "author": "Douglas Crockford"
    }
  }
}
```

### Return Book
```http
POST /api/public/v1/return
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "book_id": 1,
  "user_id": 1  // Optional: defaults to authenticated user
}
```

**Response:**
```json
{
  "success": true,
  "message": "Book returned successfully",
  "data": {
    "id": 1,
    "book_id": 1,
    "user_id": 1,
    "borrowed_at": "2024-01-01T10:00:00Z",
    "due_date": "2024-01-15T10:00:00Z",
    "returned_at": "2024-01-14T16:30:00Z",
    "late_fee": 0,
    "book": {
      "id": 1,
      "title": "JavaScript: The Good Parts",
      "author": "Douglas Crockford"
    }
  }
}
```

### Get Active Borrows
```http
GET /api/borrows/active
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "Active borrows retrieved successfully",
  "data": [
    {
      "id": 1,
      "book_id": 1,
      "borrowed_at": "2024-01-01T10:00:00Z",
      "due_date": "2024-01-15T10:00:00Z",
      "is_overdue": false,
      "days_until_due": 5,
      "can_renew": true,
      "renewal_count": 0,
      "book": {
        "id": 1,
        "title": "JavaScript: The Good Parts",
        "author": "Douglas Crockford",
        "cover_image": "https://example.com/covers/js-good-parts.jpg"
      }
    }
  ]
}
```

### Get Borrowing History
```http
GET /api/borrows/history
Authorization: Bearer {token}
```

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| status | string | Filter by status (active, returned, overdue) |
| per_page | integer | Results per page (default: 15) |
| page | integer | Page number |

### Renew Book
```http
POST /api/borrows/{borrow_id}/renew
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "Book renewed successfully",
  "data": {
    "id": 1,
    "new_due_date": "2024-01-29T10:00:00Z",
    "renewal_count": 1,
    "renewals_remaining": 1
  }
}
```

### Check Renewal Eligibility
```http
GET /api/borrows/{borrow_id}/can-renew
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "Renewal eligibility checked",
  "data": {
    "can_renew": true,
    "renewals_used": 0,
    "max_renewals": 2,
    "has_reservations": false,
    "is_overdue": false,
    "reason": "Renewal allowed"
  }
}
```

## Reservations API

### Make Reservation
```http
POST /api/reservations
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "book_id": 1,
  "user_id": 1,  // Optional: defaults to authenticated user
  "expires_at": "2024-12-31T23:59:59Z"  // Optional: defaults to 7 days from now
}
```

**Response:**
```json
{
  "success": true,
  "message": "Reservation created successfully",
  "data": {
    "id": 1,
    "book_id": 1,
    "user_id": 1,
    "reserved_at": "2024-01-01T10:00:00Z",
    "expires_at": "2024-01-08T10:00:00Z",
    "queue_position": 2,
    "estimated_available_date": "2024-01-05T10:00:00Z",
    "book": {
      "id": 1,
      "title": "JavaScript: The Good Parts",
      "author": "Douglas Crockford"
    }
  }
}
```

### List Reservations
```http
GET /api/reservations
Authorization: Bearer {token}
```

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| status | string | Filter by status (active, expired, fulfilled) |
| per_page | integer | Results per page (default: 15) |

**Response:**
```json
{
  "success": true,
  "message": "Reservations retrieved successfully",
  "data": [
    {
      "id": 1,
      "book_id": 1,
      "reserved_at": "2024-01-01T10:00:00Z",
      "expires_at": "2024-01-08T10:00:00Z",
      "status": "active",
      "queue_position": 1,
      "book": {
        "id": 1,
        "title": "JavaScript: The Good Parts",
        "author": "Douglas Crockford",
        "cover_image": "https://example.com/covers/js-good-parts.jpg"
      }
    }
  ]
}
```

### Cancel Reservation
```http
DELETE /api/reservations/{id}
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "Reservation cancelled successfully"
}
```

### Get Reservation Details
```http
GET /api/reservations/{id}
Authorization: Bearer {token}
```

## Users API

### Get User Profile
```http
GET /api/user/profile
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "Profile retrieved successfully",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "user@example.com",
    "role": "User",
    "phone": "+1234567890",
    "address": "123 Main St, City",
    "profile_photo_url": "https://example.com/photos/user1.jpg",
    "statistics": {
      "books_borrowed": 25,
      "books_returned": 23,
      "active_borrows": 2,
      "active_reservations": 1,
      "total_fines": 5.50,
      "outstanding_fines": 2.00
    },
    "preferences": {
      "favorite_genres": ["Programming", "Science Fiction"],
      "notification_settings": {
        "email_reminders": true,
        "due_date_alerts": true,
        "reservation_alerts": true
      }
    }
  }
}
```

### Update User Profile
```http
PUT /api/user/profile
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Request Body:**
```json
{
  "name": "John Doe Updated",
  "phone": "+1234567890",
  "address": "456 New St, City",
  "profile_photo": "file"  // Optional image file
}
```

### Get User Dashboard
```http
GET /api/user/dashboard
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "Dashboard data retrieved successfully",
  "data": {
    "active_borrows": [
      {
        "id": 1,
        "book": {
          "title": "JavaScript: The Good Parts",
          "author": "Douglas Crockford"
        },
        "due_date": "2024-01-15T10:00:00Z",
        "is_overdue": false
      }
    ],
    "active_reservations": [
      {
        "id": 1,
        "book": {
          "title": "Clean Code",
          "author": "Robert C. Martin"
        },
        "queue_position": 2,
        "estimated_available": "2024-01-10T10:00:00Z"
      }
    ],
    "recommendations": [
      {
        "id": 3,
        "title": "Eloquent JavaScript",
        "author": "Marijn Haverbeke",
        "reason": "Based on your reading history"
      }
    ],
    "recent_activity": [
      {
        "type": "borrow",
        "description": "Borrowed 'JavaScript: The Good Parts'",
        "date": "2024-01-01T10:00:00Z"
      }
    ]
  }
}
```

## Reviews API

### Create Review
```http
POST /api/reviews
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "book_id": 1,
  "rating": 5,
  "comment": "Excellent book for understanding JavaScript fundamentals."
}
```

**Response:**
```json
{
  "success": true,
  "message": "Review created successfully",
  "data": {
    "id": 1,
    "book_id": 1,
    "user_id": 1,
    "rating": 5,
    "comment": "Excellent book for understanding JavaScript fundamentals.",
    "status": "published",
    "created_at": "2024-01-01T10:00:00Z",
    "user": {
      "id": 1,
      "name": "John Doe"
    },
    "book": {
      "id": 1,
      "title": "JavaScript: The Good Parts"
    }
  }
}
```

### Get Book Reviews
```http
GET /api/books/{book_id}/reviews
Authorization: Bearer {token}
```

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| rating | integer | Filter by rating (1-5) |
| sort | string | Sort order (newest, oldest, rating_high, rating_low) |
| per_page | integer | Results per page (default: 15) |

### Update Review
```http
PUT /api/reviews/{id}
Authorization: Bearer {token}
Content-Type: application/json
```

### Delete Review
```http
DELETE /api/reviews/{id}
Authorization: Bearer {token}
```

## Search API

### Advanced Book Search
```http
GET /api/search/books
Authorization: Bearer {token}
```

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| q | string | Search query (searches title, author, description) |
| filters[genre] | integer | Genre filter |
| filters[status] | string | Status filter |
| filters[year_range] | string | Year range (e.g., "2000-2024") |
| filters[rating] | integer | Minimum rating |
| sort | string | Sort order |
| per_page | integer | Results per page |

**Example:**
```http
GET /api/search/books?q=javascript&filters[genre]=1&filters[rating]=4&sort=rating_desc
```

### Search Suggestions
```http
GET /api/search/suggestions
Authorization: Bearer {token}
```

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| q | string | Partial query for suggestions |
| type | string | Suggestion type (titles, authors, genres) |

**Response:**
```json
{
  "success": true,
  "message": "Suggestions retrieved successfully",
  "data": {
    "titles": ["JavaScript: The Good Parts", "JavaScript Patterns"],
    "authors": ["Douglas Crockford", "David Flanagan"],
    "genres": ["Programming", "Web Development"]
  }
}
```

## Admin API

### Dashboard Statistics
```http
GET /api/admin/dashboard
Authorization: Bearer {token}
```

**Required Permissions:** Admin

**Response:**
```json
{
  "success": true,
  "message": "Admin dashboard data retrieved successfully",
  "data": {
    "statistics": {
      "total_books": 1250,
      "total_users": 450,
      "active_borrows": 89,
      "overdue_books": 12,
      "total_fines": 245.50,
      "new_registrations_today": 5
    },
    "charts": {
      "borrowing_trends": {
        "labels": ["Jan", "Feb", "Mar", "Apr", "May"],
        "data": [120, 135, 98, 156, 142]
      },
      "popular_genres": {
        "Programming": 45,
        "Fiction": 32,
        "Science": 28
      }
    },
    "recent_activities": [
      {
        "type": "borrow",
        "user": "John Doe",
        "book": "JavaScript: The Good Parts",
        "timestamp": "2024-01-01T10:00:00Z"
      }
    ]
  }
}
```

### User Management
```http
GET /api/admin/users
Authorization: Bearer {token}
```

**Required Permissions:** Admin

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| role | string | Filter by role |
| status | string | Filter by status (active, suspended) |
| search | string | Search by name or email |

### System Reports
```http
GET /api/admin/reports/{type}
Authorization: Bearer {token}
```

**Required Permissions:** Admin

**Available Report Types:**
- `borrowing` - Borrowing statistics and trends
- `overdue` - Overdue books and users
- `financial` - Fine collection and revenue
- `usage` - System usage analytics
- `inventory` - Book inventory and availability

### Export Data
```http
GET /api/admin/export/{type}
Authorization: Bearer {token}
```

**Required Permissions:** Admin

**Available Export Types:**
- `books` - All books data
- `users` - User information
- `borrowing` - Borrowing history
- `fines` - Financial data

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| format | string | Export format (csv, excel, pdf) |
| date_from | string | Start date for data range |
| date_to | string | End date for data range |

## WebHooks

The API supports webhooks for real-time notifications of important events.

### Available Events

- `book.created` - New book added to catalog
- `book.updated` - Book information updated
- `book.deleted` - Book removed from catalog
- `borrow.created` - Book borrowed by user
- `borrow.returned` - Book returned by user
- `borrow.overdue` - Book is overdue
- `reservation.created` - New reservation made
- `reservation.fulfilled` - Reservation fulfilled
- `user.registered` - New user registered

### Webhook Payload Example

```json
{
  "event": "borrow.created",
  "timestamp": "2024-01-01T10:00:00Z",
  "data": {
    "borrow": {
      "id": 1,
      "book_id": 1,
      "user_id": 1,
      "borrowed_at": "2024-01-01T10:00:00Z",
      "due_date": "2024-01-15T10:00:00Z"
    },
    "book": {
      "id": 1,
      "title": "JavaScript: The Good Parts",
      "author": "Douglas Crockford"
    },
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "user@example.com"
    }
  }
}
```

### Webhook Security

Webhooks are signed using HMAC-SHA256. Verify the signature using the `X-Signature-256` header:

```php
$signature = hash_hmac('sha256', $payload, $webhook_secret);
$expected = 'sha256=' . $signature;
$received = $_SERVER['HTTP_X_SIGNATURE_256'] ?? '';

if (!hash_equals($expected, $received)) {
    // Invalid signature
    exit('Invalid signature');
}
```

## SDK and Code Examples

### JavaScript/TypeScript Example

```javascript
class LibraryAPI {
  constructor(baseURL, token) {
    this.baseURL = baseURL;
    this.token = token;
  }

  async request(endpoint, options = {}) {
    const url = `${this.baseURL}${endpoint}`;
    const config = {
      headers: {
        'Authorization': `Bearer ${this.token}`,
        'Content-Type': 'application/json',
        ...options.headers
      },
      ...options
    };

    const response = await fetch(url, config);
    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.message || 'API request failed');
    }

    return data;
  }

  // Get books
  async getBooks(params = {}) {
    const query = new URLSearchParams(params);
    return this.request(`/api/public/v1/books?${query}`);
  }

  // Borrow book
  async borrowBook(bookId, userId = null) {
    return this.request('/api/public/v1/borrow', {
      method: 'POST',
      body: JSON.stringify({ book_id: bookId, user_id: userId })
    });
  }

  // Return book
  async returnBook(bookId, userId = null) {
    return this.request('/api/public/v1/return', {
      method: 'POST',
      body: JSON.stringify({ book_id: bookId, user_id: userId })
    });
  }
}

// Usage
const api = new LibraryAPI('https://library.example.com', 'your-token');

try {
  const books = await api.getBooks({ search: 'javascript', per_page: 20 });
  console.log(books.data);
} catch (error) {
  console.error('Error:', error.message);
}
```

### PHP Example

```php
class LibraryAPI {
    private $baseURL;
    private $token;

    public function __construct($baseURL, $token) {
        $this->baseURL = $baseURL;
        $this->token = $token;
    }

    public function request($endpoint, $method = 'GET', $data = null) {
        $curl = curl_init();
        
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->baseURL . $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->token,
                'Content-Type: application/json',
            ],
        ]);

        if ($data) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $result = json_decode($response, true);

        if ($httpCode >= 400) {
            throw new Exception($result['message'] ?? 'API request failed');
        }

        return $result;
    }

    public function getBooks($params = []) {
        $query = http_build_query($params);
        return $this->request("/api/public/v1/books?{$query}");
    }

    public function borrowBook($bookId, $userId = null) {
        return $this->request('/api/public/v1/borrow', 'POST', [
            'book_id' => $bookId,
            'user_id' => $userId
        ]);
    }
}

// Usage
$api = new LibraryAPI('https://library.example.com', 'your-token');

try {
    $books = $api->getBooks(['search' => 'javascript', 'per_page' => 20]);
    print_r($books['data']);
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
```

### Python Example

```python
import requests
import json

class LibraryAPI:
    def __init__(self, base_url, token):
        self.base_url = base_url
        self.token = token
        self.session = requests.Session()
        self.session.headers.update({
            'Authorization': f'Bearer {token}',
            'Content-Type': 'application/json'
        })

    def request(self, endpoint, method='GET', data=None):
        url = f"{self.base_url}{endpoint}"
        response = self.session.request(method, url, json=data)
        
        if not response.ok:
            error_data = response.json()
            raise Exception(error_data.get('message', 'API request failed'))
        
        return response.json()

    def get_books(self, **params):
        query = '&'.join([f"{k}={v}" for k, v in params.items()])
        return self.request(f"/api/public/v1/books?{query}")

    def borrow_book(self, book_id, user_id=None):
        return self.request('/api/public/v1/borrow', 'POST', {
            'book_id': book_id,
            'user_id': user_id
        })

# Usage
api = LibraryAPI('https://library.example.com', 'your-token')

try:
    books = api.get_books(search='javascript', per_page=20)
    print(json.dumps(books['data'], indent=2))
except Exception as e:
    print(f'Error: {e}')
```

## Changelog

### Version 2.0.0 (Current)
- Added comprehensive search with Typesense integration
- Enhanced borrowing workflow with automatic fine calculation
- Added reservation system with queue management
- Improved authentication with role-based permissions
- Added webhook support for real-time notifications
- Enhanced error handling and validation

### Version 1.0.0
- Initial API release
- Basic CRUD operations for books and users
- Simple borrowing system
- Basic authentication with Sanctum

---

**API Version:** 2.0.0  
**Last Updated:** January 2025  
**Contact:** [API Support](mailto:api-support@library.example.com)