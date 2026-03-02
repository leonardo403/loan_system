<template>
  <div class="space-y-8">
    <div class="bg-white rounded-lg shadow-lg p-8">
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-4xl font-bold text-gray-800">Histórico de Solicitações</h1>
        <router-link to="/new-application" class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition-colors">
          + Nova Solicitação
        </router-link>
      </div>

      <!-- Filters -->
      <div class="mb-8 p-4 bg-gray-50 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <button
            @click="filterStatus = null"
            :class="[
              'px-4 py-2 rounded-lg font-semibold transition-colors',
              filterStatus === null
                ? 'bg-indigo-600 text-white'
                : 'bg-white border border-gray-300 text-gray-800 hover:bg-gray-50'
            ]"
          >
            Todas
          </button>
          <button
            @click="filterStatus = 'pending_analysis'"
            :class="[
              'px-4 py-2 rounded-lg font-semibold transition-colors',
              filterStatus === 'pending_analysis'
                ? 'bg-yellow-500 text-white'
                : 'bg-white border border-gray-300 text-gray-800 hover:bg-gray-50'
            ]"
          >
            Pendentes
          </button>
          <button
            @click="filterStatus = 'analyzed'"
            :class="[
              'px-4 py-2 rounded-lg font-semibold transition-colors',
              filterStatus === 'analyzed'
                ? 'bg-blue-500 text-white'
                : 'bg-white border border-gray-300 text-gray-800 hover:bg-gray-50'
            ]"
          >
            Analisadas
          </button>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loanStore.loading" class="text-center py-12">
        <div class="inline-block animate-spin h-12 w-12 text-indigo-600 mb-4">
          <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
        </div>
        <p class="text-gray-600">Carregando histórico...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="loanStore.error" class="p-4 bg-red-50 border border-red-200 rounded-lg">
        <p class="text-red-800">{{ loanStore.error }}</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredApplications.length === 0" class="text-center py-12">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3 class="text-lg font-semibold text-gray-600 mb-2">Nenhuma solicitação encontrada</h3>
        <p class="text-gray-500 mb-6">Você não possui solicitações neste status</p>
        <router-link to="/new-application" class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition-colors">
          Fazer uma Solicitação
        </router-link>
      </div>

      <!-- Applications List -->
      <div v-else class="space-y-4">
        <ApplicationCard
          v-for="application in filteredApplications"
          :key="application.id"
          :application="application"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useLoanStore } from '@/stores/loanStore'
import ApplicationCard from '@/components/ApplicationCard.vue'

const loanStore = useLoanStore()
const filterStatus = ref(null)

const filteredApplications = computed(() => {
  if (!filterStatus.value) {
    return loanStore.applications
  }
  return loanStore.applications.filter(app => app.status === filterStatus.value)
})

onMounted(async () => {
  try {
    await loanStore.fetchApplications()
  } catch (error) {
    console.error('Error fetching applications:', error)
  }
})
</script>

<style scoped>
</style>
