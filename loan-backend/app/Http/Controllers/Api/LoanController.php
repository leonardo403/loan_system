<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoanApplicationRequest;
use App\Jobs\AnalyzeLoanWithoutAI;
use App\Models\Client;
use App\Models\LoanApplication;
use App\Services\LoanAnalysisService;
use App\Services\RateLimiter;
use Illuminate\Http\{JsonResponse,Request};
use Illuminate\Support\Facades\{DB,Log};

class LoanController extends Controller
{
    public function __construct(
        protected LoanAnalysisService $analysisService
    ) {}

    public function analyze(LoanApplicationRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $clientEmail = $validated['email'] ?? null;

            // Check rate limit per email or IP
            $rateLimitKey = $clientEmail ?? $request->ip();
            if (RateLimiter::isRateLimited($rateLimitKey, maxRequests: 5, windowSeconds: 3600)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Limite de requisições excedido. Tente novamente mais tarde.',
                    'remaining_requests' => 0
                ], 429);
            }

            DB::beginTransaction();

            // Create or update client
            $client = Client::updateOrCreate(
                ['email' => $clientEmail],
                [
                    'name' => $validated['name'],
                    'income' => $validated['income'],
                    'age' => $validated['age'] ?? null,
                    'employment' => $validated['employment'] ?? null,
                    'credit_history' => $validated['credit_history'] ?? null,
                    'phone' => $validated['phone'] ?? null,
                ]
            );

            // Create loan application with pending status
            $loanApplication = LoanApplication::create([
                'client_id' => $client->id,
                'requested_amount' => $validated['requested_amount'],
                'purpose' => $validated['purpose'] ?? null,
                'status' => 'pending_analysis',
            ]);

            // Record this request for rate limiting
            RateLimiter::recordRequest($rateLimitKey);

            // Dispatch AI analysis job to background queue
            AnalyzeLoanWithoutAI::dispatch($loanApplication, $validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Solicitação de análise recebida. Processando...',
                'data' => [
                    'application_id' => $loanApplication->id,
                    'client_id' => $client->id,
                    'status' => 'pending_analysis',
                    'requested_amount' => $loanApplication->requested_amount,
                    'purpose' => $loanApplication->purpose,   
                ],
                'remaining_requests' => RateLimiter::getRemainingRequests($rateLimitKey, 5)
            ], 202);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao processar análise: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar análise',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $status = $request->input('status');

        $query = LoanApplication::with('client')
            ->orderBy('created_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        $applications = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $applications
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $application = LoanApplication::with('client')->find($id);

        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'Solicitação não encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $application
        ]);
    }

    public function clientApplications(int $clientId): JsonResponse
    {
        $applications = LoanApplication::where('client_id', $clientId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $applications
        ]);
    }

    public function statistics(): JsonResponse
    {
        $stats = [
            'total_applications' => LoanApplication::count(),
            'approved' => LoanApplication::where('approval_status', 'aprovado')->count(),
            'pending' => LoanApplication::where('status', 'pending')->count(),
            'total_amount_requested' => LoanApplication::sum('requested_amount'),
            'total_amount_approved' => LoanApplication::whereNotNull('approved_amount')->sum('approved_amount'),
            'average_score' => LoanApplication::whereNotNull('credit_score')->avg('credit_score'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
