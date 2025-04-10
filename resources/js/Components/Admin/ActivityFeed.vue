<template>
    <div class="flow-root">
        <ul class="-mb-8">
            <li v-for="(activity, index) in activities" :key="index">
                <div class="relative pb-8">
                    <span v-if="index !== activities.length - 1"
                          class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                    <div class="relative flex space-x-3">
                        <div>
              <span
                  :class="[activity.iconBackground, 'h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white']">
                <component :is="activity.icon" class="h-5 w-5 text-white" aria-hidden="true"/>
              </span>
                        </div>
                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                            <div>
                                <p class="text-sm text-gray-500">
                                    {{ activity.description }}
                                    <a href="#" class="font-medium text-gray-900">{{ activity.user }}</a>
                                </p>
                            </div>
                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                <time :datetime="activity.datetime">{{ activity.time }}</time>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</template>

<script setup>
import {
    CheckCircleIcon,
    BookOpenIcon,
    ArrowRightIcon,
    ClockIcon
} from '@heroicons/vue/24/outline';
import {computed} from "vue";

const props = defineProps({
    activities: Array
});

const iconMap = {
    'book': BookOpenIcon,
    'borrow': ArrowRightIcon,
    'return': CheckCircleIcon,
    'default': ClockIcon
};

const bgColorMap = {
    'book': 'bg-blue-500',
    'user': 'bg-purple-500',
    'borrow': 'bg-green-500',
    'return': 'bg-amber-500',
    'default': 'bg-gray-500'
};

const processedActivities = computed(() => {
    return props.activities.map(activity => ({
        ...activity,
        icon: iconMap[activity.type] || iconMap['default'],
        iconBackground: bgColorMap[activity.type] || bgColorMap['default']
    }));
});
</script>
