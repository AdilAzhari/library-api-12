<template>
    <button
        :class="[
      'inline-flex items-center justify-center rounded-xl px-6 py-4 text-base font-medium text-white shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200',
      'disabled:opacity-70 disabled:cursor-not-allowed',
      variantClasses,
      loading ? 'opacity-80' : ''
    ]"
        :disabled="disabled || loading"
        @click="$emit('click')"
    >
    <span v-if="loading" class="mr-2">
      <Spinner class="h-5 w-5 text-white" />
    </span>
        <slot name="icon" v-if="!loading" />
        <span class="ml-2">
      <slot />
    </span>
        <span v-if="$slots.tooltip" class="tooltip">
      <slot name="tooltip" />
    </span>
    </button>
</template>

<script setup>
import { computed } from 'vue';
import Spinner from '@/Components/Spinner.vue';

const props = defineProps({
    variant: {
        type: String,
        default: 'primary',
        validator: (val) => ['primary', 'reserve', 'borrow', 'outline'].includes(val)
    },
    disabled: Boolean,
    loading: Boolean
});

const variantClasses = computed(() => {
    const variants = {
        primary: 'bg-[#2c3e50] hover:bg-[#34495e] focus:ring-[#2c3e50]',
        reserve: 'bg-gradient-to-r from-[#2c3e50] to-[#34495e] hover:from-[#34495e] hover:to-[#2c3e50] focus:ring-[#2c3e50]',
        borrow: 'bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 focus:ring-amber-500',
        outline: 'bg-white text-[#2c3e50] border border-[#e8e3d5] hover:bg-[#f9f7f2] focus:ring-[#2c3e50]'
    };
    return variants[props.variant];
});

defineEmits(['click']);
</script>

<style scoped>
.tooltip {
    @apply absolute hidden bg-gray-800 text-white text-xs rounded py-1 px-2 bottom-full mb-2;
    min-width: 120px;
    left: 50%;
    transform: translateX(-50%);
}

button:hover .tooltip {
    @apply block;
}

button:disabled:hover .tooltip {
    @apply hidden;
}
</style>
