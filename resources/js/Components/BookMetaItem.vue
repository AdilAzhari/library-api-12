<template>
    <div class="flex items-center p-3 bg-[#f9f7f2] rounded-lg border border-[#e8e3d5]">
        <div :class="iconContainerClasses">
            <component :is="iconComponent" class="h-5 w-5 text-white" />
        </div>
        <div class="ml-3">
            <p class="text-xs text-gray-500">{{ label }}</p>
            <p class="font-medium">{{ value }}</p>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import {
    HashtagIcon,
    BookmarkIcon,
    ClockIcon,
    UserIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    icon: {
        type: String,
        required: true,
        validator: (val) => ['isbn', 'genre', 'year', 'author'].includes(val)
    },
    label: {
        type: String,
        required: true
    },
    value: {
        type: String,
        required: true
    }
});

const iconConfig = {
    isbn: {
        component: HashtagIcon,
        bg: 'bg-[#2c3e50]'
    },
    genre: {
        component: BookmarkIcon,
        bg: 'bg-[#8b5a2b]'
    },
    year: {
        component: ClockIcon,
        bg: 'bg-[#d4a76a]'
    },
    author: {
        component: UserIcon,
        bg: 'bg-[#34495e]'
    }
};

const { component: iconComponent, bg } = iconConfig[props.icon];

const iconContainerClasses = computed(() => {
    return `p-2 rounded-lg ${bg}`;
});
</script>
