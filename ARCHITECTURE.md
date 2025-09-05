# BiblioTech Hub - User Flow Based Architecture

## 🚀 User Flow Analysis & Implementation

### **1. NEW USER JOURNEY FLOW**

#### **Step 1: Registration & Onboarding**
```
Route: /register → Controllers: Auth\RegisteredUserController
Middleware: guest, throttle:6,1
UI: resources/js/Pages/Auth/Register.vue
```

#### **Step 2: Email Verification**
```
Route: /email/verify → Controllers: Auth\VerifyEmailController  
Middleware: auth, signed, throttle:6,1
UI: resources/js/Pages/Auth/VerifyEmail.vue
```

#### **Step 3: Profile Setup**
```
Route: /profile/setup → Controllers: ProfileController@setup
Middleware: auth, verified
UI: resources/js/Pages/Profile/Setup.vue
```

#### **Step 4: Library Card Generation**
```
Route: /library-card/generate → Controllers: LibraryCardController@generate
Middleware: auth, verified, profile.complete
UI: resources/js/Pages/LibraryCard/Generate.vue
```

#### **Step 5: Dashboard Welcome**
```
Route: /dashboard → Controllers: DashboardController@index
Middleware: auth, verified, library.card
UI: resources/js/Pages/Dashboard.vue
```

---

### **2. BORROWING FLOW**

#### **Step 1: Book Discovery**
```
Route: /books → Controllers: BookController@index
Middleware: auth, verified
API: /api/books/search (for autocomplete)
UI: resources/js/Pages/Books/Index.vue
```

#### **Step 2: Book Details & Reserve/Borrow Decision**
```
Route: /books/{book} → Controllers: BookController@show
Middleware: auth, verified, overdue.check
UI: resources/js/Pages/Books/Show.vue
```

#### **Step 3a: Direct Borrow (if available)**
```
Route: /books/{book}/borrow → Controllers: BorrowController@create
Middleware: auth, verified, can.borrow, overdue.block
UI: resources/js/Pages/Borrows/Create.vue

POST /books/{book}/borrow → Controllers: BorrowController@store
Middleware: auth, verified, can.borrow, overdue.block
Redirect: /borrows → Flash: success
```

#### **Step 3b: Reserve (if not available)**
```
Route: /books/{book}/reserve → Controllers: ReservationController@create  
Middleware: auth, verified, can.reserve
UI: resources/js/Pages/Reservations/Create.vue

POST /books/{book}/reserve → Controllers: ReservationController@store
Middleware: auth, verified, can.reserve
Redirect: /reservations → Flash: success
```

#### **Step 4: Confirmation & Management**
```
Route: /borrows → Controllers: BorrowController@index
Route: /reservations → Controllers: ReservationController@index
Middleware: auth, verified
UI: resources/js/Pages/Borrows/Index.vue
UI: resources/js/Pages/Reservations/Index.vue
```

---

### **3. OVERDUE MANAGEMENT FLOW**

#### **Step 1: Overdue Detection (Scheduled)**
```
Command: php artisan library:check-overdue
Controllers: Console\Commands\CheckOverdueBooks
Middleware: N/A (Console)
Notifications: App\Notifications\OverdueBookNotification
```

#### **Step 2: User Dashboard Alert**
```
Route: /dashboard → Controllers: DashboardController@index
Middleware: auth, verified, overdue.alert
UI: resources/js/Pages/Dashboard.vue (overdue alerts)
```

#### **Step 3: Overdue Books Management**
```
Route: /borrows?filter=overdue → Controllers: BorrowController@index
Middleware: auth, verified
UI: resources/js/Pages/Borrows/Index.vue (overdue tab)
```

#### **Step 4: Return Process**
```
Route: /borrows/{borrow}/return → Controllers: BorrowController@showReturn
Middleware: auth, verified, owns.borrow
UI: resources/js/Pages/Borrows/Return.vue

POST /borrows/{borrow}/return → Controllers: BorrowController@processReturn
Middleware: auth, verified, owns.borrow
Redirect: /borrows → Flash: success/warning (if fines)
```

#### **Step 5: Fine Management**
```
Route: /fines → Controllers: FineController@index
Route: /fines/{fine}/pay → Controllers: FineController@showPayment
POST /fines/{fine}/pay → Controllers: FineController@processPayment
Middleware: auth, verified, owns.fine
UI: resources/js/Pages/Fines/Index.vue
UI: resources/js/Pages/Fines/Payment.vue
```

