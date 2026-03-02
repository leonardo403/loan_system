<template>
  <div class="space-y-8">
    <!-- Loading State -->
    <div v-if="loanStore.loading" class="flex justify-center py-12">
      <div class="text-center">
        <div class="inline-block animate-spin h-12 w-12 text-indigo-600 mb-4">
          <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
        </div>
        <p class="text-gray-600">Carregando detalhes da solicitação...</p>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="loanStore.error && !application" class="p-6 bg-red-50 border border-red-200 rounded-lg">
      <h2 class="text-lg font-semibold text-red-800 mb-2">Erro ao Carregar</h2>
      <p class="text-red-700">{{ loanStore.error }}</p>
      <router-link to="/" class="mt-4 inline-block text-indigo-600 hover:text-indigo-800">
        Voltar ao Início
      </router-link>
    </div>

    <!-- Content -->
    <template v-else-if="application">
      <!-- Header with Status -->
      <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="flex items-start justify-between mb-6">
          <div>
            <h1 class="text-4xl font-bold text-gray-800">
              Solicitação #{{ application.id }}
            </h1>
            <p class="text-gray-600 mt-2">{{ formatDate(application.created_at) }}</p>
          </div>
          <div :class="getStatusClass">
            {{ formatStatus(application.status) }}
          </div>
        </div>

        <!-- Applicant Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t pt-6">
          <div>
            <p class="text-sm text-gray-600">Cliente</p>
            <p class="text-lg font-semibold text-gray-800">{{ application.client?.name }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-600">Data de Solicitação</p>
            <p class="text-lg font-semibold text-gray-800">{{ formatDate(application.created_at) }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-600">Email</p>
            <p class="text-lg font-semibold text-gray-800">{{ application.client?.email || 'N/A' }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-600">Telefone</p>
            <p class="text-lg font-semibold text-gray-800">{{ application.client?.phone || 'N/A' }}</p>
          </div>
        </div>
      </div>

      <!-- Main Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Analysis Results -->
        <div class="lg:col-span-2 space-y-8">
          <!-- Requested Amount -->
          <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Valor Solicitado</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-6 rounded-lg border border-blue-200">
                <p class="text-sm text-gray-600 mb-2">Valor Solicitado</p>
                <p class="text-3xl font-bold text-indigo-600">
                  R$ {{ formatCurrency(application.requested_amount) }}
                </p>
              </div>
              <div v-if="application.approved_amount" class="bg-gradient-to-br from-green-50 to-emerald-50 p-6 rounded-lg border border-green-200">
                <p class="text-sm text-gray-600 mb-2">Valor Aprovado</p>
                <p class="text-3xl font-bold text-green-600">
                  R$ {{ formatCurrency(application.approved_amount) }}
                </p>
              </div>
            </div>
          </div>

          <!-- Credit Score -->
          <CreditScoreCard :application="application" />

          <!-- Risk Classification -->
          <RiskClassificationCard :application="application" />

          <!-- Loan Details -->
          <div v-if="application.purpose || application.interest_rate || application.max_term" class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Detalhes do Empréstimo</h2>
            <div class="space-y-4">
              <div v-if="application.purpose" class="pb-4 border-b">
                <p class="text-sm text-gray-600 mb-1">Motivo da Solicitação</p>
                <p class="text-lg text-gray-800">{{ application.purpose }}</p>
              </div>
              <div v-if="application.interest_rate" class="pb-4 border-b">
                <p class="text-sm text-gray-600 mb-1">Taxa de Juros</p>
                <p class="text-lg font-semibold text-gray-800">{{ application.interest_rate }}% a.m.</p>
              </div>
              <div v-if="application.max_term" class="pb-4">
                <p class="text-sm text-gray-600 mb-1">Prazo Máximo</p>
                <p class="text-lg font-semibold text-gray-800">{{ application.max_term }} meses</p>
              </div>
            </div>
          </div>

          <!-- Applicant Details -->
          <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Informações do Solicitante</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <p class="text-sm text-gray-600 mb-1">Renda Mensal</p>
                <p class="text-2xl font-bold text-gray-800">R$ {{ formatCurrency(application.client?.income) }}</p>
              </div>
              <div v-if="application.client?.age">
                <p class="text-sm text-gray-600 mb-1">Idade</p>
                <p class="text-2xl font-bold text-gray-800">{{ application.client.age }} anos</p>
              </div>
              <div v-if="application.client?.employment">
                <p class="text-sm text-gray-600 mb-1">Tipo de Emprego</p>
                <p class="text-lg text-gray-800">{{ application.client.employment }}</p>
              </div>
              <div v-if="application.client?.credit_history">
                <p class="text-sm text-gray-600 mb-1">Histórico de Crédito</p>
                <p class="text-lg text-gray-800 capitalize">{{ application.client.credit_history }}</p>
              </div>
            </div>
          </div>

          <!-- Justification -->
          <div v-if="application.justification" class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Justificativa da Análise</h2>
            <p class="text-gray-700 leading-relaxed">{{ application.justification }}</p>
          </div>

          <!-- Conditions -->
          <div v-if="application.conditions && application.conditions.length > 0" class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Condições</h2>
            <ul class="space-y-3">
              <li v-for="(condition, index) in application.conditions" :key="index" class="flex items-start space-x-3">
                <svg class="w-5 h-5 text-blue-600 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span class="text-gray-700">{{ condition }}</span>
              </li>
            </ul>
          </div>
        </div>

        <!-- Right Column - Status & Recommendations -->
        <div class="lg:col-span-1 space-y-8">
          <!-- Approval Status -->
          <ApprovalStatusCard :application="application" />

          <!-- Recommendations -->
          <RecommendationsCard :application="application" />

          <!-- Actions -->
          <div class="bg-white rounded-lg shadow-lg p-8 space-y-3">
            <router-link to="/new-application" class="block w-full bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition-colors text-center">
              Nova Solicitação
            </router-link>
            <router-link to="/history" class="block w-full bg-gray-200 text-gray-800 px-6 py-3 rounded-lg font-semibold hover:bg-gray-300 transition-colors text-center">
              Ver Histórico
            </router-link>
            <router-link to="/" class="block w-full border-2 border-gray-300 text-gray-800 px-6 py-3 rounded-lg font-semibold hover:border-gray-400 transition-colors text-center">
              Voltar ao Início
            </router-link>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useLoanStore } from '@/stores/loanStore'
import CreditScoreCard from '@/components/CreditScoreCard.vue'
import RiskClassificationCard from '@/components/RiskClassificationCard.vue'
import ApprovalStatusCard from '@/components/ApprovalStatusCard.vue'
import RecommendationsCard from '@/components/RecommendationsCard.vue'

const route = useRoute()
const loanStore = useLoanStore()
const application = ref(null)

const getStatusClass = computed(() => {
  const status = application.value?.status || ''
  const classes = 'px-4 py-2 rounded-lg font-semibold text-white'
  
  if (status === 'pending_analysis') {
    return `${classes} bg-yellow-500`
  } else if (status === 'analyzed') {
    return `${classes} bg-blue-500`
  }
  return `${classes} bg-gray-500`
})

const formatStatus = (status) => {
  const statusMap = {
    'pending_analysis': 'Pendente',
    'analyzed': 'Analisado'
  }
  return statusMap[status] || status
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('pt-BR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatCurrency = (value) => {
  if (!value) return '0,00'
  return parseFloat(value).toLocaleString('pt-BR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  })
}

onMounted(async () => {
  const id = route.params.id
  try {
    await loanStore.fetchApplication(id)
    application.value = loanStore.currentApplication
  } catch (error) {
    console.error('Error fetching application:', error)
  }
})
</script>

<style scoped>
</style>
