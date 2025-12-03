<?php

namespace App\Services;

use App\Models\GitOperation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use OpenAI\Laravel\Facades\OpenAI;

class GitHelperService
{
    protected $repoPath;

    public function __construct()
    {
        $this->repoPath = base_path();
    }

    /**
     * Get repository status
     */
    public function getStatus()
    {
        try {
            $result = Process::path($this->repoPath)->run('git status --porcelain');
            
            if (!$result->successful()) {
                throw new \Exception('Failed to get git status: ' . $result->errorOutput());
            }

            $output = $result->output();
            $files = $this->parseStatusOutput($output);
            
            // Get current branch
            $branchResult = Process::path($this->repoPath)->run('git branch --show-current');
            $currentBranch = trim($branchResult->output());
            
            // Get remote status
            $remoteResult = Process::path($this->repoPath)->run('git rev-list --left-right --count origin/' . $currentBranch . '...HEAD 2>/dev/null || echo "0\t0"');
            $remoteCounts = explode("\t", trim($remoteResult->output()));
            
            return [
                'files' => $files,
                'current_branch' => $currentBranch,
                'behind' => (int)($remoteCounts[0] ?? 0),
                'ahead' => (int)($remoteCounts[1] ?? 0),
                'total_changes' => count($files),
            ];
        } catch (\Exception $e) {
            Log::error('Git status error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Parse git status output
     */
    protected function parseStatusOutput($output)
    {
        $files = [];
        $lines = explode("\n", trim($output));
        
        foreach ($lines as $line) {
            if (empty($line)) continue;
            
            $status = substr($line, 0, 2);
            $file = trim(substr($line, 3));
            
            $files[] = [
                'file' => $file,
                'status' => $this->getFileStatus($status),
                'status_code' => trim($status),
            ];
        }
        
        return $files;
    }

    /**
     * Get file status description
     */
    protected function getFileStatus($code)
    {
        $code = trim($code);
        return match($code) {
            'M', ' M' => 'modified',
            'A', 'AM' => 'added',
            'D', ' D' => 'deleted',
            'R' => 'renamed',
            'C' => 'copied',
            '??' => 'untracked',
            'MM' => 'modified',
            default => 'unknown',
        };
    }

    /**
     * Get diff for files
     */
    public function getDiff($file = null)
    {
        try {
            $command = $file ? "git diff HEAD -- {$file}" : 'git diff HEAD';
            $result = Process::path($this->repoPath)->run($command);
            
            if (!$result->successful()) {
                throw new \Exception('Failed to get diff: ' . $result->errorOutput());
            }

            return $this->parseDiff($result->output());
        } catch (\Exception $e) {
            Log::error('Git diff error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Parse diff output
     */
    protected function parseDiff($output)
    {
        $lines = explode("\n", $output);
        $diff = [];
        $currentFile = null;
        
        foreach ($lines as $line) {
            if (str_starts_with($line, 'diff --git')) {
                preg_match('/b\/(.+)$/', $line, $matches);
                $currentFile = $matches[1] ?? 'unknown';
                $diff[$currentFile] = [];
            } elseif ($currentFile) {
                $diff[$currentFile][] = $line;
            }
        }
        
        return $diff;
    }

    /**
     * Add files to staging
     */
    public function addFiles($files = [])
    {
        try {
            if (empty($files)) {
                $result = Process::path($this->repoPath)->run('git add .');
            } else {
                $fileList = implode(' ', array_map('escapeshellarg', $files));
                $result = Process::path($this->repoPath)->run("git add {$fileList}");
            }
            
            if (!$result->successful()) {
                throw new \Exception('Failed to add files: ' . $result->errorOutput());
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Git add error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create commit
     */
    public function createCommit($message, $files = [])
    {
        try {
            // Add files
            $this->addFiles($files);
            
            // Create commit
            $escapedMessage = escapeshellarg($message);
            $result = Process::path($this->repoPath)->run("git commit -m {$escapedMessage}");
            
            if (!$result->successful()) {
                throw new \Exception('Failed to create commit: ' . $result->errorOutput());
            }

            // Get commit hash
            $hashResult = Process::path($this->repoPath)->run('git rev-parse HEAD');
            $commitHash = trim($hashResult->output());
            
            // Get current branch
            $branchResult = Process::path($this->repoPath)->run('git branch --show-current');
            $currentBranch = trim($branchResult->output());
            
            // Get stats
            $stats = $this->getCommitStats($commitHash);
            
            // Log operation
            $operation = GitOperation::create([
                'operation_type' => 'commit',
                'description' => 'Created commit: ' . substr($message, 0, 100),
                'files_changed' => $files,
                'lines_added' => $stats['additions'] ?? 0,
                'lines_deleted' => $stats['deletions'] ?? 0,
                'commit_hash' => $commitHash,
                'commit_message' => $message,
                'branch_name' => $currentBranch,
                'author' => $this->getGitUser(),
                'status' => 'success',
            ]);

            return [
                'success' => true,
                'commit_hash' => $commitHash,
                'operation' => $operation,
            ];
        } catch (\Exception $e) {
            Log::error('Git commit error: ' . $e->getMessage());
            
            // Log failed operation
            GitOperation::create([
                'operation_type' => 'commit',
                'description' => 'Failed commit attempt',
                'commit_message' => $message,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            
            throw $e;
        }
    }

    /**
     * Get commit stats
     */
    protected function getCommitStats($commitHash)
    {
        try {
            $result = Process::path($this->repoPath)->run("git show --stat {$commitHash}");
            $output = $result->output();
            
            preg_match('/(\d+) insertions?\(\+\)/', $output, $additions);
            preg_match('/(\d+) deletions?\(-\)/', $output, $deletions);
            
            return [
                'additions' => (int)($additions[1] ?? 0),
                'deletions' => (int)($deletions[1] ?? 0),
            ];
        } catch (\Exception $e) {
            return ['additions' => 0, 'deletions' => 0];
        }
    }

    /**
     * Push to remote
     */
    public function pushToRemote($branch = null)
    {
        try {
            if (!$branch) {
                $branchResult = Process::path($this->repoPath)->run('git branch --show-current');
                $branch = trim($branchResult->output());
            }
            
            $result = Process::path($this->repoPath)->timeout(120)->run("git push origin {$branch}");
            
            if (!$result->successful()) {
                throw new \Exception('Failed to push: ' . $result->errorOutput());
            }

            // Log operation
            $operation = GitOperation::create([
                'operation_type' => 'push',
                'description' => "Pushed branch {$branch} to origin",
                'branch_name' => $branch,
                'author' => $this->getGitUser(),
                'status' => 'success',
            ]);

            return [
                'success' => true,
                'branch' => $branch,
                'operation' => $operation,
            ];
        } catch (\Exception $e) {
            Log::error('Git push error: ' . $e->getMessage());
            
            GitOperation::create([
                'operation_type' => 'push',
                'description' => 'Failed push attempt',
                'branch_name' => $branch,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            
            throw $e;
        }
    }

    /**
     * Pull from remote
     */
    public function pullFromRemote($branch = null)
    {
        try {
            if (!$branch) {
                $branchResult = Process::path($this->repoPath)->run('git branch --show-current');
                $branch = trim($branchResult->output());
            }
            
            $result = Process::path($this->repoPath)->timeout(120)->run("git pull origin {$branch}");
            
            if (!$result->successful()) {
                throw new \Exception('Failed to pull: ' . $result->errorOutput());
            }

            // Log operation
            $operation = GitOperation::create([
                'operation_type' => 'pull',
                'description' => "Pulled branch {$branch} from origin",
                'branch_name' => $branch,
                'author' => $this->getGitUser(),
                'status' => 'success',
            ]);

            return [
                'success' => true,
                'branch' => $branch,
                'operation' => $operation,
            ];
        } catch (\Exception $e) {
            Log::error('Git pull error: ' . $e->getMessage());
            
            GitOperation::create([
                'operation_type' => 'pull',
                'description' => 'Failed pull attempt',
                'branch_name' => $branch,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            
            throw $e;
        }
    }

    /**
     * Get branches
     */
    public function getBranches()
    {
        try {
            // Local branches
            $localResult = Process::path($this->repoPath)->run('git branch');
            $localBranches = $this->parseBranches($localResult->output(), 'local');
            
            // Remote branches
            $remoteResult = Process::path($this->repoPath)->run('git branch -r');
            $remoteBranches = $this->parseBranches($remoteResult->output(), 'remote');
            
            // Current branch
            $currentResult = Process::path($this->repoPath)->run('git branch --show-current');
            $currentBranch = trim($currentResult->output());
            
            return [
                'local' => $localBranches,
                'remote' => $remoteBranches,
                'current' => $currentBranch,
            ];
        } catch (\Exception $e) {
            Log::error('Git branches error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Parse branches output
     */
    protected function parseBranches($output, $type)
    {
        $branches = [];
        $lines = explode("\n", trim($output));
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            $isCurrent = str_starts_with($line, '*');
            $branch = trim(str_replace('*', '', $line));
            
            // Skip HEAD reference
            if (str_contains($branch, 'HEAD ->')) continue;
            
            $branches[] = [
                'name' => $branch,
                'is_current' => $isCurrent,
                'type' => $type,
            ];
        }
        
        return $branches;
    }

    /**
     * Create new branch
     */
    public function createBranch($branchName, $checkout = true)
    {
        try {
            $command = $checkout ? "git checkout -b {$branchName}" : "git branch {$branchName}";
            $result = Process::path($this->repoPath)->run($command);
            
            if (!$result->successful()) {
                throw new \Exception('Failed to create branch: ' . $result->errorOutput());
            }

            // Log operation
            $operation = GitOperation::create([
                'operation_type' => 'branch',
                'description' => "Created branch: {$branchName}",
                'branch_name' => $branchName,
                'author' => $this->getGitUser(),
                'status' => 'success',
                'metadata' => ['action' => 'create', 'checkout' => $checkout],
            ]);

            return [
                'success' => true,
                'branch' => $branchName,
                'operation' => $operation,
            ];
        } catch (\Exception $e) {
            Log::error('Git create branch error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Switch branch
     */
    public function switchBranch($branchName)
    {
        try {
            $result = Process::path($this->repoPath)->run("git checkout {$branchName}");
            
            if (!$result->successful()) {
                throw new \Exception('Failed to switch branch: ' . $result->errorOutput());
            }

            // Log operation
            $operation = GitOperation::create([
                'operation_type' => 'branch',
                'description' => "Switched to branch: {$branchName}",
                'branch_name' => $branchName,
                'author' => $this->getGitUser(),
                'status' => 'success',
                'metadata' => ['action' => 'switch'],
            ]);

            return [
                'success' => true,
                'branch' => $branchName,
                'operation' => $operation,
            ];
        } catch (\Exception $e) {
            Log::error('Git switch branch error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get commit history
     */
    public function getHistory($limit = 20, $branch = null)
    {
        try {
            $branchArg = $branch ? $branch : '';
            $result = Process::path($this->repoPath)->run("git log {$branchArg} --pretty=format:'%H|%an|%ae|%ad|%s' --date=iso -n {$limit}");
            
            if (!$result->successful()) {
                throw new \Exception('Failed to get history: ' . $result->errorOutput());
            }

            return $this->parseHistory($result->output());
        } catch (\Exception $e) {
            Log::error('Git history error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Parse history output
     */
    protected function parseHistory($output)
    {
        $commits = [];
        $lines = explode("\n", trim($output));
        
        foreach ($lines as $line) {
            if (empty($line)) continue;
            
            $parts = explode('|', $line);
            if (count($parts) >= 5) {
                $commits[] = [
                    'hash' => $parts[0],
                    'author' => $parts[1],
                    'email' => $parts[2],
                    'date' => $parts[3],
                    'message' => $parts[4],
                ];
            }
        }
        
        return $commits;
    }

    /**
     * Generate smart commit message using AI
     */
    public function generateSmartCommitMessage($changes = null)
    {
        try {
            if (!$changes) {
                $status = $this->getStatus();
                $diff = $this->getDiff();
                $changes = [
                    'files' => $status['files'],
                    'diff' => $diff,
                ];
            }

            $prompt = $this->buildCommitMessagePrompt($changes);
            
            $response = OpenAI::chat()->create([
                'model' => 'gpt-4.1-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful assistant that generates clear, concise git commit messages following Conventional Commits format.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => 150,
                'temperature' => 0.7,
            ]);

            $message = trim($response->choices[0]->message->content);
            
            return [
                'success' => true,
                'message' => $message,
            ];
        } catch (\Exception $e) {
            Log::error('AI commit message generation error: ' . $e->getMessage());
            
            // Fallback to simple message
            return [
                'success' => false,
                'message' => 'Update files',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Build prompt for commit message generation
     */
    protected function buildCommitMessagePrompt($changes)
    {
        $filesList = collect($changes['files'])->map(function($file) {
            return "- {$file['status']}: {$file['file']}";
        })->join("\n");

        return <<<PROMPT
Generate a clear and concise git commit message for the following changes:

Files changed:
{$filesList}

Please provide a commit message following Conventional Commits format (type: description).
Types: feat, fix, docs, style, refactor, test, chore.
Keep it under 72 characters if possible.
Use English for the commit message.
PROMPT;
    }

    /**
     * Analyze changes with AI
     */
    public function analyzeChangesWithAI($changes = null)
    {
        try {
            if (!$changes) {
                $status = $this->getStatus();
                $changes = $status['files'];
            }

            $prompt = $this->buildAnalysisPrompt($changes);
            
            $response = OpenAI::chat()->create([
                'model' => 'gpt-4.1-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a code analysis expert. Analyze git changes and provide insights.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => 500,
                'temperature' => 0.7,
            ]);

            $analysis = trim($response->choices[0]->message->content);
            
            return [
                'success' => true,
                'analysis' => $analysis,
            ];
        } catch (\Exception $e) {
            Log::error('AI analysis error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'analysis' => 'Unable to analyze changes at this time.',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Build prompt for changes analysis
     */
    protected function buildAnalysisPrompt($changes)
    {
        $filesList = collect($changes)->map(function($file) {
            return "- {$file['status']}: {$file['file']}";
        })->join("\n");

        return <<<PROMPT
Analyze the following git changes and provide:
1. Summary of changes
2. Potential impact
3. Recommendations

Files changed:
{$filesList}

Provide a brief analysis in English.
PROMPT;
    }

    /**
     * Get git user
     */
    protected function getGitUser()
    {
        try {
            $result = Process::path($this->repoPath)->run('git config user.name');
            return trim($result->output()) ?: 'Unknown';
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }
}