---

### **4. RESERVATION FLOW**

#### **Step 1: Join Queue**
```
Route: /books/{book}/reserve → Controllers: ReservationController@create
Middleware: auth, verified, can.reserve, overdue.check
UI: resources/js/Pages/Reservations/Create.vue
```

#### **Step 2: Queue Management**
```
Route: /reservations → Controllers: ReservationController@index
Middleware: auth, verified
UI: resources/js/Pages/Reservations/Index.vue
```

#### **Step 3: Ready Notification (Automated)**
```
Event: BookReturned → Listener: UpdateReservationQueue
Notification: ReservationReadyNotification
Email/SMS: reservation-ready templates
```

#### **Step 4: Pickup Process**
```
Route: /reservations → Controllers: ReservationController@index (ready filter)
POST /reservations/{reservation}/pickup → Controllers: ReservationController@confirmPickup
Middleware: auth, verified, owns.reservation, reservation.ready
Redirect: /borrows → Flash: success
```

---

## 🛡️ MIDDLEWARE ORGANIZATION

### **Authentication Flow Middlewares**
```php
// routes/web.php
Route::middleware(['guest'])->group(function () {
    // Registration, login routes
});

Route::middleware(['auth'])->group(function () {
    // Email verification required routes
    Route::middleware(['verified'])->group(function () {
        // Main application routes
    });
});
```

### **Role-Based Access Control**
```php
// Core user routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard, profile, basic features
});

// Admin routes  
Route::prefix('admin')->middleware(['auth', 'verified', 'role:admin'])->group(function () {
    // Admin-only features
});
```

### **Borrowing-Specific Middlewares**
```php
// Apply to borrowing-related routes
Route::middleware(['auth', 'verified', 'overdue.check'])->group(function () {
    Route::get('/books/{book}', [BookController::class, 'show']);
    Route::get('/books/{book}/reserve', [ReservationController::class, 'create']);
});

// Block borrowing if overdue
Route::middleware(['auth', 'verified', 'overdue.block'])->group(function () {
    Route::post('/books/{book}/borrow', [BorrowController::class, 'store']);
    Route::get('/books/{book}/borrow', [BorrowController::class, 'create']);
});

// Ownership verification
Route::middleware(['auth', 'verified', 'owns:borrow'])->group(function () {
    Route::get('/borrows/{borrow}/return', [BorrowController::class, 'showReturn']);
    Route::post('/borrows/{borrow}/return', [BorrowController::class, 'processReturn']);
});
```

---

## 🔗 ROUTE ORGANIZATION BY FLOW

### **File Structure**
```
routes/
├── web.php              # Main routes entry point
├── auth.php             # Authentication flow routes  
├── borrowing.php        # Borrowing flow routes
├── reservations.php     # Reservation flow routes
├── fines.php           # Overdue/fines flow routes
├── admin.php           # Admin flow routes
└── api.php             # API endpoints
```

### **Main Routes File (web.php)**
```php
<?php
// routes/web.php

use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentication flow
require __DIR__.'/auth.php';

// Protected application routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard & Profile
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('profile', ProfileController::class)->except(['index', 'destroy']);
    
    // Core flows
    require __DIR__.'/borrowing.php';
    require __DIR__.'/reservations.php';  
    require __DIR__.'/fines.php';
});

// Admin routes
Route::prefix('admin')->middleware(['auth', 'verified', 'role:admin'])->group(function () {
    require __DIR__.'/admin.php';
});
```

### **Borrowing Flow Routes (routes/borrowing.php)**
```php
<?php
// routes/borrowing.php

use App\Http\Controllers\{BorrowController, BookController, ReadingListController};
use Illuminate\Support\Facades\Route;

// Book discovery and browsing
Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/books/{book}', [BookController::class, 'show'])
    ->middleware(['overdue.check'])
    ->name('books.show');

// Borrowing workflow
Route::middleware(['overdue.block'])->group(function () {
    Route::get('/books/{book}/borrow', [BorrowController::class, 'create'])->name('borrows.create');
    Route::post('/books/{book}/borrow', [BorrowController::class, 'store'])->name('borrows.store');
});

// Borrow management
Route::get('/borrows', [BorrowController::class, 'index'])->name('borrows.index');

// Return workflow
Route::middleware(['owns:borrow'])->group(function () {
    Route::get('/borrows/{borrow}/return', [BorrowController::class, 'showReturn'])->name('borrows.return.show');
    Route::post('/borrows/{borrow}/return', [BorrowController::class, 'processReturn'])->name('borrows.return.process');
    Route::post('/borrows/{borrow}/renew', [BorrowController::class, 'renew'])->name('borrows.renew');
});

// Reading Lists integration
Route::resource('reading-lists', ReadingListController::class);
Route::post('/reading-lists/{readingList}/books/{book}', [ReadingListController::class, 'addBook'])->name('reading-lists.add-book');
Route::delete('/reading-lists/{readingList}/books/{bookItem}', [ReadingListController::class, 'removeBook'])->name('reading-lists.remove-book');
```

