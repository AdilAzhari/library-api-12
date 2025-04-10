<template>
    <div class="min-h-screen bg-[#f9f7f2]">
        <Header />

        <main class="container mx-auto px-4 py-8 max-w-4xl">
            <div class="bg-white rounded-xl shadow-md overflow-hidden p-8">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-serif font-bold text-[#2c3e50] mb-4">Privacy Policy</h1>
                    <div class="w-24 h-1 bg-amber-400 mx-auto"></div>
                    <p class="text-gray-600 mt-4">Last updated: January 1, 2023</p>
                </div>

                <div class="prose max-w-none">
                    <p class="text-gray-700">Academia Inspired ("us", "we", or "our") operates the Academia Inspired website (the "Service"). This page informs you of our policies regarding the collection, use, and disclosure of personal data when you use our Service and the choices you have associated with that data.</p>

                    <h2 class="text-xl font-serif text-[#2c3e50] mt-6">Information Collection And Use</h2>
                    <p class="text-gray-700 mt-2">We collect several different types of information for various purposes to provide and improve our Service to you.</p>

                    <h3 class="font-medium text-[#2c3e50] mt-4">Types of Data Collected</h3>
                    <ul class="list-disc pl-5 space-y-2 text-gray-700">
                        <li><strong>Personal Data:</strong> While using our Service, we may ask you to provide us with certain personally identifiable information that can be used to contact or identify you ("Personal Data"). Personally identifiable information may include, but is not limited to: Email address, First name and last name, Phone number, Address, Cookies and Usage Data</li>
                        <li><strong>Usage Data:</strong> We may also collect information how the Service is accessed and used ("Usage Data"). This Usage Data may include information such as your computer's Internet Protocol address (e.g. IP address), browser type, browser version, the pages of our Service that you visit, the time and date of your visit, the time spent on those pages, unique device identifiers and other diagnostic data.</li>
                        <li><strong>Tracking & Cookies Data:</strong> We use cookies and similar tracking technologies to track the activity on our Service and hold certain information.</li>
                    </ul>

                    <h2 class="text-xl font-serif text-[#2c3e50] mt-6">Use of Data</h2>
                    <p class="text-gray-700 mt-2">Academia Inspired uses the collected data for various purposes:</p>
                    <ul class="list-disc pl-5 space-y-2 text-gray-700">
                        <li>To provide and maintain our Service</li>
                        <li>To notify you about changes to our Service</li>
                        <li>To allow you to participate in interactive features of our Service when you choose to do so</li>
                        <li>To provide customer support</li>
                        <li>To gather analysis or valuable information so that we can improve our Service</li>
                        <li>To monitor the usage of our Service</li>
                        <li>To detect, prevent and address technical issues</li>
                    </ul>

                    <h2 class="text-xl font-serif text-[#2c3e50] mt-6">Data Security</h2>
                    <p class="text-gray-700 mt-2">The security of your data is important to us, but remember that no method of transmission over the Internet, or method of electronic storage is 100% secure. While we strive to use commercially acceptable means to protect your Personal Data, we cannot guarantee its absolute security.</p>

                    <h2 class="text-xl font-serif text-[#2c3e50] mt-6">Changes To This Privacy Policy</h2>
                    <p class="text-gray-700 mt-2">We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page. We will let you know via email and/or a prominent notice on our Service, prior to the change becoming effective and update the "effective date" at the top of this Privacy Policy.</p>

                    <div class="bg-[#e8e3d5] p-4 rounded-lg mt-8">
                        <h3 class="font-serif font-bold text-[#2c3e50]">Contact Us</h3>
                        <p class="text-gray-700 mt-2 text-sm">If you have any questions about this Privacy Policy, please contact us at <a href="mailto:privacy@academiainspired.com" class="text-amber-600 hover:underline">privacy@academiainspired.com</a>.</p>
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
