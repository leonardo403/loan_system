# Sistema de Análise de Crédito - Frontend Vue.js 3

Frontend moderno desenvolvido com Vue.js 3, Vite, Pinia, Tailwind CSS e Axios para integração com a API REST do sistema de análise de crédito.

## 🚀 Características

- ✅ **Análise de crédito com regras de negócio do backend**
- ✅ **Score de crédito automático** - Visualizado em tempo real
- ✅ **Classificação de risco** - Baixo, Médio ou Alto
- ✅ **Aprovação/rejeição inteligente** - Decisão automática
- ✅ **Histórico completo de solicitações** - Com filtros e detalhes
- ✅ **Recomendações personalizadas** - Baseadas na análise
- ✅ **API REST completa** - Integração com backend Laravel

## 📋 Pré-requisitos

- Node.js >= 16.x
- npm ou yarn
- Backend Laravel rodando em `http://localhost:8000`

## 🔧 Instalação

1. **Clone o repositório** (se não tiver feito):
```bash
cd loan-frontend
```

2. **Instale as dependências**:
```bash
npm install
```

3. **Configure as variáveis de ambiente**:
Crie um arquivo `.env.local` na raiz do projeto:
```
VITE_API_URL=http://localhost:8000/api
```

## 🚀 Como Executar

### Modo Desenvolvimento
```bash
npm run dev
```
O frontend será executado em `http://localhost:5173`

### Build para Produção
```bash
npm run build
```

### Preview da Build
```bash
npm run preview
```

## 📁 Estrutura do Projeto

```
src/
├── components/          # Componentes reutilizáveis
│   ├── FormInput.vue   # Campo de entrada
│   ├── FormSelect.vue  # Seletor
│   ├── FormTextarea.vue # Área de texto
│   ├── FeatureCard.vue # Card de recurso
│   ├── StatisticCard.vue # Card de estatística
│   ├── StepCard.vue    # Card de passo
│   ├── CreditScoreCard.vue # Visualização de score
│   ├── RiskClassificationCard.vue # Classificação de risco
│   ├── RiskIndicator.vue # Indicador de risco
│   ├── ApprovalStatusCard.vue # Status de aprovação
│   ├── RecommendationsCard.vue # Recomendações
│   └── ApplicationCard.vue # Card de solicitação
├── views/              # Páginas da aplicação
│   ├── Home.vue        # Página inicial
│   ├── NewApplication.vue # Formulário de solicitação
│   ├── ApplicationDetails.vue # Detalhes da solicitação
│   ├── ApplicationHistory.vue # Histórico
│   └── NotFound.vue    # Página 404
├── services/           # Serviços e integração API
│   ├── api.js          # Configuração do Axios
│   └── loanService.js  # Serviço de empréstimos
├── stores/             # Pinia stores
│   └── loanStore.js    # Store principal de empréstimos
├── router/             # Configuração do Vue Router
│   └── index.js        # Rotas da aplicação
├── App.vue             # Componente raiz
├── main.js             # Entrada da aplicação
└── style.css           # Estilos globais CSS
```

## 🎨 Componentes Principais

### 1. **FormInput** / **FormSelect** / **FormTextarea**
Componentes de formulário reutilizáveis com suporte a validação completa.

### 2. **CreditScoreCard**
Exibe o score de crédito com visualização circular progressiva (0-1000).

### 3. **RiskClassificationCard**
Mostra a classificação de risco (Baixo, Médio, Alto) com indicadores.

### 4. **ApprovalStatusCard**
Exibe o status da aprovação e detalhes do empréstimo aprovado.

### 5. **RecommendationsCard**
Mostra recomendações personalizadas para melhorar o score.

### 6. **ApplicationCard**
Card compacto para listagem de solicitações no histórico.

## 🔌 Integração com API

O frontend comunica com o backend através dos seguintes endpoints:

### POST /api/loans/analyze
Submete uma nova solicitação de empréstimo
```javascript
{
  name: "João Silva",
  email: "joao@email.com",
  age: 30,
  phone: "(11) 99999-9999",
  employment: "CLT",
  credit_history: "bom",
  income: 5000,
  requested_amount: 10000,
  purpose: "Compra de veículo"
}
```

### GET /api/loans/applications
Obtém lista de todas as solicitações

### GET /api/loans/applications/:id
Obtém detalhes de uma solicitação específica

### GET /api/loans/clients/:clientId/applications
Obtém solicitações de um cliente específico

### GET /api/loans/statistics
Obtém estatísticas gerais

## 📊 Fluxo da Aplicação

1. **Página Inicial** → Apresenta recursos e estatísticas
2. **Nova Solicitação** → Formulário com todos os campos necessários
3. **Processamento** → Sistema aguarda análise do backend
4. **Resultados** → Exibe score, risco, aprovação e recomendações
5. **Histórico** → Visualização de todas as solicitações anteriores

## 🎯 Recursos Implementados

### Estado Global (Pinia Store)
- Gerenciamento de solicitações
- Aplicação atual em análise
- Estados de carregamento e erro
- Estatísticas

### Polling Automático
Após submissão, o sistema faz polling automático para obter o resultado da análise (máx 30 tentativas a cada 1s).

### Filtros de Histórico
- Ver todas as solicitações
- Filtrar por status (Pendente, Analisado)
- Visualizar detalhes completos

### Formatação Automática
- Valores em moeda (R$)
- Datas localizadas (pt-BR)
- Números com separadores

## 🛠️ Desenvolvimento

### Adicionar Novo Campo no Formulário

1. Adicione em `NewApplication.vue`:
```javascript
formData.ref.novoTipo = ''
```

2. Inclua no template:
```vue
<FormInput
  v-model="formData.novoTipo"
  label="Label do Campo"
  placeholder="Placeholder"
/>
```

3. Valide no backend

### Adicionar Nova Página

1. Crie arquivo em `src/views/NovaPagina.vue`
2. Adicione rota em `src/router/index.js`
3. Atualize navegação em `src/App.vue`

## 📦 Dependências Principais

- **Vue 3.5.25** - Framework progressivo
- **Vue Router 4.2.5** - Roteamento
- **Pinia 2.1.6** - Gerenciamento de estado
- **Axios 1.6.2** - Cliente HTTP
- **Tailwind CSS 3.4.1** - Estilização
- **Lucide Vue Next 0.292.0** - Ícones vetoriais

## 🔐 Tratamento de Erros

- Mensagens de erro do backend exibidas ao usuário
- Validação de formulário antes de submissão
- Retry automático para requisições com falha
- Estados de loading enquanto aguarda resposta

## 📝 Variáveis de Ambiente

| Variável | Descrição | Padrão |
|----------|-----------|--------|
| VITE_API_URL | URL base da API | http://localhost:8000/api |

## 🚀 Deploy

Para fazer deploy da aplicação:

1. Build do projeto:
```bash
npm run build
```

2. Configure a variável `VITE_API_URL` apontando para sua API em produção

3. Sirva o conteúdo de `dist/` em seu servidor web (Nginx, Apache, etc)

## 📄 Licença

Este projeto é parte do Sistema de Análise de Crédito.

## 🤝 Contribuições

Para contribuir com melhorias no frontend, abra uma pull request com suas mudanças.

## 📞 Suporte

Para suporte ou dúvidas sobre o frontend, consulte a documentação da API ou entre em contato com o time de desenvolvimento.
