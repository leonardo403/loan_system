<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
    <!-- Header -->
    <header class="bg-white shadow-md">
      <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex justify-between items-center">
          <router-link to="/" class="flex items-center space-x-2">
            <div class="bg-indigo-600 p-2 rounded-lg">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <span class="text-xl font-bold text-indigo-600">Sistema de Empréstimos</span>
          </router-link>

          <div class="flex items-center space-x-6">
            <router-link to="/" class="text-gray-700 hover:text-indigo-600 transition-colors font-medium">
              Início
            </router-link>
            <router-link to="/new-application" class="text-gray-700 hover:text-indigo-600 transition-colors font-medium">
              Nova Solicitação
            </router-link>
            <router-link to="/history" class="text-gray-700 hover:text-indigo-600 transition-colors font-medium">
              Histórico
            </router-link>
          </div>
        </div>
      </nav>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <router-view />
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-12 py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
          <p class="text-gray-400">© 2026 Sistema de Análise de Crédito. Todos os direitos reservados.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
</script>

<style scoped>
</style>
