<template>
    <!-- Flash Messages Container -->
    <div v-if="flashMessage.success || flashMessage.error" class="fixed top-4 right-4 z-50 max-w-sm w-full">
        <FlashMessage
            v-if="flashMessage.success"
            :type="'success'"
            :message="flashMessage.success"
            @close="flashMessage.success = null"
        />

        <FlashMessage
            v-if="flashMessage.error"
            :type="'error'"
            :message="flashMessage.error"
            @close="flashMessage.error = null"
        />
    </div>
</template>

<script setup lang="ts">
import { reactive } from "vue";
import FlashMessage from "../Components/FlashMessage.vue"; // Ensure this path is correct

const flashMessage = reactive({
    success: null as string | null,
    error: null as string | null,
});
</script>
