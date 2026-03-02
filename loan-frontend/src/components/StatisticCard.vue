<template>
  <div class="bg-white rounded-lg shadow-md p-6 text-center">
    <div class="text-3xl font-bold text-indigo-600 mb-2">{{ value }}</div>
    <p class="text-gray-600 font-semibold">{{ label }}</p>
  </div>
</template>

<script setup>
defineProps({
  label: {
    type: String,
    required: true
  },
  value: {
    type: [String, Number],
    required: true
  }
})
</script>

<style scoped>
</style>
