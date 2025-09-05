<template>
    <div class="min-h-screen bg-[#f9f7f2]">
        <Header/>

        <main class="container mx-auto px-4 py-8 max-w-7xl">
            <!-- Hero Section -->
            <div class="mb-12 bg-[#e8e3d5] rounded-xl p-8 flex flex-col md:flex-row items-center justify-between">
                <div class="md:w-1/2 mb-6 md:mb-0">
                    <h2 class="text-3xl font-serif font-bold text-[#2c3e50] mb-3">Discover Your Next Read</h2>
                    <p class="text-lg text-gray-700 mb-6">Browse our collection of carefully curated books</p>
                    
                    <!-- Enhanced Search Box -->
                    <div class="relative w-full max-w-md">
                        <div class="relative">
                            <input
                                type="text"
                                placeholder="Search by title, author, ISBN or genre..."
                                class="w-full pl-12 pr-24 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-300 focus:border-amber-300 transition-all duration-200"
                                v-model="filters.search"
                                @input="handleSearchInput"
                                @focus="showSearchSuggestions = true"
                                @blur="hideSearchSuggestions"
                                @keydown.escape="showSearchSuggestions = false"
                                @keydown.enter="handleSearchSubmit"
                            />
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500 absolute left-3 top-3.5"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            
                            <!-- Clear search button -->
                            <button
                                v-if="filters.search"
                                @click="clearSearch"
                                class="absolute right-3 top-3.5 text-gray-400 hover:text-gray-600 transition-colors"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        
                        <!-- Search Results Summary -->
                        <div v-if="filters.search && !isLoading" class="mt-2 text-sm text-gray-600">
                            Found {{ props.pagination?.total || 0 }} books matching "{{ filters.search }}"
                        </div>
                        
                        <!-- Search Loading Indicator -->
                        <div v-if="isSearching" class="absolute right-12 top-3.5">
                            <div class="animate-spin h-5 w-5 border-2 border-amber-500 border-t-transparent rounded-full"></div>
                        </div>
                        
                        <!-- Quick Search Suggestions (appears when focused and no search term) -->
                        <div
                            v-if="showSearchSuggestions && !filters.search"
                            class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-10 p-3"
                        >
                            <div class="text-sm text-gray-700 mb-2 font-medium">Popular searches:</div>
                            <div class="flex flex-wrap gap-2">
                                <button
                                    v-for="suggestion in popularSearches"
                                    :key="suggestion"
                                    @mousedown.prevent="searchForSuggestion(suggestion)"
                                    class="px-3 py-1 text-sm bg-gray-100 hover:bg-amber-100 rounded-full transition-colors"
                                >
                                    {{ suggestion }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="md:w-1/2 flex justify-center">
                    <img src="/images/library-hero.png" alt="Library illustration" class="h-64 object-contain">
                </div>
            </div>

            <!-- Enhanced Filter Bar -->
            <div class="mb-10 bg-white rounded-xl shadow-sm p-6 border border-[#e8e3d5]">
                <!-- Basic Filters Row -->
                <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center mb-4">
                    <div class="flex flex-col sm:flex-row w-full gap-4">
                        <!-- Genre Filter -->
                        <div class="relative flex-grow">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Genre</label>
                            <select
                                v-model="filters.genre"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-300 focus:border-amber-300 appearance-none bg-white"
                                @change="applyFilters"
                            >
                                <option value="">All Genres</option>
                                <option v-for="genre in genres" :key="genre.id" :value="genre.id">{{ genre.name }}</option>
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div class="relative flex-grow">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Availability</label>
                            <select
                                v-model="filters.status"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-300 focus:border-amber-300 appearance-none bg-white"
                                @change="applyFilters"
                            >
                                <option value="">All Statuses</option>
                                <option v-for="status in statusFacets" :key="status.value" :value="status.value">
                                    {{ formatStatusLabel(status.value) }} ({{ status.count }})
                                </option>
                            </select>
                        </div>

                        <!-- Sort Options -->
                        <div class="relative flex-grow">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sort by</label>
                            <select
                                v-model="filters.sort"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-300 focus:border-amber-300 appearance-none bg-white"
                                @change="applyFilters"
                            >
                                <option value="">Default</option>
                                <option value="title_asc">Title (A-Z)</option>
                                <option value="title_desc">Title (Z-A)</option>
                                <option value="author_asc">Author (A-Z)</option>
                                <option value="author_desc">Author (Z-A)</option>
                                <option value="year_desc">Publication Year (Newest)</option>
                                <option value="year_asc">Publication Year (Oldest)</option>
                                <option value="rating_desc">Rating (Highest)</option>
                                <option value="rating_asc">Rating (Lowest)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Advanced Filters Toggle -->
                <div class="border-t pt-4">
                    <button
                        @click="showAdvancedFilters = !showAdvancedFilters"
                        class="flex items-center gap-2 text-sm text-gray-600 hover:text-gray-800 transition-colors mb-4"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform" :class="{ 'rotate-180': showAdvancedFilters }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                        Advanced Filters
                    </button>

                    <!-- Advanced Filters Section -->
                    <div v-if="showAdvancedFilters" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Publication Year Range -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Publication Year</label>
                                <div class="flex gap-2">
                                    <input
                                        type="number"
                                        placeholder="From"
                                        v-model="filters.year_from"
                                        @input="handleAdvancedFilterChange"
                                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-300 focus:border-amber-300"
                                        min="1800"
                                        max="2024"
                                    />
                                    <input
                                        type="number"
                                        placeholder="To"
                                        v-model="filters.year_to"
                                        @input="handleAdvancedFilterChange"
                                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-300 focus:border-amber-300"
                                        min="1800"
                                        max="2024"
                                    />
                                </div>
                            </div>

                            <!-- Rating Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Rating</label>
                                <select
                                    v-model="filters.min_rating"
                                    @change="handleAdvancedFilterChange"
                                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-300 focus:border-amber-300"
                                >
                                    <option value="">Any Rating</option>
                                    <option value="1">1+ Stars</option>
                                    <option value="2">2+ Stars</option>
                                    <option value="3">3+ Stars</option>
                                    <option value="4">4+ Stars</option>
                                    <option value="5">5 Stars</option>
                                </select>
                            </div>

                            <!-- Author Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Author</label>
                                <input
                                    type="text"
                                    placeholder="Search by author..."
                                    v-model="filters.author"
                                    @input="handleAdvancedFilterChange"
                                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-300 focus:border-amber-300"
                                />
                            </div>

                            <!-- ISBN Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">ISBN</label>
                                <input
                                    type="text"
                                    placeholder="Enter ISBN..."
                                    v-model="filters.isbn"
                                    @input="handleAdvancedFilterChange"
                                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-300 focus:border-amber-300"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons and Results Summary -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pt-4 border-t">
                    <div class="text-sm text-gray-600">
                        <span v-if="!isLoading">
                            Showing {{ props.pagination?.from || 0 }}-{{ props.pagination?.to || 0 }} of {{ props.pagination?.total || 0 }} books
                            <span v-if="hasActiveFilters" class="text-amber-600 font-medium ml-1">(filtered)</span>
                        </span>
                        <span v-else>Searching...</span>
                    </div>
                    
                    <div class="flex gap-2">
                        <button
                            v-if="hasActiveFilters"
                            @click="resetFilters"
                            class="flex items-center gap-2 px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Clear Filters
                        </button>
                        
                        <button
                            @click="saveSearchPreferences"
                            v-if="hasActiveFilters"
                            class="flex items-center gap-2 px-4 py-2 text-sm bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition-colors"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                            </svg>
                            Save Search
                        </button>
                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="isLoading" class="flex justify-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-amber-500"></div>
            </div>

            <!-- Book Grid -->
            <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                <div
                    v-for="book in books"
                    :key="book.id"
                    class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 flex flex-col border border-[#e8e3d5]"
                >
                    <!-- Book Spine Effect -->
                    <div class="h-2 bg-gradient-to-r from-[#8b5a2b] to-[#d4a76a]"></div>

                    <div class="relative">
                        <div class="h-48 overflow-hidden">
                            <img
                                :src="book.cover_image_url"
                                :alt="book.title"
                                class="w-full h-full object-cover transition-transform duration-500 hover:scale-105"
                            />
                            <div v-if="!book.cover_image_url"
                                 class="w-full h-full bg-gray-100 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                        </div>
                        <div class="absolute top-3 right-3 flex flex-col gap-1">
              <span v-if="book.status === 'available'"
                    class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-1 rounded-full flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 13l4 4L19 7"/>
                </svg>
                Available
              </span>
                            <span v-else-if="book.status === 'borrowed'"
                                  class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-1 rounded-full flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"/>
                </svg>
                On Loan
              </span>
                            <span v-else-if="book.status === 'reserved'"
                                  class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-1 rounded-full flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Reserved
              </span>
                        </div>
                    </div>

                    <div class="p-5 flex-grow">
                        <div class="flex justify-between items-start mb-2">
                            <h2 class="text-lg font-serif font-bold text-gray-900 line-clamp-2">
                                <span v-if="book.highlight?.title" v-html="book.highlight.title"></span>
                                <span v-else>{{ book.title }}</span>
                            </h2>
                        </div>
                        <p class="text-[#8b5a2b] font-medium">
                            <span v-if="book.highlight?.author" v-html="book.highlight.author"></span>
                            <span v-else>{{ book.author }}</span>
                        </p>
                        <p class="text-xs text-gray-500 mt-1">Published {{ book.publication_year }}</p>

                        <div class="mt-3 flex items-center">
                            <div class="flex items-center mr-2">
                                <svg v-for="i in 5" :key="i" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                     :class="i <= (book.average_rating || 0) ? 'text-amber-400 fill-current' : 'text-gray-300'"
                                     viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </div>
                            <span class="text-xs text-gray-500">({{ book.reviews_count || 0 }} reviews)</span>
                        </div>

                        <p class="text-sm text-gray-600 mt-3 line-clamp-3">
                            {{ book.description || 'No description available.' }}
                        </p>
                    </div>

                    <div class="p-5 pt-0 border-t border-gray-100 mt-auto">
                        <div class="flex justify-between items-center">
                            <Link
                                :href="`/books/${book.id}`"
                                class="text-[#2c3e50] hover:text-amber-600 transition-colors duration-300 font-medium text-sm flex items-center"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Details
                            </Link>

                            <div class="flex space-x-2">
                                <!-- Add to Reading List Button -->
                                <button
                                    @click="showAddToListModal(book)"
                                    class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-1 rounded text-sm font-medium transition flex items-center"
                                    title="Add to Reading List"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fillRule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clipRule="evenodd"/>
                                    </svg>
                                </button>
                                
                                <!-- Primary Actions -->
                                <button
                                    v-if="book.status === 'available' && book.has_active_reservation_for_user"
                                    @click="borrowBook(book.id)"
                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm font-medium transition flex items-center"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                    </svg>
                                    Borrow
                                </button>
                                <button
                                    v-else-if="book.status === 'available' && !book.isBorrowed"
                                    @click="reserveBook(book.id)"
                                    class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1 rounded text-sm font-medium transition flex items-center"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Reserve
                                </button>
                                <button
                                    v-else-if="book.status === 'borrowed' && !book.isBorrowed"
                                    @click="reserveBook(book.id)"
                                    class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1 rounded text-sm font-medium transition flex items-center"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Reserve
                                </button>
                                <span v-else-if="book.status === 'reserved' && !book.has_active_reservation_for_user"
                                      class="text-xs text-gray-500 flex items-center px-2 py-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Reserved
                                </span>
                                <span v-if="book.isBorrowed" class="text-xs text-green-600 flex items-center px-2 py-1 bg-green-50 rounded">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Borrowed
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="!isLoading" class="mt-12 flex justify-center">
                <nav class="inline-flex rounded-md shadow-sm">
                    <button
                        @click="goToPage(currentPage - 1)"
                        :disabled="currentPage === 1"
                        :class="{
              'cursor-not-allowed opacity-50': currentPage === 1,
              'hover:bg-gray-50': currentPage > 1
            }"
                        class="px-4 py-2 rounded-l-md border border-gray-300 bg-white text-gray-700 flex items-center"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Prev
                    </button>
                    <template v-for="page in visiblePages" :key="page">
                        <button
                            v-if="page === '...'"
                            class="px-4 py-2 border-t border-b border-gray-300 bg-white text-gray-700"
                            disabled
                        >
                            ...
                        </button>
                        <button
                            v-else
                            @click="goToPage(page)"
                            :class="{
                'bg-[#2c3e50] text-white': currentPage === page,
                'text-gray-700 hover:bg-gray-50': currentPage !== page
              }"
                            class="px-4 py-2 border-t border-b border-gray-300 bg-white"
                        >
                            {{ page }}
                        </button>
                    </template>
                    <button
                        @click="goToPage(currentPage + 1)"
                        :disabled="currentPage === totalPages"
                        :class="{
              'cursor-not-allowed opacity-50': currentPage === totalPages,
              'hover:bg-gray-50': currentPage < totalPages
            }"
                        class="px-4 py-2 rounded-r-md border border-gray-300 bg-white text-gray-700 flex items-center"
                    >
                        Next
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </nav>
            </div>
        </main>

        <!-- Footer -->
        <Footer :year="new Date().getFullYear()" />
        
        <!-- Add to Reading List Modal -->
        <div v-if="showListModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                        <svg class="h-6 w-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fillRule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clipRule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 text-center mt-4">Add to Reading List</h3>
                    <p class="text-sm text-gray-500 text-center mt-2">
                        Add "{{ selectedBook?.title }}" to one of your reading lists
                    </p>
                    
                    <div class="mt-6">
                        <div v-if="userReadingLists.length > 0" class="space-y-2 max-h-48 overflow-y-auto">
                            <button v-for="list in userReadingLists" :key="list.id" 
                                    @click="addBookToList(selectedBook.id, list.id)"
                                    class="w-full text-left px-3 py-2 rounded-lg border border-gray-200 hover:border-blue-500 hover:bg-blue-50 transition-colors">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" 
                                         :class="`bg-${list.color_theme || 'blue'}-100`">
                                        <svg class="w-4 h-4" :class="`text-${list.color_theme || 'blue'}-600`" fill="currentColor" viewBox="0 0 20 20">
                                            <path fillRule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clipRule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ list.name }}</p>
                                        <p class="text-sm text-gray-500">{{ list.books_count || 0 }} books</p>
                                    </div>
                                </div>
                            </button>
                        </div>
                        <div v-else class="text-center py-6 text-gray-500">
                            <p class="text-sm">You don't have any reading lists yet.</p>
                            <Link href="/reading-lists/create" class="text-blue-600 hover:text-blue-800 text-sm font-medium mt-2 inline-block">
                                Create your first reading list
                            </Link>
                        </div>
                    </div>
                    
                    <div class="flex justify-end mt-6">
                        <button @click="showListModal = false"
                                class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {computed, reactive, ref, watch} from 'vue';
