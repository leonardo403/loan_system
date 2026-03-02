<template>
  <div class="bg-white rounded-lg shadow-lg p-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center space-x-2">
      <span>💡</span>
      <span>Recomendações</span>
    </h2>

    <div v-if="application.recommendations && application.recommendations.length > 0" class="space-y-3">
      <div
        v-for="(recommendation, index) in application.recommendations"
        :key="index"
        class="p-4 bg-blue-50 border-l-4 border-blue-500 rounded-lg"
      >
        <p v-if="typeof recommendation === 'string'" class="text-gray-700">
          {{ recommendation }}
        </p>
        <div v-else class="text-gray-700">
          <p class="font-semibold">{{ recommendation.title }}</p>
          <p class="text-sm mt-1">{{ recommendation.description }}</p>
        </div>
      </div>
    </div>

    <div v-else class="text-center py-6">
      <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <p class="text-gray-600">Nenhuma recomendação disponível no momento</p>
    </div>
  </div>
</template>

<script setup>
defineProps({
  application: {
    type: Object,
    required: true
  }
})
</script>

<style scoped>
</style>
