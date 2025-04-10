<template>
    <div class="min-h-screen bg-[#f9f7f2]">
        <Header />

        <main class="container mx-auto px-4 py-8 max-w-4xl">
            <div class="bg-white rounded-xl shadow-md overflow-hidden p-8">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-serif font-bold text-[#2c3e50] mb-4">Terms of Service</h1>
                    <div class="w-24 h-1 bg-amber-400 mx-auto"></div>
                    <p class="text-gray-600 mt-4">Last updated: January 1, 2023</p>
                </div>

                <div class="prose max-w-none">
                    <p class="text-gray-700">Please read these Terms of Service ("Terms", "Terms of Service") carefully before using the Academia Inspired website (the "Service") operated by Academia Inspired ("us", "we", or "our").</p>

                    <h2 class="text-xl font-serif text-[#2c3e50] mt-6">1. Accounts</h2>
                    <p class="text-gray-700 mt-2">When you create an account with us, you must provide us information that is accurate, complete, and current at all times. Failure to do so constitutes a breach of the Terms, which may result in immediate termination of your account on our Service.</p>

                    <h2 class="text-xl font-serif text-[#2c3e50] mt-6">2. Borrowing Policy</h2>
                    <p class="text-gray-700 mt-2">Books may be borrowed for a period of 14 days, with the option to renew for an additional 14 days if no other member has reserved the book. Late returns will incur fines of $0.50 per day per book.</p>

                    <h2 class="text-xl font-serif text-[#2c3e50] mt-6">3. Intellectual Property</h2>
                    <p class="text-gray-700 mt-2">The Service and its original content, features, and functionality are and will remain the exclusive property of Academia Inspired and its licensors. The Service is protected by copyright, trademark, and other laws of both the United States and foreign countries.</p>

                    <h2 class="text-xl font-serif text-[#2c3e50] mt-6">4. Links To Other Web Sites</h2>
                    <p class="text-gray-700 mt-2">Our Service may contain links to third-party web sites or services that are not owned or controlled by Academia Inspired. We have no control over, and assume no responsibility for the content, privacy policies, or practices of any third party web sites or services.</p>

                    <h2 class="text-xl font-serif text-[#2c3e50] mt-6">5. Termination</h2>
                    <p class="text-gray-700 mt-2">We may terminate or suspend your account immediately, without prior notice or liability, for any reason whatsoever, including without limitation if you breach the Terms.</p>

                    <h2 class="text-xl font-serif text-[#2c3e50] mt-6">6. Limitation Of Liability</h2>
                    <p class="text-gray-700 mt-2">In no event shall Academia Inspired, nor its directors, employees, partners, agents, suppliers, or affiliates, be liable for any indirect, incidental, special, consequential or punitive damages, including without limitation, loss of profits, data, use, goodwill, or other intangible losses, resulting from your access to or use of or inability to access or use the Service.</p>

                    <h2 class="text-xl font-serif text-[#2c3e50] mt-6">7. Governing Law</h2>
                    <p class="text-gray-700 mt-2">These Terms shall be governed and construed in accordance with the laws of the State of California, United States, without regard to its conflict of law provisions.</p>

                    <h2 class="text-xl font-serif text-[#2c3e50] mt-6">8. Changes</h2>
                    <p class="text-gray-700 mt-2">We reserve the right, at our sole discretion, to modify or replace these Terms at any time. By continuing to access or use our Service after those revisions become effective, you agree to be bound by the revised terms.</p>

                    <div class="bg-[#e8e3d5] p-4 rounded-lg mt-8">
                        <h3 class="font-serif font-bold text-[#2c3e50]">Contact Us</h3>
                        <p class="text-gray-700 mt-2 text-sm">If you have any questions about these Terms, please contact us at <a href="mailto:terms@academiainspired.com" class="text-amber-600 hover:underline">terms@academiainspired.com</a>.</p>
                    </div>
                </div>
            </div>
        </main>

        <AppFooter />
    </div>
</template>

<script setup>
import Header from '@/Components/AppHeader.vue';
import AppFooter from '@/Components/AppFooter.vue';
</script>
