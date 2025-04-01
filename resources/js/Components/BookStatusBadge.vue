<template>
  <span v-if="status === 'available'"
        class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-1 rounded-full flex items-center">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
    </svg>
    Available
  </span>
    <span v-else-if="status === 'borrowed'"
          class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-1 rounded-full flex items-center">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
    </svg>
    On Loan
  </span>
    <span v-else-if="status === 'reserved'"
          class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-1 rounded-full flex items-center">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    Reserved
  </span>
</template>

<script setup>
defineProps({
    status: {
        type: String,
        required: true,
        validator: value => ['available', 'borrowed', 'reserved'].includes(value)
    }
});
</script>
