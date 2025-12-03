<?php

namespace App\Analyzers;

/**
 * Simple Complexity Analyzer without external dependencies
 * Uses regex and token parsing for basic complexity analysis
 */
class SimpleComplexityAnalyzer
{
    /**
     * Analyze file complexity
     */
    public function analyzeFile(string $filePath): array
    {
        if (!file_exists($filePath)) {
            return [];
        }

        $code = file_get_contents($filePath);
        $tokens = token_get_all($code);
        
        return [
            'file' => $filePath,
            'functions' => $this->extractFunctions($code, $tokens),
            'classes' => $this->extractClasses($code, $tokens),
            'cyclomatic_complexity' => $this->calculateComplexity($code),
            'lines' => $this->countLines($code),
        ];
    }

    /**
     * Analyze directory
     */
    public function analyzeDirectory(string $directory): array
    {
        $files = $this->getPhpFiles($directory);
        $results = [];

        foreach ($files as $file) {
            $results[] = $this->analyzeFile($file);
        }

        return $this->aggregateResults($results);
    }

    /**
     * Extract functions from code
     */
    private function extractFunctions(string $code, array $tokens): array
    {
        $functions = [];
        $inFunction = false;
        $functionName = '';
        $braceLevel = 0;
        $functionStart = 0;
        $currentClass = null;

        for ($i = 0; $i < count($tokens); $i++) {
            $token = $tokens[$i];

            // Detect class
            if (is_array($token) && $token[0] === T_CLASS) {
                for ($j = $i + 1; $j < count($tokens); $j++) {
                    if (is_array($tokens[$j]) && $tokens[$j][0] === T_STRING) {
                        $currentClass = $tokens[$j][1];
                        break;
                    }
                }
            }

            // Detect function
            if (is_array($token) && $token[0] === T_FUNCTION) {
                $inFunction = true;
                $functionStart = $token[2];
                
                // Get function name
                for ($j = $i + 1; $j < count($tokens); $j++) {
                    if (is_array($tokens[$j]) && $tokens[$j][0] === T_STRING) {
                        $functionName = $tokens[$j][1];
                        if ($currentClass) {
                            $functionName = $currentClass . '::' . $functionName;
                        }
                        break;
                    }
                }
            }

            // Track braces
            if ($inFunction) {
                if ($token === '{') {
                    $braceLevel++;
                } elseif ($token === '}') {
                    $braceLevel--;
                    
                    if ($braceLevel === 0) {
                        $functionEnd = is_array($token) ? $token[2] : $functionStart + 10;
                        $functionCode = $this->extractCodeBetweenLines($code, $functionStart, $functionEnd);
                        
                        $functions[] = [
                            'name' => $functionName,
                            'cyclomatic_complexity' => $this->calculateComplexity($functionCode),
                            'lines' => $functionEnd - $functionStart,
                            'parameters' => $this->countParameters($functionCode),
                        ];
                        
                        $inFunction = false;
                        $functionName = '';
                    }
                }
            }
        }

        return $functions;
    }

    /**
     * Extract classes from code
     */
    private function extractClasses(string $code, array $tokens): array
    {
        $classes = [];
        
        for ($i = 0; $i < count($tokens); $i++) {
            $token = $tokens[$i];
            
            if (is_array($token) && $token[0] === T_CLASS) {
                $className = '';
                $classStart = $token[2];
                
                // Get class name
                for ($j = $i + 1; $j < count($tokens); $j++) {
                    if (is_array($tokens[$j]) && $tokens[$j][0] === T_STRING) {
                        $className = $tokens[$j][1];
                        break;
                    }
                }
                
                // Count methods and properties
                $braceLevel = 0;
                $methods = 0;
                $properties = 0;
                $classEnd = $classStart;
                
                for ($j = $i; $j < count($tokens); $j++) {
                    if ($tokens[$j] === '{') {
                        $braceLevel++;
                    } elseif ($tokens[$j] === '}') {
                        $braceLevel--;
                        if ($braceLevel === 0) {
                            $classEnd = is_array($tokens[$j]) ? $tokens[$j][2] : $classStart + 50;
                            break;
                        }
                    }
                    
                    if (is_array($tokens[$j])) {
                        if ($tokens[$j][0] === T_FUNCTION) {
                            $methods++;
                        } elseif (in_array($tokens[$j][0], [T_PUBLIC, T_PROTECTED, T_PRIVATE]) && 
                                  isset($tokens[$j + 2]) && is_array($tokens[$j + 2]) && 
                                  $tokens[$j + 2][0] === T_VARIABLE) {
                            $properties++;
                        }
                    }
                }
                
                $classes[] = [
                    'name' => $className,
                    'lines' => $classEnd - $classStart,
                    'methods' => $methods,
                    'properties' => $properties,
                ];
            }
        }
        
        return $classes;
    }