import {Link, router} from '@inertiajs/vue3';
import Header from '@/Components/AppHeader.vue';
import Footer from '@/Components/AppFooter.vue';

const props = defineProps({
    books: {
        type: Array,
        default: () => []
    },
    genres: {
        type: Array,
        default: () => []
    },
    facets: {
        type: Array,
        default: () => []
    },
    pagination: {
        type: Object,
        default: () => ({
            current_page: 1,
            last_page: 1,
            per_page: 12,
            total: 0
        })
    },
    filters: {
        type: Object,
        default: () => ({
            search: '',
            genre: '',
            status: '',
            sort: ''
        })
    }
});

// Reactive state
const isLoading = ref(false);
const currentPage = ref(props.pagination.current_page);
const totalPages = computed(() => props.pagination.last_page);
const searchTimeout = ref(null);
const showListModal = ref(false);
const selectedBook = ref(null);
const userReadingLists = ref([]);

// Enhanced filter state
const filters = reactive({
    search: props.filters.search || '',
    genre: props.filters.genre || '',
    status: props.filters.status || '',
    sort: props.filters.sort || '',
    year_from: props.filters.year_from || '',
    year_to: props.filters.year_to || '',
    min_rating: props.filters.min_rating || '',
    author: props.filters.author || '',
    isbn: props.filters.isbn || ''
});

