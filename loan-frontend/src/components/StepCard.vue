<template>
  <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-indigo-600">
    <div class="flex items-start space-x-4">
      <div class="flex-shrink-0">
        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-100">
          <span class="text-2xl font-bold text-indigo-600">{{ step }}</span>
        </div>
      </div>
      <div>
        <h3 class="text-lg font-semibold text-gray-800">{{ title }}</h3>
        <p class="text-gray-600 mt-2">{{ description }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  step: {
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