### **Reservation Flow Routes (routes/reservations.php)**
```php
<?php
// routes/reservations.php  

use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;

// Reservation workflow
Route::get('/books/{book}/reserve', [ReservationController::class, 'create'])
    ->middleware(['overdue.check'])
    ->name('reservations.create');
Route::post('/books/{book}/reserve', [ReservationController::class, 'store'])
    ->middleware(['overdue.check'])
    ->name('reservations.store');

// Reservation management
Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');

// Reservation actions
Route::middleware(['owns:reservation'])->group(function () {
    Route::post('/reservations/{reservation}/pickup', [ReservationController::class, 'confirmPickup'])->name('reservations.pickup');
    Route::post('/reservations/{reservation}/extend', [ReservationController::class, 'extend'])->name('reservations.extend');
    Route::delete('/reservations/{reservation}', [ReservationController::class, 'cancel'])->name('reservations.cancel');
    Route::post('/reservations/{reservation}/duplicate', [ReservationController::class, 'duplicate'])->name('reservations.duplicate');
});
```

### **Fines Flow Routes (routes/fines.php)**
```php
<?php
// routes/fines.php

use App\Http\Controllers\FineController;
use Illuminate\Support\Facades\Route;

// Fine management
Route::get('/fines', [FineController::class, 'index'])->name('fines.index');

Route::middleware(['owns:fine'])->group(function () {
    Route::get('/fines/{fine}', [FineController::class, 'show'])->name('fines.show');
    Route::get('/fines/{fine}/pay', [FineController::class, 'showPayment'])->name('fines.payment.show');  
    Route::post('/fines/{fine}/pay', [FineController::class, 'processPayment'])->name('fines.payment.process');
});
```

---

## 🔌 API ENDPOINTS ALIGNED WITH FLOWS

### **API Routes Structure (routes/api.php)**
```php
<?php
// routes/api.php

use App\Http\Controllers\Api\{BookController, BorrowController, ReservationController, FineController};
use Illuminate\Support\Facades\Route;

// Authentication required for all API routes
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    
    // Book search and discovery
    Route::prefix('books')->group(function () {
        Route::get('/search', [BookController::class, 'search']); // Autocomplete/filtering
        Route::get('/recommendations', [BookController::class, 'recommendations']); // Personalized recommendations
        Route::get('/{book}/availability', [BookController::class, 'availability']); // Real-time availability
    });
    
    // Borrowing API endpoints
    Route::prefix('borrows')->group(function () {
        Route::get('/active', [BorrowController::class, 'active']); // Active borrows for dashboard
        Route::get('/overdue', [BorrowController::class, 'overdue']); // Overdue items
        Route::get('/due-soon', [BorrowController::class, 'dueSoon']); // Due soon alerts
        Route::post('/{borrow}/renew', [BorrowController::class, 'renew']); // Quick renew
    });
    
    // Reservation API endpoints  
    Route::prefix('reservations')->group(function () {
        Route::get('/queue/{book}', [ReservationController::class, 'queueInfo']); // Queue position/wait time
        Route::get('/ready', [ReservationController::class, 'ready']); // Ready for pickup
        Route::post('/{reservation}/notification-preferences', [ReservationController::class, 'updateNotifications']);
    });
    
    // Reading Lists API
    Route::get('/reading-lists/user', [ReadingListController::class, 'userLists']); // For modals/quick access
    
    // Fines API
    Route::prefix('fines')->group(function () {
        Route::get('/summary', [FineController::class, 'summary']); // Dashboard summary
        Route::get('/payment-methods', [FineController::class, 'paymentMethods']); // Available payment options
    });
    
    // Dashboard data aggregation
    Route::get('/dashboard-data', [DashboardController::class, 'dashboardData']); // Combined dashboard info
});
```

---

## 🎨 UI COMPONENT CONNECTIONS