// Enhanced UI state
const showAdvancedFilters = ref(false);
const showSearchSuggestions = ref(false);
const isSearching = ref(false);
const advancedFilterTimeout = ref(null);

// Popular search terms (could be fetched from backend)
const popularSearches = ref([
    'fiction', 'mystery', 'romance', 'science fiction', 
    'biography', 'history', 'philosophy', 'programming'
]);

// Computed properties
const hasFilters = computed(() => {
    return filters.search || filters.genre || filters.status || filters.sort;
});

const hasActiveFilters = computed(() => {
    return filters.search || filters.genre || filters.status || filters.sort ||
           filters.year_from || filters.year_to || filters.min_rating ||
           filters.author || filters.isbn;
});

const statusFacets = computed(() => {
    const statusFacet = props.facets?.find(f => f.field === 'status');
    return statusFacet?.values || [];
});
const visiblePages = computed(() => {
    const range = 2;
    const pages = [];
    pages.push(1);

    if (currentPage.value - range > 2) {
        pages.push('...');
    }

    for (let i = Math.max(2, currentPage.value - range); i <= Math.min(totalPages.value - 1, currentPage.value + range); i++) {
        pages.push(i);
    }

    if (currentPage.value + range < totalPages.value - 1) {
        pages.push('...');
    }

    if (totalPages.value > 1) {
        pages.push(totalPages.value);
    }

    return pages;
});

