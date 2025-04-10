<template>
    <div class="bg-white overflow-hidden shadow rounded-lg" :class="{
    'border-l-4 border-blue-500': color === 'blue',
    'border-l-4 border-green-500': color === 'green',
    'border-l-4 border-red-500': color === 'red',
    'border-l-4 border-amber-500': color === 'amber'
  }">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0" :class="{
          'bg-blue-100': color === 'blue',
          'bg-green-100': color === 'green',
          'bg-red-100': color === 'red',
          'bg-amber-100': color === 'amber'
        }" style="height: 48px; width: 48px;">
                    <component :is="iconComponent" class="h-6 w-6" :class="{
            'text-blue-600': color === 'blue',
            'text-green-600': color === 'green',
            'text-red-600': color === 'red',
            'text-amber-600': color === 'amber'
          }"/>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            {{ title }}
                        </dt>
                        <dd>
                            <div class="text-lg font-medium text-gray-900">
                                {{ value }}
                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {computed} from 'vue';

const props = defineProps({
    title: String,
    value: [String, Number],
    icon: String,
    color: {
        type: String,
        default: 'blue'
    }
});

const iconComponent = computed(() => {
    switch (props.icon) {
        case 'book':
            return BookIcon;
        case 'borrow':
            return BorrowIcon;
        case 'alert':
            return AlertIcon;
        case 'reservation':
            return ReservationIcon;
        default:
            return BookIcon;
    }
});

const BookIcon = {
    template: `
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
    </svg>
  `
};

const BorrowIcon = {
    template: `
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
    </svg>
  `
};

const AlertIcon = {
    template: `
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
    </svg>
  `
};

const ReservationIcon = {
    template: `
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
    </svg>
  `
};
</script>
