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

class AnalyzeLoanWithAI implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public LoanApplication $loanApplication,
        public array $clientData
    ) {
        // Set to use sync queue initially, can be changed to redis/database
        $this->queue = 'default';
    }

    public function handle(LoanAnalysisService $analysisService): void
    {
        try {
            Log::info("Starting AI analysis for loan application {$this->loanApplication->id}");

            // Perform AI analysis with retry logic
            $aiAnalysis = $analysisService->analyzeWithAI($this->clientData);

            // Update loan application with results
            $this->loanApplication->update([
                'status' => 'analyzed',
                'credit_score' => $aiAnalysis['scoreCredito'],
                'risk_category' => $aiAnalysis['riscoCategoria'],
                'approval_status' => $aiAnalysis['aprovacao'],
                'approved_amount' => $aiAnalysis['valorAprovado'],
                'interest_rate' => $aiAnalysis['taxaJurosSugerida'],
                'max_term' => $aiAnalysis['prazoMaximo'],
                'justification' => $aiAnalysis['justificativa'],
                'recommendations' => $aiAnalysis['recomendacoes'] ?? [],
                'conditions' => $aiAnalysis['condicoes'] ?? [],
                'ai_analysis' => $aiAnalysis,
            ]);

            Log::info("AI analysis completed successfully for loan application {$this->loanApplication->id}");

        } catch (\Exception $e) {
            Log::error("AI analysis failed for loan application {$this->loanApplication->id}: " . $e->getMessage());

            // Update status to failed
            $this->loanApplication->update([
                'status' => 'analysis_failed',
                'analysis_error' => $e->getMessage(),
            ]);

            // Retry the job with exponential backoff
            if ($this->attempts() < 3) {
                $delay = (2 ** $this->attempts()) * 60; // 1 min, 2 min, 4 min backoff
                $this->release($delay);
                Log::info("Retrying AI analysis for loan {$this->loanApplication->id} in {$delay} seconds (attempt {$this->attempts()})");
            } else {
                Log::error("AI analysis permanently failed for loan {$this->loanApplication->id} after 3 attempts");
                throw $e;
            }
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Job failed for loan application {$this->loanApplication->id}: " . $exception->getMessage());

        $this->loanApplication->update([
            'status' => 'analysis_failed',
            'analysis_error' => 'Permanent failure after retries: ' . $exception->getMessage(),
        ]);
    }
}