### **Flow-Based Component Organization**
```
resources/js/Pages/
├── Auth/                    # Authentication flow
│   ├── Login.vue
│   ├── Register.vue  
│   └── VerifyEmail.vue
├── Dashboard.vue            # Central hub - connects to all flows
├── Books/                   # Book discovery flow
│   ├── Index.vue           # → Show.vue → (Reserve|Borrow)
│   └── Show.vue            # → Borrows/Create.vue | Reservations/Create.vue
├── Borrows/                 # Borrowing flow  
│   ├── Index.vue           # → Return.vue
│   ├── Create.vue          # → Index.vue
│   └── Return.vue          # → Fines/Payment.vue (if fines)
├── Reservations/            # Reservation flow
│   ├── Index.vue           # → Create.vue, manage queue
│   └── Create.vue          # → Index.vue
├── Fines/                   # Overdue management flow
│   ├── Index.vue           # → Show.vue → Payment.vue
│   ├── Show.vue            # → Payment.vue
│   └── Payment.vue         # → Index.vue
└── ReadingLists/           # Cross-flow integration
    ├── Index.vue
    ├── Create.vue
    ├── Show.vue
    └── Edit.vue
```

### **Component Flow Connections**
```vue
<!-- Books/Show.vue connects to multiple flows -->
<template>
  <!-- Primary action based on book status -->
  <button v-if="book.status === 'available'" @click="borrowBook">
    <!-- → Borrows/Create.vue -->
  </button>
  <button v-else @click="reserveBook">  
    <!-- → Reservations/Create.vue -->
  </button>
  
  <!-- Secondary actions -->
  <button @click="addToReadingList">
    <!-- → Opens ReadingList modal -->
  </button>
</template>
```

```vue
<!-- Dashboard.vue as central hub -->
<template>
  <div class="dashboard">
    <!-- Quick actions linking to flows -->
    <Link href="/borrows?filter=overdue">{{ overdueCount }} Overdue</Link> <!-- → Borrows/Index.vue -->
    <Link href="/reservations?filter=ready">{{ readyCount }} Ready</Link>   <!-- → Reservations/Index.vue -->
    <Link href="/fines">{{ fineAmount }} Outstanding</Link>                  <!-- → Fines/Index.vue -->
    
    <!-- Recently borrowed books -->
    <div v-for="borrow in recentBorrows">
      <Link :href="`/books/${borrow.book_id}`">{{ borrow.book.title }}</Link> <!-- → Books/Show.vue -->
    </div>
  </div>
</template>
```

---

## 🔐 CUSTOM MIDDLEWARE IMPLEMENTATION

### **Overdue Check Middleware**
```php
<?php
// app/Http/Middleware/CheckOverdueBooks.php

class CheckOverdueBooks
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $overdueCount = auth()->user()->activeBorrows()
                ->where('due_date', '<', now())
                ->count();
                
            // Add overdue alert to session
            if ($overdueCount > 0) {
                session()->flash('overdue_alert', "You have {$overdueCount} overdue book(s).");
            }
        }
        
        return $next($request);
    }
}
```

### **Overdue Block Middleware**
```php
<?php
// app/Http/Middleware/BlockOverdueUsers.php

class BlockOverdueUsers  
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $hasOverdue = auth()->user()->activeBorrows()
                ->where('due_date', '<', now())
                ->exists();
                
            if ($hasOverdue) {
                return redirect()->route('borrows.index')
                    ->with('error', 'Please return overdue books before borrowing more.');
            }
        }
        
        return $next($request);
    }
}
```

### **Ownership Verification Middleware**
```php
<?php  
// app/Http/Middleware/VerifyOwnership.php

class VerifyOwnership
{
    public function handle(Request $request, Closure $next, string $model)
    {
        $resourceId = $request->route($model);
        $modelClass = match($model) {
            'borrow' => \App\Models\Borrow::class,
            'reservation' => \App\Models\Reservation::class,
            'fine' => \App\Models\Fine::class,
            default => throw new \InvalidArgumentException("Unknown model: {$model}")
        };
        
        $resource = $modelClass::findOrFail($resourceId);
        
        if ($resource->user_id !== auth()->id()) {
            abort(403, 'You do not have permission to access this resource.');
        }
        
        return $next($request);
    }
}
```

This architecture ensures that each user flow is logically organized, properly secured with appropriate middleware, and seamlessly connected through the UI components. The separation of concerns makes the codebase maintainable while providing a smooth user experience across all library management workflows.
