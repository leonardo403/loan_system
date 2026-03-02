<template>
  <div class="space-y-8">
    <!-- Hero Section -->
    <section class="relative">
      <div class="bg-gradient-to-r from-indigo-600 to-blue-600 rounded-lg shadow-lg p-8 md:p-12 text-white">
        <div class="max-w-4xl">
          <h1 class="text-4xl md:text-5xl font-bold mb-4">
            Análise Inteligente de Crédito
          </h1>
          <p class="text-lg text-indigo-100 mb-8">
            Solicite um empréstimo e receba uma análise automática com score de crédito, classificação de risco e decisão de aprovação em tempo real.
          </p>
          <div class="flex flex-col sm:flex-row gap-4">
            <router-link to="/new-application" class="bg-white text-indigo-600 px-8 py-3 rounded-lg font-semibold hover:bg-indigo-50 transition-colors text-center">
              Solicitar Empréstimo
            </router-link>
            <router-link to="/history" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:bg-opacity-10 transition-colors text-center">
              Ver Histórico
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Grid -->
    <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <FeatureCard
        icon="⚡"
        title="Análise Rápida"
        description="Receba uma decisão em segundos com base em critérios inteligentes"
      />
      <FeatureCard
        icon="📊"
        title="Score de Crédito"
        description="Cálculo automático do seu score com base em dados financeiros"
      />
      <FeatureCard
        icon="🎯"
        title="Classificação de Risco"
        description="Classificação automática do seu perfil de risco (Baixo, Médio ou Alto)"
      />
      <FeatureCard
        icon="✅"
        title="Aprovação Inteligente"
        description="Decisão automática considerando regras de negócio complexas"
      />
      <FeatureCard
        icon="💡"
        title="Recomendações Personalizadas"
        description="Sugestões personalizadas para melhorar suas chances"
      />
      <FeatureCard
        icon="📈"
        title="Histórico Completo"
        description="Acompanhe todas as suas solicitações e decisões"
      />
    </section>

    <!-- Statistics Section -->
    <section v-if="statistics" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <StatisticCard
        label="Total de Solicitações"
        :value="statistics.total_applications"
      />
      <StatisticCard
        label="Aprovadas"
        :value="statistics.approved"
        class="bg-green-50"
      />
      <StatisticCard
        label="Pendentes"
        :value="statistics.pending"
        class="bg-yellow-50"
      />
      <StatisticCard
        label="Score Médio"
        :value="formatScore(statistics.average_score)"
        class="bg-blue-50"
      />
    </section>

    <!-- Quick Links -->
    <section class="bg-white rounded-lg shadow-md p-8">
      <h2 class="text-2xl font-bold text-gray-800 mb-6">Como Funciona</h2>
      <div class="space-y-4">
        <StepCard
          step="1"
          title="Preenchimento do Formulário"
          description="Forneça seus dados pessoais, renda e informações sobre o empréstimo desejado"
        />
        <StepCard
          step="2"
          title="Análise Automática"
          description="Nosso sistema analisa suas informações usando regras de negócio avançadas"
        />
        <StepCard
          step="3"
          title="Resultado Imediato"
          description="Receba seu score de crédito, classificação de risco e decisão de aprovação"
        />
        <StepCard
          step="4"
          title="Recomendações"
          description="Sugestões personalizadas para melhorar seu score ou solicitar novamente"
        />
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useLoanStore } from '@/stores/loanStore'
import FeatureCard from '@/components/FeatureCard.vue'
import StatisticCard from '@/components/StatisticCard.vue'
import StepCard from '@/components/StepCard.vue'

const loanStore = useLoanStore()
const statistics = ref(null)

onMounted(async () => {
  try {
    await loanStore.fetchStatistics()
    statistics.value = loanStore.statistics
  } catch (error) {
    console.error('Error fetching statistics:', error)
  }
})

const formatScore = (score) => {
  if (!score) return 'N/A'
  return Math.round(score)
}
</script>

<style scoped>
</style>
