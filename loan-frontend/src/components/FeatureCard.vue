<template>
  <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-indigo-600 hover:shadow-lg transition-shadow">
    <div class="text-4xl mb-3">{{ icon }}</div>
    <h3 class="text-xl font-bold text-gray-800 mb-2">{{ title }}</h3>
    <p class="text-gray-600">{{ description }}</p>
  </div>
</template>

<script setup>
defineProps({
  icon: {
    type: String,
    required: true
  },
  title: {
    type: String,
    required: true
  },
  description: {
    type: String,
    required: true
  }
})
</script>

<style scoped>
</style>
