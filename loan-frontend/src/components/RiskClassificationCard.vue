<template>
  <div class="bg-white rounded-lg shadow-lg p-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Classificação de Risco</h2>

    <div v-if="application.risk_category" class="space-y-6">
      <!-- Risk Category Badge -->
      <div class="text-center">
        <div :class="riskClassBadge" class="inline-block px-8 py-4 rounded-lg text-center">
          <div class="text-3xl font-bold capitalize">{{ application.risk_category }}</div>
          <div class="text-sm mt-2">Perfil de Risco</div>
        </div>
      </div>

      <!-- Risk Description -->
      <div :class="riskClassDescription" class="p-4 rounded-lg">
        <p class="font-semibold mb-2">{{ riskLabel }}</p>
        <p class="text-sm">{{ riskDescription }}</p>
      </div>

      <!-- Risk Indicators -->
      <div class="grid grid-cols-1 gap-3">
        <RiskIndicator
          :class="getRiskIndicatorClass('income')"
          icon="💰"
          label="Renda"
          :status="getRiskIndicatorStatus('income')"
        />
        <RiskIndicator
          :class="getRiskIndicatorClass('debt')"
          icon="📊"
          label="Relação Dívida/Renda"
          :status="getRiskIndicatorStatus('debt')"
        />
        <RiskIndicator
          :class="getRiskIndicatorClass('history')"
          icon="📜"
          label="Histórico"
          :status="getRiskIndicatorStatus('history')"
        />
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
import RiskIndicator from './RiskIndicator.vue'

defineProps({
  application: {
    type: Object,
    required: true
  }
})

const riskClassBadge = computed(() => {
  const risk = application.application?.risk_category || ''
  if (risk === 'baixo') return 'bg-green-100 text-green-800'
  if (risk === 'médio') return 'bg-amber-100 text-amber-800'
  return 'bg-red-100 text-red-800'
})

const riskClassDescription = computed(() => {
  const risk = application.application?.risk_category || ''
  if (risk === 'baixo') return 'bg-green-50 border border-green-200 text-green-800'
  if (risk === 'médio') return 'bg-amber-50 border border-amber-200 text-amber-800'
  return 'bg-red-50 border border-red-200 text-red-800'
})

const riskLabel = computed(() => {
  const risk = application.risk_category || ''
  if (risk === 'baixo') return 'Risco Baixo'
  if (risk === 'médio') return 'Risco Médio'
  return 'Risco Alto'
})

const riskDescription = computed(() => {
  const risk = application.risk_category || ''
  if (risk === 'baixo') return 'Excelente perfil! Você tem ótimas chances de aprovação com boas condições.'
  if (risk === 'médio') return 'Perfil moderado. Você pode ser aprovado com condições apropriadas.'
  return 'Perfil de maior risco. Recomendamos melhorar sua situação financeira.'
})

const getRiskIndicatorStatus = (indicator) => {
  if (indicator === 'income') return 'good'
  if (indicator === 'debt') return 'medium'
  return 'good'
}

const getRiskIndicatorClass = (indicator) => {
  const status = getRiskIndicatorStatus(indicator)
  if (status === 'good') return 'bg-green-50 border-green-200'
  if (status === 'medium') return 'bg-amber-50 border-amber-200'
  return 'bg-red-50 border-red-200'
}
</script>

<style scoped>
</style>
