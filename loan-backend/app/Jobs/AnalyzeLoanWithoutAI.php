<?php

namespace App\Jobs;

use App\Models\LoanApplication;
use App\Services\LoanAnalysisService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AnalyzeLoanWithoutAI implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public LoanApplication $loanApplication,
        public array $clientData
    ) {
        // Set to use sync queue by default, can be changed to redis/database
        $this->queue = 'default';
    }

    public function handle(LoanAnalysisService $analysisService): void
    {
        try {
            Log::info("Starting rule-based analysis for loan application {$this->loanApplication->id}");

            // Perform analysis without AI (rule-based)
            $analysis = $analysisService->analyzeWithoutAI($this->clientData);

            // Update loan application with results
            $this->loanApplication->update([
                'status' => 'analyzed',
                'credit_score' => $analysis['scoreCredito'],
                'risk_category' => $analysis['riscoCategoria'],
                'approval_status' => $analysis['aprovacao'],
                'approved_amount' => $analysis['valorAprovado'],
                'interest_rate' => $analysis['taxaJurosSugerida'],
                'max_term' => $analysis['prazoMaximo'],
                'justification' => $analysis['justificativa'],
                'recommendations' => $analysis['recomendacoes'] ?? [],
                'conditions' => $analysis['condicoes'] ?? [],
                'ai_analysis' => $analysis,
            ]);

            Log::info("Rule-based analysis completed successfully for loan application {$this->loanApplication->id}");

        } catch (\Throwable $e) {
            Log::error(
                "Failed to analyze loan application {$this->loanApplication->id}: {$e->getMessage()}",
                ['trace' => $e->getTraceAsString()]
            );

            // Update status to failed
            $this->loanApplication->update([
                'status' => 'failed',
                'justification' => "Erro ao processar análise: {$e->getMessage()}"
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error(
            "Job failed permanently for loan application {$this->loanApplication->id}: {$exception->getMessage()}"
        );

        $this->loanApplication->update([
            'status' => 'failed',
            'justification' => "Falha permanente ao processar análise: {$exception->getMessage()}"
        ]);
    }
}
