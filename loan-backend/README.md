O que foi implementado:
🔧 Novo método no LoanAnalysisService.php
analyzeWithoutAI() - Realiza análise usando regras de negócio
📊 Sistema de Scoring:
Credit Score (0-1000): Considera idade, emprego, histórico de crédito e debt-to-income ratio
Categoria de Risco: baixo (750+), médio (600-749), alto (<600)
Taxa de Juros: Varia conforme o risco (1.5% a 8.5% ao mês)
Valor Aprovado: Baseado em 40% da renda mensal × 24 meses, ajustado pelo score
Prazo Máximo: 60 meses (baixo), 48 meses (médio), 24 meses (alto)
📋 Critérios Avaliados:
Idade ideal: 25-55 anos
Emprego: efetivo > contrato > autônomo > desempregado
Histórico: excelente > bom > regular > ruim
Debt-to-Income: < 20% (ideal) até > 70% (negado)
💼 Novo Job: AnalyzeLoanWithoutAI.php
Processamento assíncrono da análise sem IA com tratamento de erros

Como usar:
Edited
LoanAnalysisService.php+293-0
A análise retorna os mesmos campos que a versão com IA, garantindo compatibilidade total com seu banco de dados!