<script setup>
defineProps({
    year: {
        type: Number,
        required: true,
        default: new Date().getFullYear(),
    }
});
</script>
<template>
    <footer class="bg-gradient-to-b from-[#1a2a3a] to-[#2c3e50] text-white pt-12 pb-6 shadow-lg">
        <div class="container mx-auto px-6">
            <!-- Main Footer Content -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 mb-8">
                <!-- Brand Column -->
                <div class="flex flex-col items-center lg:items-start">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="bg-amber-500 p-2 rounded-lg shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <h2 class="text-xl font-serif font-bold bg-clip-text text-transparent bg-gradient-to-r from-amber-400 to-amber-200">
                            Academia Inspired
                        </h2>
                    </div>
                    <p class="text-sm text-gray-300 text-center lg:text-left mb-4">
                        Your gateway to knowledge and academic excellence since {{ year }}
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-amber-400 transition-colors duration-300">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd"
                                      d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-amber-400 transition-colors duration-300">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path
                                    d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-amber-400 transition-colors duration-300">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd"
                                      d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="lg:col-span-2 grid grid-cols-2 md:grid-cols-3 gap-6">
                    <div>
                        <h3 class="text-sm font-semibold text-amber-300 uppercase tracking-wider mb-4">Explore</h3>
                        <ul class="space-y-2">
                            <li><a href="/books"
                                   class="text-gray-300 hover:text-amber-400 transition-colors duration-200 text-sm">Browse
                                Books</a></li>
                            <li><a href="/genres"
                                   class="text-gray-300 hover:text-amber-400 transition-colors duration-200 text-sm">Genres</a>
                            </li>
                            <li><a href="/new-releases"
                                   class="text-gray-300 hover:text-amber-400 transition-colors duration-200 text-sm">New
                                Releases</a></li>
                            <li><a href="/recommendations"
                                   class="text-gray-300 hover:text-amber-400 transition-colors duration-200 text-sm">Recommendations</a>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-amber-300 uppercase tracking-wider mb-4">Account</h3>
                        <ul class="space-y-2">
                            <li><a href="/profile"
                                   class="text-gray-300 hover:text-amber-400 transition-colors duration-200 text-sm">My
                                Profile</a></li>
                            <li><a href="/borrowed"
                                   class="text-gray-300 hover:text-amber-400 transition-colors duration-200 text-sm">My
                                Borrowed</a></li>
                            <li><a href="/reserved"
                                   class="text-gray-300 hover:text-amber-400 transition-colors duration-200 text-sm">My
                                Reservations</a></li>
                            <li><a href="/reviews"
                                   class="text-gray-300 hover:text-amber-400 transition-colors duration-200 text-sm">My
                                Reviews</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-amber-300 uppercase tracking-wider mb-4">Company</h3>
                        <ul class="space-y-2">
                            <li><a href="/about"
                                   class="text-gray-300 hover:text-amber-400 transition-colors duration-200 text-sm">About
                                Us</a></li>
                            <li><a href="/contact"
                                   class="text-gray-300 hover:text-amber-400 transition-colors duration-200 text-sm">Contact</a>
                            </li>
                            <li><a href="/careers"
                                   class="text-gray-300 hover:text-amber-400 transition-colors duration-200 text-sm">Careers</a>
                            </li>
                            <li><a href="/blog"
                                   class="text-gray-300 hover:text-amber-400 transition-colors duration-200 text-sm">Blog</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Newsletter -->
                <div>
                    <h3 class="text-sm font-semibold text-amber-300 uppercase tracking-wider mb-4">Stay Updated</h3>
                    <p class="text-gray-300 text-sm mb-4">Subscribe to our newsletter for the latest updates and
                        releases.</p>
                    <form class="flex flex-col sm:flex-row gap-2">
                        <input type="email" placeholder="Your email"
                               class="px-4 py-2 rounded-lg bg-gray-700 text-white text-sm focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none w-full">
                        <button type="submit"
                                class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 shadow-md hover:shadow-lg">
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>

            <!-- Bottom Footer -->
            <div class="border-t border-gray-700 pt-6 mt-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="text-xs text-gray-400 mb-4 md:mb-0">
                        &copy; {{ year }} Academia Inspired. All rights reserved.
                    </div>
                    <div class="flex flex-wrap justify-center gap-4">
                        <a href="/privacy"
                           class="text-xs text-gray-400 hover:text-amber-300 transition-colors duration-200">Privacy
                            Policy</a>
                        <a href="/terms"
                           class="text-xs text-gray-400 hover:text-amber-300 transition-colors duration-200">Terms of
                            Service</a>
                        <a href="/cookies"
                           class="text-xs text-gray-400 hover:text-amber-300 transition-colors duration-200">Cookie
                            Policy</a>
                        <a href="/sitemap"
                           class="text-xs text-gray-400 hover:text-amber-300 transition-colors duration-200">Sitemap</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</template>
