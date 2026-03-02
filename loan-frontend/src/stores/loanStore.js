import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { loanService } from '@/services/loanService'

export const useLoanStore = defineStore('loan', () => {
  // State
  const applications = ref([])
  const currentApplication = ref(null)
  const loading = ref(false)
  const error = ref(null)
  const statistics = ref(null)
  const submittedApplicationId = ref(null)

  // Computed
  const pendingApplications = computed(() =>
    applications.value.filter(app => app.status === 'pending_analysis')
  )

  const approvedApplications = computed(() =>
    applications.value.filter(app => app.approval_status === 'aprovado')
  )

  const rejectedApplications = computed(() =>
    applications.value.filter(app => app.approval_status === 'rejeitado')
  )

  // Actions
  async function submitLoanApplication(formData) {
    loading.value = true
    error.value = null
    submittedApplicationId.value = null

    try {
      const response = await loanService.analyzeLoan(formData)

      if (response.status === 202 && response.data.success) {
        submittedApplicationId.value = response.data.data.application_id
        currentApplication.value = response.data.data

        // Start polling for the result
        await pollApplicationAnalysis(response.data.data.application_id)

        return response.data.data
      }

      throw new Error(response.data.message || 'Erro ao submeter solicitação')
    } catch (err) {
      error.value = err.response?.data?.message || err.message || 'Erro ao submeter solicitação'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function pollApplicationAnalysis(applicationId) {
    try {
      const result = await loanService.pollApplicationStatus(applicationId)
      currentApplication.value = result

      // Add to applications list if not already there
      const existingIndex = applications.value.findIndex(app => app.id === result.id)
      if (existingIndex > -1) {
        applications.value[existingIndex] = result
      } else {
        applications.value.unshift(result)
      }

      return result
    } catch (err) {
      error.value = 'Erro ao aguardar resultado da análise'
      console.error('Poll error:', err)
      throw err
    }
  }

  async function fetchApplications(filters = {}) {
    loading.value = true
    error.value = null

    try {
      const response = await loanService.getApplications(filters)

      if (response.data.success) {
        applications.value = response.data.data.data || response.data.data
      }

      return applications.value
    } catch (err) {
      error.value = err.response?.data?.message || err.message || 'Erro ao carregar solicitações'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function fetchApplication(applicationId) {
    loading.value = true
    error.value = null

    try {
      const response = await loanService.getApplication(applicationId)

      if (response.data.success) {
        currentApplication.value = response.data.data
      }

      return currentApplication.value
    } catch (err) {
      error.value = err.response?.data?.message || err.message || 'Erro ao carregar solicitação'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function fetchClientApplications(clientId) {
    loading.value = true
    error.value = null

    try {
      const response = await loanService.getClientApplications(clientId)

      if (response.data.success) {
        applications.value = response.data.data
      }

      return applications.value
    } catch (err) {
      error.value = err.response?.data?.message || err.message || 'Erro ao carregar solicitações do cliente'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function fetchStatistics() {
    try {
      const response = await loanService.getStatistics()

      if (response.data.success) {
        statistics.value = response.data.data
      }

      return statistics.value
    } catch (err) {
      console.error('Error fetching statistics:', err)
      throw err
    }
  }

  function clearCurrentApplication() {
    currentApplication.value = null
    submittedApplicationId.value = null
    error.value = null
  }

  function clearError() {
    error.value = null
  }

  return {
    // State
    applications,
    currentApplication,
    loading,
    error,
    statistics,
    submittedApplicationId,

    // Computed
    pendingApplications,
    approvedApplications,
    rejectedApplications,

    // Actions
    submitLoanApplication,
    pollApplicationAnalysis,
    fetchApplications,
    fetchApplication,
    fetchClientApplications,
    fetchStatistics,
    clearCurrentApplication,
    clearError
  }
})