// Methods
const borrowBook = (bookId) => {
    router.post(`/books/${bookId}/borrow`, {}, {
        onSuccess: () => {
            router.reload({only: ['books']});
        }
    });
};

const reserveBook = (bookId) => {
    router.post(`/reservations/books/${bookId}/reserve`, {}, {
        onSuccess: () => {
            router.reload({only: ['books']});
        },
        onError: (errors) => {
            const errorMsg = errors.message || 'Failed to reserve the book. Please try again.';
            alert(errorMsg);
        }
    });
};

const showAddToListModal = async (book) => {
    selectedBook.value = book;
    showListModal.value = true;
    
    // Fetch user's reading lists
    try {
        const response = await fetch('/api/reading-lists/user');
        const data = await response.json();
        userReadingLists.value = data.reading_lists || [];
    } catch (error) {
        console.error('Failed to fetch reading lists:', error);
        userReadingLists.value = [];
    }
};

const addBookToList = (bookId, listId) => {
    router.post(`/reading-lists/${listId}/books/${bookId}`, {}, {
        onSuccess: () => {
            showListModal.value = false;
            selectedBook.value = null;
            alert('Book added to reading list successfully!');
        },
        onError: (errors) => {
            const errorMsg = errors.message || 'Failed to add book to reading list.';
            alert(errorMsg);
        }
    });
};


