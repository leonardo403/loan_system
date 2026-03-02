<template>
  <div class="bg-white rounded-lg shadow-lg p-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Status de Aprovação</h2>

    <div v-if="application.approval_status" class="space-y-6">
      <!-- Approval Badge -->
      <div class="text-center">
        <div :class="approvalClassBadge" class="inline-flex items-center space-x-2 px-8 py-4 rounded-lg">
          <svg v-if="isApproved" class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
          </svg>
          <svg v-else class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
          </svg>
          <div class="text-3xl font-bold capitalize">
            {{ application.approval_status === 'aprovado' ? 'Aprovado' : 'Rejeitado' }}
          </div>
        </div>
      </div>

      <!-- Approval Details -->
      <div v-if="isApproved" class="space-y-3 bg-green-50 p-4 rounded-lg border border-green-200">
        <div class="flex justify-between items-center">
          <span class="text-gray-700">Valor Aprovado:</span>
          <span class="font-bold text-green-600">R$ {{ formatCurrency(application.approved_amount) }}</span>
        </div>
        <div v-if="application.interest_rate" class="flex justify-between items-center">
          <span class="text-gray-700">Taxa de Juros:</span>
          <span class="font-bold text-green-600">{{ application.interest_rate }}% a.m.</span>
        </div>
        <div v-if="application.max_term" class="flex justify-between items-center">
          <span class="text-gray-700">Prazo Máximo:</span>
          <span class="font-bold text-green-600">{{ application.max_term }} meses</span>
        </div>
      </div>

      <div v-else class="bg-red-50 p-4 rounded-lg border border-red-200">
        <p class="text-red-800 text-center font-semibold">
          Infelizmente sua solicitação foi rejeitada
        </p>
      </div>
    </div>

    <div v-else class="text-center py-8">
      <div class="inline-block animate-spin h-12 w-12 text-indigo-600 mb-4">
        <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
      </div>
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

const isApproved = computed(() => {
  return application.application?.approval_status === 'aprovado'
})

const approvalClassBadge = computed(() => {
  return isApproved.value
    ? 'bg-green-100 text-green-800'
    : 'bg-red-100 text-red-800'
})

const formatCurrency = (value) => {
  if (!value) return '0,00'
  return parseFloat(value).toLocaleString('pt-BR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  })
}
</script>

<style scoped>
</style>
