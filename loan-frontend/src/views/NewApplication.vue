<template>
  <div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-8">
      <h1 class="text-4xl font-bold text-gray-800 mb-8">
        Nova Solicitação de Empréstimo
      </h1>

      <!-- Loading State -->
      <div v-if="isSubmitting" class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-center space-x-3">
          <div class="animate-spin h-5 w-5 text-blue-600">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
          </div>
          <p class="text-blue-800">Processando sua solicitação...</p>
        </div>
      </div>

      <!-- Error Message -->
      <div v-if="loanStore.error" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
        <p class="text-red-800">{{ loanStore.error }}</p>
      </div>

      <!-- Form -->
      <form @submit.prevent="handleSubmit" v-if="!isSubmitting" class="space-y-6">
        <!-- Personal Information Section -->
        <div class="border-t pt-6">
          <h2 class="text-xl font-semibold text-gray-800 mb-4">Informações Pessoais</h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <FormInput
              v-model="formData.name"
              label="Nome Completo"
              type="text"
              placeholder="João da Silva"
              required
            />
            <FormInput
              v-model="formData.email"
              label="Email"
              type="email"
              placeholder="seu@email.com"
            />
            <FormInput
              v-model="formData.age"
              label="Idade"
              type="number"
              placeholder="30"
              min="18"
              max="120"
            />
            <FormInput
              v-model="formData.phone"
              label="Telefone"
              type="tel"
              placeholder="(11) 9999-9999"
            />
            <FormInput
              v-model="formData.employment"
              label="Tipo de Emprego"
              type="text"
              placeholder="ex: CLT, Autônomo, Empresário"
            />
            <FormSelect
              v-model="formData.credit_history"
              label="Histórico de Crédito"
              :options="creditHistoryOptions"
            />
          </div>
        </div>

        <!-- Financial Information Section -->
        <div class="border-t pt-6">
          <h2 class="text-xl font-semibold text-gray-800 mb-4">Informações Financeiras</h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <FormInput
              v-model="formData.income"
              label="Renda Mensal (R$)"
              type="number"
              placeholder="5000.00"
              step="0.01"
              required
            />
            <FormInput
              v-model="formData.requested_amount"
              label="Valor Solicitado (R$)"
              type="number"
              placeholder="10000.00"
              step="0.01"
              required
            />
          </div>
        </div>

        <!-- Loan Details Section -->
        <div class="border-t pt-6">
          <h2 class="text-xl font-semibold text-gray-800 mb-4">Detalhes do Empréstimo</h2>
          
          <FormTextarea
            v-model="formData.purpose"
            label="Motivo do Empréstimo"
            placeholder="ex: Compra de veículo, Reforma da casa, etc."
            rows="4"
          />
        </div>

        <!-- Submit Button -->
        <div class="flex justify-center pt-6 border-t">
          <button
            type="submit"
            class="bg-indigo-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition-colors disabled:bg-gray-400"
            :disabled="isSubmitting || !isFormValid"
          >
            {{ isSubmitting ? 'Enviando...' : 'Solicitar Empréstimo' }}
          </button>
        </div>
      </form>

      <!-- Success Message - Redirect to Details -->
      <div v-if="showSuccessRedirect" class="text-center py-8">
        <svg class="w-16 h-16 text-green-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        <h2 class="text-2xl font-bold text-green-600 mb-2">Solicitação Recebida!</h2>
        <p class="text-gray-600 mb-4">Redirecionando para os detalhes da análise...</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useLoanStore } from '@/stores/loanStore'
import FormInput from '@/components/FormInput.vue'
import FormSelect from '@/components/FormSelect.vue'
import FormTextarea from '@/components/FormTextarea.vue'

const router = useRouter()
const loanStore = useLoanStore()

const formData = ref({
  name: '',
  email: '',
  age: '',
  phone: '',
  employment: '',
  credit_history: '',
  income: '',
  requested_amount: '',
  purpose: ''
})

const isSubmitting = ref(false)
const showSuccessRedirect = ref(false)

const creditHistoryOptions = [
  { value: '', label: 'Selecionar...' },
  { value: 'excelente', label: 'Excelente' },
  { value: 'bom', label: 'Bom' },
  { value: 'regular', label: 'Regular' },
  { value: 'ruim', label: 'Ruim' },
  { value: 'sem_historico', label: 'Sem Histórico' }
]

const isFormValid = computed(() => {
  return (
    formData.value.name &&
    formData.value.income &&
    formData.value.requested_amount &&
    parseFloat(formData.value.income) > 0 &&
    parseFloat(formData.value.requested_amount) >= 100
  )
})

const handleSubmit = async () => {
  if (!isFormValid.value) return

  isSubmitting.value = true
  loanStore.clearError()

  try {
    const submitData = {
      ...formData.value,
      income: parseFloat(formData.value.income),
      requested_amount: parseFloat(formData.value.requested_amount),
      age: formData.value.age ? parseInt(formData.value.age) : null
    }

    await loanStore.submitLoanApplication(submitData)
    showSuccessRedirect.value = true

    // Redirect to details page after a short delay
    setTimeout(() => {
      if (loanStore.currentApplication?.id) {
        router.push({
          name: 'ApplicationDetails',
          params: { id: loanStore.currentApplication.id }
        })
      }
    }, 2000)
  } catch (error) {
    console.error('Submission error:', error)
    isSubmitting.value = false
  }
}
</script>

<style scoped>
</style>
