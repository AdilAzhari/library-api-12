<template>
    <div v-if="links.length > 3" class="flex items-center justify-between border-t border-gray-200 px-4 py-3 sm:px-6">
        <!-- Mobile -->
        <div class="flex flex-1 justify-between sm:hidden">
            <button
                :disabled="!links[0].url"
                @click="$emit('navigate', links[0].url)"
                class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50"
            >
                &laquo; Previous
            </button>
            <button
                :disabled="!links[links.length - 1].url"
                @click="$emit('navigate', links[links.length - 1].url)"
                class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50"
            >
                Next &raquo;
            </button>
        </div>

        <!-- Desktop -->
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Showing <span class="font-medium">{{ from }}</span> to
                    <span class="font-medium">{{ to }}</span> of
                    <span class="font-medium">{{ total }}</span> results
                </p>
            </div>
            <div>
                <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                    <button
                        v-for="(link, index) in links"
                        :key="index"
                        @click="$emit('navigate', link.url)"
                        class="relative inline-flex items-center px-4 py-2 text-sm font-semibold focus:z-20"
                        :class="{
                            'z-10 bg-[#2c3e50] text-white': link.active,
                            'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50': !link.active && link.url,
                            'text-gray-400 cursor-not-allowed': !link.url,
                            'rounded-l-md': index === 0,
                            'rounded-r-md': index === links.length - 1
                        }"
                        :disabled="!link.url"
                    >
                        <template v-if="index === 0">&laquo; Previous</template>
                        <template v-else-if="index === links.length - 1">Next &raquo;</template>
                        <template v-else>{{ link.label }}</template>
                    </button>
                </nav>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    links: {
        type: Array,
        required: true
    },
    from: {
        type: Number,
        required: true
    },
    to: {
        type: Number,
        required: true
    },
    total: {
        type: Number,
        required: true
    }
});
defineEmits(['navigate']);
</script>