    /**
     * Calculate cyclomatic complexity
     */
    private function calculateComplexity(string $code): int
    {
        $complexity = 1; // Base complexity
        
        // Count decision points
        $patterns = [
            '/\bif\s*\(/',           // if statements
            '/\belseif\s*\(/',       // elseif statements
            '/\bfor\s*\(/',          // for loops
            '/\bforeach\s*\(/',      // foreach loops
            '/\bwhile\s*\(/',        // while loops
            '/\bcase\s+/',           // case statements
            '/\bcatch\s*\(/',        // catch blocks
            '/\&\&/',                // logical AND
            '/\|\|/',                // logical OR
            '/\?.*:/',               // ternary operator
        ];
        
        foreach ($patterns as $pattern) {
            $complexity += preg_match_all($pattern, $code);
        }
        
        return $complexity;
    }

    /**
     * Count function parameters
     */
    private function countParameters(string $code): int
    {
        if (preg_match('/function\s+\w+\s*\((.*?)\)/s', $code, $matches)) {
            $params = trim($matches[1]);
            if (empty($params)) {
                return 0;
            }
            return substr_count($params, ',') + 1;
        }
        return 0;
    }

    /**
     * Extract code between lines
     */
    private function extractCodeBetweenLines(string $code, int $start, int $end): string
    {
        $lines = explode("\n", $code);
        $extracted = array_slice($lines, $start - 1, $end - $start + 1);
        return implode("\n", $extracted);
    }

    /**
     * Count lines in code
     */
    private function countLines(string $code): array
    {
        $lines = explode("\n", $code);
        $total = count($lines);
        $blank = 0;
        $comment = 0;
        $logical = 0;

        $inBlockComment = false;

        foreach ($lines as $line) {
            $trimmed = trim($line);

            if (empty($trimmed)) {
                $blank++;
                continue;
            }

            // Block comments
            if (str_contains($trimmed, '/*')) {
                $inBlockComment = true;
            }

            if ($inBlockComment) {
                $comment++;
                if (str_contains($trimmed, '*/')) {
                    $inBlockComment = false;
                }
                continue;
            }

            // Line comments
            if (str_starts_with($trimmed, '//') || str_starts_with($trimmed, '#')) {
                $comment++;
                continue;
            }

            $logical++;
        }

        return [
            'total' => $total,
            'logical' => $logical,
            'blank' => $blank,
            'comment' => $comment,
        ];
    }

    /**
     * Get PHP files from directory
     */
    private function getPhpFiles(string $directory): array
    {
        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    /**
     * Aggregate results from multiple files
     */
    private function aggregateResults(array $results): array
    {
        $totalFiles = count($results);
        $totalFunctions = 0;
        $totalClasses = 0;
        $totalLines = 0;
        $logicalLines = 0;
        $commentLines = 0;
        $blankLines = 0;
        $complexities = [];
        $functionSizes = [];
        $classSizes = [];

        foreach ($results as $result) {
            if (isset($result['error'])) {
                continue;
            }

            $totalFunctions += count($result['functions'] ?? []);
            $totalClasses += count($result['classes'] ?? []);

            if (isset($result['lines'])) {
                $totalLines += $result['lines']['total'];
                $logicalLines += $result['lines']['logical'];
                $commentLines += $result['lines']['comment'];
                $blankLines += $result['lines']['blank'];
            }

            foreach ($result['functions'] ?? [] as $function) {
                $complexities[] = $function['cyclomatic_complexity'] ?? 1;
                $functionSizes[] = $function['lines'] ?? 0;
            }

            foreach ($result['classes'] ?? [] as $class) {
                $classSizes[] = $class['lines'] ?? 0;
            }
        }

        return [
            'total_files' => $totalFiles,
            'total_functions' => $totalFunctions,
            'total_classes' => $totalClasses,
            'total_lines' => $totalLines,
            'logical_lines' => $logicalLines,
            'comment_lines' => $commentLines,
            'blank_lines' => $blankLines,
            'avg_cyclomatic_complexity' => !empty($complexities) ? round(array_sum($complexities) / count($complexities), 2) : 0,
            'max_cyclomatic_complexity' => !empty($complexities) ? max($complexities) : 0,
            'avg_function_size' => !empty($functionSizes) ? round(array_sum($functionSizes) / count($functionSizes), 2) : 0,
            'max_function_size' => !empty($functionSizes) ? max($functionSizes) : 0,
            'avg_class_size' => !empty($classSizes) ? round(array_sum($classSizes) / count($classSizes), 2) : 0,
            'max_class_size' => !empty($classSizes) ? max($classSizes) : 0,
            'documentation_percentage' => $totalLines > 0 ? round(($commentLines / $totalLines) * 100, 2) : 0,
        ];
    }
}
