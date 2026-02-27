<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use OpenAI;

class LoanAnalysisService
{
    private const MAX_RETRIES = 3;
    private const INITIAL_RETRY_DELAY = 2; // seconds
    private const MAX_RETRY_DELAY = 60; // seconds

    public function analyzeWithAI(array $data, int $attempt = 1): array
    {
        $prompt = $this->buildPrompt($data);

        try {
            $apiKey = env('OPENAI_API_KEY');
            if (empty($apiKey)) {
                throw new \RuntimeException('OPENAI_API_KEY não está definido no .env');
            }

            $client = OpenAI::client($apiKey);

            $response = $client->chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'max_tokens' => 100,
            ]);

            // compatibilidade com array/objeto retornado
            if (is_array($response)) {
                $textContent = $response['choices'][0]['message']['content'] ?? '';
            } else {
                $textContent = $response->choices[0]->message->content ?? '';
            }

            // Clean and parse JSON
            $cleanText = preg_replace('/```json|```/', '', $textContent);
            $cleanText = trim($cleanText);

            $analysis = json_decode($cleanText, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Erro ao decodificar resposta JSON da IA: ' . json_last_error_msg());
            }

            return $analysis;

        } catch (\Exception $e) {
            // Check if error is rate limit related
            $errorMessage = $e->getMessage();
            $isRateLimitError = preg_match(
                '/rate_limit_exceeded|Rate limit|quota|429/',
                $errorMessage
            ) === 1;

            // Retry on rate limit or transient errors
            if ($isRateLimitError && $attempt < self::MAX_RETRIES) {
                $delay = min(
                    self::INITIAL_RETRY_DELAY * (2 ** ($attempt - 1)),
                    self::MAX_RETRY_DELAY
                );

                Log::warning(
                    "Rate limit encountered. Retrying in {$delay}s (attempt {$attempt}/" . self::MAX_RETRIES . "): " . $e->getMessage()
                );

                sleep($delay);
                return $this->analyzeWithAI($data, $attempt + 1);
            }

            Log::error('Erro ao analisar com IA: ' . $e->getMessage());
            throw new \RuntimeException('Erro ao analisar com IA: ' . $e->getMessage());
        }
    }

    public function analyzeWithoutAI(array $data): array
    {
        $income = (float)($data['income'] ?? 0);
        $age = (int)($data['age'] ?? 0);
        $requestedAmount = (float)($data['requested_amount'] ?? 0);
        $employment = strtolower($data['employment'] ?? '');
        $creditHistory = strtolower($data['credit_history'] ?? '');

        try {
            // 1. Calcular Credit Score (0-1000)
            $creditScore = $this->calculateCreditScore($income, $age, $employment, $creditHistory, $requestedAmount);

            // 2. Determinar categoria de risco
            $riskCategory = $this->determineRiskCategory($creditScore, $income, $requestedAmount);

            // 3. Calcular taxa de juros sugerida baseada no risco
            $interestRate = $this->calculateInterestRate($riskCategory, $creditScore);

            // 4. Determinar valor aprovado
            $approvedAmount = $this->calculateApprovedAmount($income, $requestedAmount, $creditScore, $employment);

            // 5. Determinar prazo máximo
            $maxTerm = $this->calculateMaxTerm($income, $approvedAmount, $riskCategory);

            // 6. Determinar status de aprovação
            $approvalStatus = $this->determineApprovalStatus($creditScore, $riskCategory, $income, $requestedAmount);

            // 7. Gerar justificativa
            $justification = $this->generateJustification(
                $creditScore,
                $riskCategory,
                $approvalStatus,
                $income,
                $requestedAmount,
                $employment
            );

            // 8. Gerar recomendações
            $recommendations = $this->generateRecommendations($creditScore, $riskCategory, $employment, $creditHistory);

            // 9. Gerar condições
            $conditions = $this->generateConditions($approvalStatus, $riskCategory, $income, $approvedAmount);

            return [
                'scoreCredito' => $creditScore,
                'riscoCategoria' => $riskCategory,
                'aprovacao' => $approvalStatus,
                'taxaJurosSugerida' => $interestRate,
                'valorAprovado' => round($approvedAmount, 2),
                'prazoMaximo' => $maxTerm,
                'justificativa' => $justification,
                'recomendacoes' => $recommendations,
                'condicoes' => $conditions,
            ];
        } catch (\Exception $e) {
            Log::error('Erro ao analisar crédito sem IA: ' . $e->getMessage());
            throw new \RuntimeException('Erro ao analisar crédito sem IA: ' . $e->getMessage());
        }
    }

    private function calculateCreditScore(float $income, int $age, string $employment, string $creditHistory, float $requestedAmount): int
    {
        $score = 600; // Score base

        // Idade (18-65 é ideal)
        if ($age >= 25 && $age <= 55) {
            $score += 100;
        } elseif ($age >= 20 && $age <= 65) {
            $score += 50;
        } elseif ($age < 18 || $age > 75) {
            $score -= 100;
        }

        // Emprego
        if (strpos($employment, 'efetivo') !== false || strpos($employment, 'permanent') !== false) {
            $score += 150;
        } elseif (strpos($employment, 'contrato') !== false || strpos($employment, 'temporário') !== false) {
            $score += 50;
        } elseif (strpos($employment, 'autônomo') !== false || strpos($employment, 'freelancer') !== false) {
            $score += 30;
        } elseif (strpos($employment, 'desempregado') !== false || strpos($employment, 'unemployed') !== false) {
            $score -= 150;
        }

        // Histórico de Crédito
        if (strpos($creditHistory, 'excelente') !== false || strpos($creditHistory, 'excellent') !== false) {
            $score += 150;
        } elseif (strpos($creditHistory, 'bom') !== false || strpos($creditHistory, 'good') !== false) {
            $score += 100;
        } elseif (strpos($creditHistory, 'regular') !== false || strpos($creditHistory, 'fair') !== false) {
            $score += 30;
        } elseif (strpos($creditHistory, 'ruim') !== false || strpos($creditHistory, 'poor') !== false || strpos($creditHistory, 'sem histórico') !== false) {
            $score -= 50;
        }

        // Renda vs Valor solicitado (debt-to-income ratio)
        if ($income > 0) {
            $monthlyPayment = $requestedAmount / 24; // média de 24 meses
            $debtToIncome = $monthlyPayment / $income;

            if ($debtToIncome < 0.2) {
                $score += 100;
            } elseif ($debtToIncome < 0.3) {
                $score += 50;
            } elseif ($debtToIncome < 0.5) {
                $score += 10;
            } elseif ($debtToIncome >= 0.5 && $debtToIncome < 0.7) {
                $score -= 50;
            } else {
                $score -= 150;
            }
        }

        return max(0, min(1000, $score));
    }

    private function determineRiskCategory(int $creditScore, float $income, float $requestedAmount): string
    {
        if ($creditScore >= 750) {
            return 'baixo';
        } elseif ($creditScore >= 600) {
            return 'médio';
        } else {
            return 'alto';
        }
    }

    private function calculateInterestRate(string $riskCategory, int $creditScore): float
    {
        $baseRate = 1.5; // 1.5% ao mês como base

        switch ($riskCategory) {
            case 'baixo':
                return round($baseRate + (1 - ($creditScore / 1000)) * 2, 2);
            case 'médio':
                return round($baseRate + 2 + (1 - ($creditScore / 1000)) * 2, 2);
            case 'alto':
                return round($baseRate + 5 + (1 - ($creditScore / 1000)) * 3, 2);
            default:
                return $baseRate + 3;
        }
    }

    private function calculateApprovedAmount(float $income, float $requestedAmount, int $creditScore, string $employment): float
    {
        // Limitar a 40% da renda mensal como pagamento
        $maxMonthlyPayment = $income * 0.4;
        $maxLoanAmount = $maxMonthlyPayment * 24; // 24 meses

        // Ajustar baseado no credit score
        $scoreMultiplier = $creditScore / 1000;
        $adjustedMax = $maxLoanAmount * $scoreMultiplier;

        // Ajustar baseado no tipo de emprego
        if (strpos($employment, 'efetivo') !== false) {
            $adjustedMax *= 1.1;
        } elseif (strpos($employment, 'desempregado') !== false) {
            $adjustedMax *= 0.5;
        }

        return min($requestedAmount, $adjustedMax);
    }

    private function calculateMaxTerm(float $income, float $approvedAmount, string $riskCategory): int
    {
        $baseTerm = 36; // 36 meses padrão

        switch ($riskCategory) {
            case 'baixo':
                $term = 60; // até 60 meses
                break;
            case 'médio':
                $term = 48; // até 48 meses
                break;
            case 'alto':
                $term = 24; // até 24 meses
                break;
            default:
                $term = $baseTerm;
        }

        // Verificar se é viável (parcela mínima de R$ 50)
        $monthlyPayment = $approvedAmount / $term;
        while ($monthlyPayment < 50 && $term > 12) {
            $term -= 6;
            $monthlyPayment = $approvedAmount / $term;
        }

        return max(12, $term);
    }

    private function determineApprovalStatus(int $creditScore, string $riskCategory, float $income, float $requestedAmount): string
    {
        if ($creditScore < 400 || $income == 0) {
            return 'negado';
        } elseif ($creditScore >= 750 && $riskCategory === 'baixo') {
            return 'aprovado';
        } elseif ($creditScore >= 600) {
            return 'aprovado_condicional';
        } else {
            return 'negado';
        }
    }

    private function generateJustification(int $creditScore, string $riskCategory, string $approvalStatus, float $income, float $requestedAmount, string $employment): string
    {
        $justification = "Análise de crédito realizada sem IA, baseada em regras de negócio. ";

        switch ($approvalStatus) {
            case 'aprovado':
                $justification .= "Solicitação aprovada. Cliente apresenta excelente perfil com credit score de {$creditScore}/1000 " .
                    "e categoria de risco {$riskCategory}. Renda mensal de R$ " . number_format($income, 2, ',', '.') .
                    " é adequada para o valor solicitado de R$ " . number_format($requestedAmount, 2, ',', '.');
                break;

            case 'aprovado_condicional':
                $justification .= "Solicitação aprovada com condições. O credit score de {$creditScore}/1000 indica " .
                    "capacidade de pagamento, porém o cliente se enquadra em categoria de risco {$riskCategory}. " .
                    "Recomenda-se análise complementar ou apresentação de documentação adicional.";
                break;

            case 'negado':
                $justification .= "Solicitação negada. O credit score de {$creditScore}/1000 não atende aos critérios mínimos " .
                    "de aprovação. ";
                if ($income == 0) {
                    $justification .= "Renda não informada ou insuficiente. ";
                }
                if ($riskCategory === 'alto') {
                    $justification .= "Cliente classificado em categoria de risco alto.";
                }
                break;
        }

        return $justification;
    }

    private function generateRecommendations(int $creditScore, string $riskCategory, string $employment, string $creditHistory): array
    {
        $recommendations = [];

        if ($creditScore < 600) {
            $recommendations[] = "Melhorar histórico de crédito pagando dívidas em dia";
            $recommendations[] = "Aumentar renda ou procurar emprego com melhor estabilidade";
        }

        if (strpos($employment, 'desempregado') !== false) {
            $recommendations[] = "Buscar emprego para melhorar perfil de crédito";
            $recommendations[] = "Considerar renda de outros familiares como co-devedor";
        }

        if (strpos($creditHistory, 'ruim') !== false || strpos($creditHistory, 'sem histórico') !== false) {
            $recommendations[] = "Construir histórico de crédito com cartão de débito/crédito";
            $recommendations[] = "Manter contas bancárias ativas e sem atrasos";
        }

        if ($riskCategory === 'alto') {
            $recommendations[] = "Oferecer garantias adicionais ou avalista";
            $recommendations[] = "Considerar reduzir valor solicitado";
        }

        if (empty($recommendations)) {
            $recommendations[] = "Continue mantendo bom histórico de crédito";
            $recommendations[] = "Acompanhe regularmente suas informações de crédito";
        }

        return $recommendations;
    }

    private function generateConditions(string $approvalStatus, string $riskCategory, float $income, float $approvedAmount): array
    {
        $conditions = [];

        if ($approvalStatus === 'aprovado_condicional' || $approvalStatus === 'negado') {
            $conditions[] = "Análise complementar obrigatória";
        }

        if ($riskCategory === 'alto') {
            $conditions[] = "Pode ser necessário avalista";
            $conditions[] = "Comprovação de renda obrigatória (últimos 3 meses)";
            $conditions[] = "Análise de referências pessoais";
        } elseif ($riskCategory === 'médio') {
            $conditions[] = "Comprovação de renda obrigatória";
            $conditions[] = "Verificação de documentação pessoal";
        }

        if ($approvalStatus === 'aprovado') {
            $conditions[] = "Assinatura de contrato de empréstimo";
            $conditions[] = "Cadastro no sistema de proteção de crédito";
        }

        return $conditions;
    }

    private function buildPrompt(array $data): string
    {
        return sprintf(
            "Você é um analista de crédito especializado. Analise o seguinte perfil de cliente para empréstimo e
            no final da análise mostrar financeiras para realizar emprestimo com link dos sites:

Nome: %s
Renda Mensal: R$ %.2f
Idade: %s anos
Situação Empregatícia: %s
Histórico de Crédito: %s
Valor Solicitado: R$ %.2f
Finalidade: %s
Bancos: %s

Forneça uma análise em formato JSON com:
{
  \"scoreCredito\": número de 0-1000,
  \"riscoCategoria\": \"baixo/médio/alto\",
  \"aprovacao\": \"aprovado/aprovado_condicional/negado\",
  \"taxaJurosSugerida\": número (ex: 1.5 para 1.5%% ao mês),
  \"valorAprovado\": número,
  \"prazoMaximo\": número em meses,
  \"justificativa\": \"explicação detalhada\",
  \"recomendacoes\": [\"lista\", \"de\", \"recomendações\"],
  \"condicoes\": [\"condições\", \"se\", \"houver\"]
}

Responda APENAS com o JSON, sem texto adicional.",
            $data['name'] ?? 'Não informado',
            $data['income'] ?? 0,
            $data['age'] ?? 'Não informado',
            $data['employment'] ?? 'Não informado',
            $data['credit_history'] ?? 'Não informado',
            $data['requested_amount'] ?? 0,
            $data['purpose'] ?? 'Não especificado',
            $data['banks'] ?? 'Não especificado'
        );
    }
}
