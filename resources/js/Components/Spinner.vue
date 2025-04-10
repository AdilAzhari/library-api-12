<template>
    <svg
        :class="['animate-spin', sizeClasses]"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
    >
        <circle
            class="opacity-25"
            cx="12"
            cy="12"
            r="10"
            stroke="currentColor"
            stroke-width="4"
        ></circle>
        <path
            class="opacity-75"
            fill="currentColor"
            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
        ></path>
    </svg>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    size: {
        type: String,
        default: 'md',
        validator: (val) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(val)
    }
});

const sizeClasses = computed(() => {
    const sizes = {
        xs: 'h-3 w-3',
        sm: 'h-4 w-4',
        md: 'h-5 w-5',
        lg: 'h-6 w-6',
        xl: 'h-8 w-8'
    };
    return sizes[props.size];
});
</script>