const resetFilters = () => {
    filters.search = '';
    filters.genre = '';
    filters.status = '';
    filters.sort = '';
    filters.year_from = '';
    filters.year_to = '';
    filters.min_rating = '';
    filters.author = '';
    filters.isbn = '';
    showAdvancedFilters.value = false;
    applyFilters();
};

const handleSearchInput = () => {
    isSearching.value = true;
    clearTimeout(searchTimeout.value);
    searchTimeout.value = setTimeout(() => {
        const result = applyFilters();
        if (result && typeof result.finally === 'function') {
            result.finally(() => {
                isSearching.value = false;
            });
        } else {
            isSearching.value = false;
        }
    }, 500);
};

const handleAdvancedFilterChange = () => {
    clearTimeout(advancedFilterTimeout.value);
    advancedFilterTimeout.value = setTimeout(() => {
        applyFilters();
    }, 800);
};

const handleSearchSubmit = () => {
    clearTimeout(searchTimeout.value);
    isSearching.value = true;
    const result = applyFilters();
    if (result && typeof result.finally === 'function') {
        result.finally(() => {
            isSearching.value = false;
        });
    } else {
        isSearching.value = false;
    }
};

const clearSearch = () => {
    filters.search = '';
    showSearchSuggestions.value = false;
    applyFilters();
};

