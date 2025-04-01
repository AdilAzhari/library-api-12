<template>
    <transition name="fade">
        <div v-if="message" class="fixed top-4 right-4 z-50 max-w-sm w-full">
            <div :class="[
        'p-4 rounded-lg shadow-lg border relative',
        type === 'success'
          ? 'bg-green-100 border-green-400 text-green-700'
          : 'bg-red-100 border-red-400 text-red-700'
      ]">
                <span class="block sm:inline">{{ message }}</span>
                <button
                    @click="close"
                    class="absolute top-0 bottom-0 right-0 px-4 py-3 focus:outline-none"
                >
                    <svg :class="[
            'h-6 w-6',
            type === 'success' ? 'text-green-500' : 'text-red-500'
          ]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </transition>
</template>

<script setup>
import { onMounted, ref } from 'vue';

const props = defineProps({
    type: {
        type: String,
        required: true,
        validator: (value) => ['success', 'error'].includes(value)
    },
    message: {
        type: String,
        default: null
    },
    duration: {
        type: Number,
        default: 5000 // 5 seconds
    }
});

const emit = defineEmits(['close']);

const show = ref(true);
let timeoutId = null;

const close = () => {
    clearTimeout(timeoutId);
    emit('close');
};

onMounted(() => {
    if (props.duration > 0) {
        timeoutId = setTimeout(() => {
            close();
        }, props.duration);
    }
});
</script>

<style>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
