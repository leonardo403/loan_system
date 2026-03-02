<template>
  <router-link
    :to="{ name: 'ApplicationDetails', params: { id: application.id } }"
    class="block bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6 border-l-4"
    :class="statusBorderColor"
  >
    <div class="flex justify-between items-start mb-4">
      <div class="flex-1">
        <h3 class="text-lg font-bold text-gray-800">
          Solicitação #{{ application.id }}
        </h3>
        <p class="text-sm text-gray-600">{{ formatDate(application.created_at) }}</p>
      </div>
      <div :class="statusBadge" class="px-3 py-1 rounded-full text-sm font-semibold">
        {{ formatStatus(application.status) }}
      </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
      <div>
        <p class="text-xs text-gray-600 uppercase">Cliente</p>
        <p class="font-semibold text-gray-800">{{ application.client?.name || 'N/A' }}</p>
      </div>
      <div>
        <p class="text-xs text-gray-600 uppercase">Valor Solicitado</p>
        <p class="font-semibold text-gray-800">R$ {{ formatCurrency(application.requested_amount) }}</p>
      </div>
      <div>
        <p class="text-xs text-gray-600 uppercase">Score</p>
        <p v-if="application.credit_score" :class="scoreClass" class="font-semibold text-lg">
          {{ Math.round(application.credit_score) }}
        </p>
        <p v-else class="text-gray-500">--</p>
      </div>
      <div>
        <p class="text-xs text-gray-600 uppercase">Decisão</p>
        <p v-if="application.approval_status" :class="approvalClass" class="font-semibold">
          {{ formatApprovalStatus(application.approval_status) }}
        </p>
        <p v-else class="text-gray-500">Pendente</p>
      </div>
    </div>

    <div v-if="application.risk_category" class="flex items-center space-x-2">
      <span class="text-xs text-gray-600 uppercase">Risco:</span>
      <span :class="riskClass" class="text-xs font-semibold capitalize px-2 py-1 rounded">
        {{ application.risk_category }}
      </span>
    </div>
  </router-link>
</template>

<script setup>
import { computed } from 'vue'

defineProps({
  application: {
    type: Object,
    required: true
  }
})

const statusBorderColor = computed(() => {
  if (application.application?.status === 'pending_analysis') return 'border-yellow-500'
  if (application.application?.status === 'analyzed') return 'border-blue-500'
  return 'border-gray-300'
})

const statusBadge = computed(() => {
  const status = application.application?.status || ''
  if (status === 'pending_analysis') return 'bg-yellow-100 text-yellow-800'
  if (status === 'analyzed') return 'bg-blue-100 text-blue-800'
  return 'bg-gray-100 text-gray-800'
})

const scoreClass = computed(() => {
  const score = application.application?.credit_score || 0
  if (score >= 750) return 'text-green-600'
  if (score >= 600) return 'text-amber-600'
  return 'text-red-600'
})

const approvalClass = computed(() => {
  const status = application.application?.approval_status || ''
  if (status === 'aprovado') return 'text-green-600'
  if (status === 'rejeitado') return 'text-red-600'
  return 'text-gray-600'
})

const riskClass = computed(() => {
  const risk = application.application?.risk_category || ''
  if (risk === 'baixo') return 'bg-green-100 text-green-800'
  if (risk === 'médio') return 'bg-amber-100 text-amber-800'
  return 'bg-red-100 text-red-800'
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('pt-BR', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatStatus = (status) => {
  const statusMap = {
    'pending_analysis': 'Pendente',
    'analyzed': 'Analisado'
  }
  return statusMap[status] || status
}

const formatApprovalStatus = (status) => {
  return status === 'aprovado' ? 'Aprovado' : 'Rejeitado'
}

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