const hideSearchSuggestions = () => {
    setTimeout(() => {
        showSearchSuggestions.value = false;
    }, 150);
};

const searchForSuggestion = (suggestion) => {
    filters.search = suggestion;
    showSearchSuggestions.value = false;
    handleSearchSubmit();
};

const formatStatusLabel = (status) => {
    switch(status) {
        case 'available':
            return 'Available';
        case 'borrowed':
            return 'Borrowed';
        case 'reserved':
            return 'Reserved';
        default:
            return status?.charAt(0)?.toUpperCase() + status?.slice(1) || 'Unknown';
    }
};

const saveSearchPreferences = () => {
    // Save current search preferences to localStorage
    const preferences = {
        genre: filters.genre,
        sort: filters.sort,
        year_from: filters.year_from,
        year_to: filters.year_to,
        min_rating: filters.min_rating
    };
    
    localStorage.setItem('book_search_preferences', JSON.stringify(preferences));
    
    // Show success message (you might want to use a proper notification system)
    alert('Search preferences saved successfully!');
};

// Enhanced apply filters method
const applyFilters = () => {
    isLoading.value = true;
    
    // Build clean filter object
    const filterParams = {};
    Object.keys(filters).forEach(key => {
        if (filters[key]) {
            filterParams[key] = filters[key];
        }
    });
    
    return router.get(route('books.index'), {
        ...filterParams,
        page: 1 // Reset to first page when applying new filters
    }, {
        preserveState: true,
        replace: true,
        onFinish: () => {
            isLoading.value = false;
        },
        onError: () => {
            isLoading.value = false;
            isSearching.value = false;
        }
    });
};

const goToPage = (page) => {
    if (page < 1 || page > totalPages.value || page === currentPage.value) return;

    isLoading.value = true;
    
    // Build clean filter object for pagination
    const filterParams = {};
    Object.keys(filters).forEach(key => {
        if (filters[key]) {
            filterParams[key] = filters[key];
        }
    });
    
    router.get(route('books.index'), {
        ...filterParams,
        page
    }, {
        preserveState: true,
        replace: true,
        onFinish: () => {
            isLoading.value = false;
        }
    });
};

// Watch for route changes to update current page
watch(() => props.pagination.current_page, (newVal) => {
    currentPage.value = newVal;
});

// Watch other filters and apply immediately
watch(() => [filters.genre, filters.status, filters.sort], () => {
    applyFilters();
}, {deep: true});
</script>

<style>
/* Smooth transitions for interactive elements */
button, a {
    transition: all 0.2s ease;
}

/* Book card hover effect */
.book-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Highlight styling */
mark {
    background-color: #FEF08A;
    padding: 0 0.2em;
    border-radius: 0.2em;
}
</style>
