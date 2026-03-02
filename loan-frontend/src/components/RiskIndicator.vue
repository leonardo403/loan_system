<template>
  <div :class="['border-l-4 p-4', classes]">
    <div class="flex items-center space-x-3">
      <span class="text-2xl">{{ icon }}</span>
      <div class="flex-1">
        <p class="text-sm font-semibold">{{ label }}</p>
        <p class="text-xs capitalize">{{ status }}</p>
      </div>
      <svg class="w-5 h-5" :class="statusColor" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
      </svg>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

defineProps({
  icon: {
    type: String,
    required: true
  },
  label: {
    type: String,
    required: true
  },
  status: {
    type: String,
    required: true
  }
})

const statusColor = computed(() => {
  const status = props.status.toLowerCase()
  if (status === 'good') return 'text-green-600'
  if (status === 'medium') return 'text-amber-600'
  return 'text-red-600'
})

const classes = computed(() => {
  const status = props.status.toLowerCase()
  if (status === 'good') return 'border-green-300 bg-green-50 text-green-800'
  if (status === 'medium') return 'border-amber-300 bg-amber-50 text-amber-800'
  return 'border-red-300 bg-red-50 text-red-800'
})

defineProps({
  icon: String,
  label: String,
  status: String
})
</script>

<style scoped>
</style>
