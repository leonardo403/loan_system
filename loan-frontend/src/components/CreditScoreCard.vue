<template>
  <div class="bg-white rounded-lg shadow-lg p-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Score de Crédito</h2>
    
    <div v-if="application.credit_score !== null" class="space-y-6">
      <!-- Score Visualization -->
      <div class="text-center">
        <div class="relative inline-flex items-center justify-center">
          <svg class="w-32 h-32 transform -rotate-90" viewBox="0 0 120 120">
            <!-- Background circle -->
            <circle cx="60" cy="60" r="54" fill="none" stroke="#e5e7eb" stroke-width="8" />
            <!-- Progress circle -->
            <circle
              cx="60"
              cy="60"
              r="54"
              fill="none"
              :stroke="scoreColor"
              stroke-width="8"
              stroke-dasharray="339.29"
              :stroke-dashoffset="getDashOffset"
              stroke-linecap="round"
              class="transition-all duration-1000"
            />
          </svg>
          <div class="absolute text-center">
            <div class="text-5xl font-bold" :style="{ color: scoreColor }">
              {{ Math.round(application.credit_score) }}
            </div>
            <div class="text-gray-600 text-sm">em 1000</div>
          </div>
        </div>
      </div>

      <!-- Score Description -->
      <div :class="scoreClassDescription" class="text-center p-4 rounded-lg">
        <p class="font-bold">{{ scoreLabel }}</p>
        <p class="text-sm mt-1">{{ scoreDescription }}</p>
      </div>

      <!-- Score Details -->
      <div class="grid grid-cols-2 gap-4 text-center">
        <div class="bg-blue-50 p-4 rounded-lg">
          <p class="text-sm text-gray-600">Score Min</p>
          <p class="text-xl font-bold text-blue-600">0</p>
        </div>
        <div class="bg-blue-50 p-4 rounded-lg">
          <p class="text-sm text-gray-600">Score Máx</p>
          <p class="text-xl font-bold text-blue-600">1000</p>
        </div>
      </div>
    </div>

    <div v-else class="text-center py-8">
      <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
      </svg>
      <p class="text-gray-600">Análise em progresso...</p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

defineProps({
  application: {
    type: Object,
    required: true
  }
})

const scoreColor = computed(() => {
  const score = application.credit_score
  if (score >= 750) return '#10b981' // Green
  if (score >= 600) return '#f59e0b' // Amber
  return '#ef4444' // Red
})

const scoreLabel = computed(() => {
  const score = application.credit_score
  if (score >= 750) return 'Excelente'
  if (score >= 600) return 'Bom'
  return 'Regular'
})

const scoreDescription = computed(() => {
  const score = application.credit_score
  if (score >= 750) return 'Parabéns! Seu score é excelente.'
  if (score >= 600) return 'Seu score é bom, você tem boas chances.'
  return 'Seu score precisa de melhorias.'
})

const scoreClassDescription = computed(() => {
  const score = application.credit_score
  if (score >= 750) return 'bg-green-50 border border-green-200 text-green-800'
  if (score >= 600) return 'bg-amber-50 border border-amber-200 text-amber-800'
  return 'bg-red-50 border border-red-200 text-red-800'
})

const getDashOffset = computed(() => {
  const score = application.credit_score
  const circumference = 339.29
  const progress = score / 1000
  return circumference - progress * circumference
})
</script>

<style scoped>
</style>
