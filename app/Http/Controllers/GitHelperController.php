<?php

namespace App\Http\Controllers;

use App\Models\GitOperation;
use App\Services\GitHelperService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GitHelperController extends Controller
{
    protected $gitService;

    public function __construct(GitHelperService $gitService)
    {
        $this->gitService = $gitService;
    }

    /**
     * Display main dashboard
     */
    public function index()
    {
        try {
            $status = $this->gitService->getStatus();
            $recentOperations = GitOperation::getRecent(10);
            $statistics = [
                'today' => GitOperation::getStatistics('today'),
                'week' => GitOperation::getStatistics('week'),
                'month' => GitOperation::getStatistics('month'),
            ];

            return view('git-helper.index', [
                'status' => $status,
                'recentOperations' => $recentOperations,
                'statistics' => $statistics,
            ]);
        } catch (\Exception $e) {
            Log::error('Git Helper index error: ' . $e->getMessage());
            return view('git-helper.index', [
                'error' => 'فشل في تحميل حالة المستودع: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Get repository status
     */
    public function status()
    {
        try {
            $status = $this->gitService->getStatus();
            return response()->json([
                'success' => true,
                'data' => $status,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get diff for files
     */
    public function diff(Request $request)
    {
        try {
            $file = $request->input('file');
            $diff = $this->gitService->getDiff($file);
            
            return response()->json([
                'success' => true,
                'data' => $diff,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create commit
     */
    public function commit(Request $request)
    {
        try {
            $request->validate([
                'message' => 'required|string|min:3',
                'files' => 'array',
            ]);

            $result = $this->gitService->createCommit(
                $request->input('message'),
                $request->input('files', [])
            );

            return redirect()
                ->route('git-helper.index')
                ->with('success', 'تم إنشاء الـ Commit بنجاح! Hash: ' . substr($result['commit_hash'], 0, 7));
        } catch (\Exception $e) {
            Log::error('Git commit error: ' . $e->getMessage());
            return back()->with('error', 'فشل في إنشاء الـ Commit: ' . $e->getMessage());
        }
    }

    /**
     * Push to remote
     */
    public function push(Request $request)
    {
        try {
            $branch = $request->input('branch');
            $result = $this->gitService->pushToRemote($branch);

            return redirect()
                ->route('git-helper.index')
                ->with('success', 'تم رفع التغييرات إلى GitHub بنجاح!');
        } catch (\Exception $e) {
            Log::error('Git push error: ' . $e->getMessage());
            return back()->with('error', 'فشل في رفع التغييرات: ' . $e->getMessage());
        }
    }

    /**
     * Pull from remote
     */
    public function pull(Request $request)
    {
        try {
            $branch = $request->input('branch');
            $result = $this->gitService->pullFromRemote($branch);

            return redirect()
                ->route('git-helper.index')
                ->with('success', 'تم جلب التحديثات من GitHub بنجاح!');
        } catch (\Exception $e) {
            Log::error('Git pull error: ' . $e->getMessage());
            return back()->with('error', 'فشل في جلب التحديثات: ' . $e->getMessage());
        }
    }

    /**
     * Get branches
     */
    public function branches()
    {
        try {
            $branches = $this->gitService->getBranches();
            
            return view('git-helper.branches', [
                'branches' => $branches,
            ]);
        } catch (\Exception $e) {
            Log::error('Git branches error: ' . $e->getMessage());
            return view('git-helper.branches', [
                'error' => 'فشل في تحميل الفروع: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Create new branch
     */
    public function createBranch(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|regex:/^[a-zA-Z0-9_\-\/]+$/',
                'checkout' => 'boolean',
            ]);

            $result = $this->gitService->createBranch(
                $request->input('name'),
                $request->input('checkout', true)
            );

            return redirect()
                ->route('git-helper.branches')
                ->with('success', 'تم إنشاء الفرع بنجاح: ' . $result['branch']);
        } catch (\Exception $e) {
            Log::error('Git create branch error: ' . $e->getMessage());
            return back()->with('error', 'فشل في إنشاء الفرع: ' . $e->getMessage());
        }
    }

    /**
     * Switch branch
     */
    public function switchBranch(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
            ]);

            $result = $this->gitService->switchBranch($request->input('name'));

            return redirect()
                ->route('git-helper.branches')
                ->with('success', 'تم التبديل إلى الفرع: ' . $result['branch']);
        } catch (\Exception $e) {
            Log::error('Git switch branch error: ' . $e->getMessage());
            return back()->with('error', 'فشل في التبديل إلى الفرع: ' . $e->getMessage());
        }
    }

    /**
     * Get commit history
     */
    public function history(Request $request)
    {
        try {
            $limit = $request->input('limit', 20);
            $branch = $request->input('branch');
            
            $history = $this->gitService->getHistory($limit, $branch);
            $branches = $this->gitService->getBranches();
            
            return view('git-helper.history', [
                'history' => $history,
                'branches' => $branches,
            ]);
        } catch (\Exception $e) {
            Log::error('Git history error: ' . $e->getMessage());
            return view('git-helper.history', [
                'error' => 'فشل في تحميل التاريخ: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Generate smart commit message
     */
    public function generateCommitMessage(Request $request)
    {
        try {
            $result = $this->gitService->generateSmartCommitMessage();
            
            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
            ]);
        } catch (\Exception $e) {
            Log::error('Generate commit message error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Update files',
            ], 500);
        }
    }

    /**
     * Analyze changes with AI
     */
    public function analyzeChanges(Request $request)
    {
        try {
            $result = $this->gitService->analyzeChangesWithAI();
            
            return response()->json([
                'success' => $result['success'],
                'analysis' => $result['analysis'],
            ]);
        } catch (\Exception $e) {
            Log::error('Analyze changes error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get operations history
     */
    public function operations(Request $request)
    {
        try {
            $type = $request->input('type');
            $status = $request->input('status');
            
            $query = GitOperation::query();
            
            if ($type) {
                $query->where('operation_type', $type);
            }
            
            if ($status) {
                $query->where('status', $status);
            }
            
            $operations = $query->orderBy('created_at', 'desc')
                ->paginate(20);
            
            return view('git-helper.operations', [
                'operations' => $operations,
            ]);
        } catch (\Exception $e) {
            Log::error('Git operations error: ' . $e->getMessage());
            return view('git-helper.operations', [
                'error' => 'فشل في تحميل العمليات: ' . $e->getMessage(),
            ]);
        }
    }
}
