<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\ReadingListCreateDTO;
use App\Models\Book;
use App\Models\ReadingList;
use App\Services\ReadingListService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

final class ReadingListController extends Controller
{
    public function __construct(
        protected ReadingListService $readingListService
    ) {}

    /**
     * Display user's reading lists
     */
    public function index(Request $request): Response|RedirectResponse
    {
        try {
            $user = Auth::user();
            $view = $request->input('view', 'my'); // my, public, featured

            $readingLists = match ($view) {
                'public' => $this->readingListService->getPublicLists($request->input('per_page', 12)),
                'featured' => $this->readingListService->getFeaturedLists($request->input('per_page', 12)),
                default => $this->readingListService->getUserLists($user)
            };

            $stats = $view === 'my' ? [
                'total_books' => $user->readingLists()->withCount('books')->get()->sum('books_count'),
                'total_lists' => $user->readingLists()->count(),
                'public_lists' => $user->readingLists()->where('is_public', true)->count(),
            ] : null;

            return Inertia::render('ReadingLists/Index', [
                'readingLists' => $readingLists,
                'stats' => $stats,
                'view' => $view,
                'canCreateList' => $user->readingLists()->count() < config('library.max_reading_lists_per_user', 10),
            ]);

        } catch (Exception $e) {
            Log::error('Failed to load reading lists: '.$e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Failed to load reading lists. Please try again.');
        }
    }

    /**
     * Show reading list creation form
     */
    public function create(): Response
    {
        return Inertia::render('ReadingLists/Create', [
            'colorThemes' => config('library.reading_list_themes', [
                'blue', 'green', 'purple', 'orange', 'red', 'teal',
            ]),
            'icons' => config('library.reading_list_icons', [
                'book', 'star', 'heart', 'bookmark', 'list', 'folder',
            ]),
        ]);
    }

    /**
     * Store a new reading list
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'is_public' => 'boolean',
                'color_theme' => 'nullable|string|in:blue,green,purple,orange,red,teal',
                'icon' => 'nullable|string|in:book,star,heart,bookmark,list,folder',
            ]);

            $dto = new ReadingListCreateDTO(
                user_id: Auth::id(),
                name: $validatedData['name'],
                description: $validatedData['description'] ?? null,
                is_public: $validatedData['is_public'] ?? false,
                color: $validatedData['color_theme'] ?? '#3B82F6',
                icon: $validatedData['icon'] ?? 'book-open'
            );

            $readingList = $this->readingListService->create($dto);

            return redirect()
                ->route('reading-lists.show', $readingList)
                ->with('success', 'Reading list created successfully!');

        } catch (Exception $e) {
            Log::error('Failed to create reading list: '.$e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create reading list. Please try again.');
        }
    }

    /**
     * Show reading list details
     */
    public function show(ReadingList $readingList): Response|RedirectResponse
    {
        try {
            if (! $readingList->is_public) {
                Gate::authorize('view', $readingList);
            }

            $books = $readingList->books()
                ->withPivot(['added_at', 'notes', 'priority', 'order'])
                ->orderByPivot('order')
                ->with(['genre', 'activeBorrow', 'activeReservation'])
                ->paginate(20);

            $stats = [
                'total_books' => $readingList->books()->count(),
                'available_books' => $readingList->books()->available()->count(),
                'genres_breakdown' => $readingList->books()
                    ->with('genre')
                    ->get()
                    ->groupBy('genre.name')
                    ->map->count(),
            ];

            return Inertia::render('ReadingLists/Show', [
                'readingList' => $readingList->load('user'),
                'books' => $books,
                'stats' => $stats,
                'canEdit' => Gate::allows('update', $readingList),
                'canAddBooks' => Gate::allows('addBook', $readingList),
            ]);

        } catch (Exception $e) {
            Log::error('Failed to load reading list: '.$e->getMessage());

            return redirect()
                ->route('reading-lists.index')
                ->with('error', 'Failed to load reading list. Please try again.');
        }
    }

    /**
     * Show edit form
     */
    public function edit(ReadingList $readingList): Response|RedirectResponse
    {
        try {
            Gate::authorize('update', $readingList);

            return Inertia::render('ReadingLists/Edit', [
                'readingList' => $readingList,
                'colorThemes' => config('library.reading_list_themes', [
                    'blue', 'green', 'purple', 'orange', 'red', 'teal',
                ]),
                'icons' => config('library.reading_list_icons', [
                    'book', 'star', 'heart', 'bookmark', 'list', 'folder',
                ]),
            ]);

        } catch (Exception) {
            return redirect()
                ->route('reading-lists.index')
                ->with('error', 'You do not have permission to edit this reading list.');
        }
    }

    /**
     * Update reading list
     */
    public function update(Request $request, ReadingList $readingList): RedirectResponse
    {
        try {
            Gate::authorize('update', $readingList);

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'is_public' => 'boolean',
                'color_theme' => 'nullable|string|in:blue,green,purple,orange,red,teal',
                'icon' => 'nullable|string|in:book,star,heart,bookmark,list,folder',
            ]);

            $this->readingListService->updateReadingList($readingList, $validatedData);

            return redirect()
                ->route('reading-lists.show', $readingList)
                ->with('success', 'Reading list updated successfully!');

        } catch (Exception $e) {
            Log::error('Failed to update reading list: '.$e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update reading list. Please try again.');
        }
    }

    /**
     * Delete reading list
     */
    public function destroy(ReadingList $readingList): RedirectResponse
    {
        try {
            Gate::authorize('delete', $readingList);

            // Prevent deletion of default reading list
            if ($readingList->is_default) {
                return redirect()
                    ->back()
                    ->with('error', 'Cannot delete your default reading list.');
            }

            $this->readingListService->deleteReadingList($readingList);

            return redirect()
                ->route('reading-lists.index')
                ->with('success', 'Reading list deleted successfully.');

        } catch (Exception $e) {
            Log::error('Failed to delete reading list: '.$e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Failed to delete reading list. Please try again.');
        }
    }

    /**
     * Add book to reading list
     */
    public function addBook(Request $request, ReadingList $readingList, Book $book): RedirectResponse
    {
        try {
            Gate::authorize('addBook', $readingList);

            $validatedData = $request->validate([
                'notes' => 'nullable|string|max:500',
                'priority' => 'nullable|integer|between:1,5',
            ]);

            $this->readingListService->addBookToList(
                $readingList,
                $book,
                $validatedData['notes'] ?? null,
                $validatedData['priority'] ?? 3
            );

            return redirect()
                ->back()
                ->with('success', 'Book added to reading list successfully!');

        } catch (Exception $e) {
            Log::error('Failed to add book to reading list: '.$e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Failed to add book to reading list: '.$e->getMessage());
        }
    }

    /**
     * Remove book from reading list
     */
    public function removeBook(ReadingList $readingList, Book $book): RedirectResponse
    {
        try {
            Gate::authorize('removeBook', $readingList);

            $this->readingListService->removeBookFromList($readingList, $book);

            return redirect()
                ->back()
                ->with('success', 'Book removed from reading list successfully!');

        } catch (Exception $e) {
            Log::error('Failed to remove book from reading list: '.$e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Failed to remove book from reading list. Please try again.');
        }
    }
}
