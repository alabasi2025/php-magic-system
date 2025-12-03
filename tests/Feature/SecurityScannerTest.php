<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\SecurityScanner;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Security Scanner Test
 * 
 * اختبارات شاملة لفاحص الأمان
 * 
 * @version 3.14.0
 */
class SecurityScannerTest extends TestCase
{
    private SecurityScanner $scanner;

    protected function setUp(): void
    {
        parent::setUp();
        $this->scanner = new SecurityScanner();
    }

    /**
     * اختبار فحص SQL Injection
     */
    public function test_detects_sql_injection_vulnerabilities()
    {
        $code = '<?php
        $id = $_GET["id"];
        DB::raw("SELECT * FROM users WHERE id = " . $id);
        ';

        $results = $this->scanner->scan($code);

        $this->assertGreaterThan(0, $results['total_issues']);
        $this->assertGreaterThan(0, $results['critical_count']);
        
        $sqlInjectionFound = false;
        foreach ($results['issues'] as $issue) {
            if ($issue['category'] === 'SQL Injection') {
                $sqlInjectionFound = true;
                break;
            }
        }
        
        $this->assertTrue($sqlInjectionFound, 'Should detect SQL Injection vulnerability');
    }

    /**
     * اختبار فحص XSS
     */
    public function test_detects_xss_vulnerabilities()
    {
        $code = '<?php
        echo $_GET["name"];
        ';

        $results = $this->scanner->scan($code);

        $this->assertGreaterThan(0, $results['total_issues']);
        
        $xssFound = false;
        foreach ($results['issues'] as $issue) {
            if ($issue['category'] === 'XSS') {
                $xssFound = true;
                break;
            }
        }
        
        $this->assertTrue($xssFound, 'Should detect XSS vulnerability');
    }

    /**
     * اختبار فحص CSRF
     */
    public function test_detects_csrf_vulnerabilities()
    {
        $code = '<form method="post" action="/submit">
            <input type="text" name="data">
            <button type="submit">Submit</button>
        </form>';

        $results = $this->scanner->scan($code);

        $csrfFound = false;
        foreach ($results['issues'] as $issue) {
            if ($issue['category'] === 'CSRF') {
                $csrfFound = true;
                break;
            }
        }
        
        $this->assertTrue($csrfFound, 'Should detect missing CSRF protection');
    }

    /**
     * اختبار فحص الصلاحيات
     */
    public function test_detects_permission_issues()
    {
        $code = '<?php
        public function delete($id) {
            $user = User::find($id);
            $user->delete();
        }
        ';

        $results = $this->scanner->scan($code);

        $permissionFound = false;
        foreach ($results['issues'] as $issue) {
            if ($issue['category'] === 'Permissions') {
                $permissionFound = true;
                break;
            }
        }
        
        $this->assertTrue($permissionFound, 'Should detect missing permission check');
    }

    /**
     * اختبار فحص رفع الملفات
     */
    public function test_detects_file_upload_issues()
    {
        $code = '<?php
        $request->file("upload")->store("uploads");
        ';

        $results = $this->scanner->scan($code);

        $fileUploadFound = false;
        foreach ($results['issues'] as $issue) {
            if ($issue['category'] === 'File Upload') {
                $fileUploadFound = true;
                break;
            }
        }
        
        $this->assertTrue($fileUploadFound, 'Should detect file upload without validation');
    }

    /**
     * اختبار فحص المصادقة
     */
    public function test_detects_authentication_issues()
    {
        $code = '<?php
        $password = md5($_POST["password"]);
        ';

        $results = $this->scanner->scan($code);

        $authFound = false;
        foreach ($results['issues'] as $issue) {
            if ($issue['category'] === 'Authentication') {
                $authFound = true;
                break;
            }
        }
        
        $this->assertTrue($authFound, 'Should detect weak password hashing');
    }

    /**
     * اختبار فحص التشفير
     */
    public function test_detects_encryption_issues()
    {
        $code = '<?php
        $api_key = "sk-1234567890abcdef";
        ';

        $results = $this->scanner->scan($code);

        $encryptionFound = false;
        foreach ($results['issues'] as $issue) {
            if ($issue['category'] === 'Encryption') {
                $encryptionFound = true;
                break;
            }
        }
        
        $this->assertTrue($encryptionFound, 'Should detect hardcoded API key');
    }

    /**
     * اختبار فحص التحقق من المدخلات
     */
    public function test_detects_input_validation_issues()
    {
        $code = '<?php
        $data = $_POST["data"];
        ';

        $results = $this->scanner->scan($code);

        $validationFound = false;
        foreach ($results['issues'] as $issue) {
            if ($issue['category'] === 'Input Validation') {
                $validationFound = true;
                break;
            }
        }
        
        $this->assertTrue($validationFound, 'Should detect missing input validation');
    }

    /**
     * اختبار حساب درجة الأمان
     */
    public function test_calculates_security_score()
    {
        $safeCode = '<?php
        namespace App\Http\Controllers;
        
        class SafeController extends Controller {
            public function index() {
                return view("home");
            }
        }
        ';

        $results = $this->scanner->scan($safeCode);

        $this->assertEquals(100, $results['score'], 'Safe code should have score of 100');
    }

    /**
     * اختبار تخصيص خيارات الفحص
     */
    public function test_custom_scan_options()
    {
        $code = '<?php
        echo $_GET["name"];
        DB::raw("SELECT * FROM users WHERE id = " . $_GET["id"]);
        ';

        // فحص XSS فقط
        $results = $this->scanner->scan($code, [
            'scans' => [
                'sql_injection' => false,
                'xss' => true,
                'csrf' => false,
                'permissions' => false,
                'file_upload' => false,
                'authentication' => false,
                'encryption' => false,
                'input_validation' => false,
            ]
        ]);

        $hasOnlyXSS = true;
        foreach ($results['issues'] as $issue) {
            if ($issue['category'] !== 'XSS') {
                $hasOnlyXSS = false;
                break;
            }
        }

        $this->assertTrue($hasOnlyXSS, 'Should only detect XSS when other scans are disabled');
    }

    /**
     * اختبار الحصول على التوصيات
     */
    public function test_get_recommendations()
    {
        $recommendations = $this->scanner->getRecommendations();

        $this->assertIsArray($recommendations);
        $this->assertArrayHasKey('sql_injection', $recommendations);
        $this->assertArrayHasKey('xss', $recommendations);
        $this->assertArrayHasKey('csrf', $recommendations);
        $this->assertArrayHasKey('permissions', $recommendations);
        
        foreach ($recommendations as $category => $data) {
            $this->assertArrayHasKey('title', $data);
            $this->assertArrayHasKey('tips', $data);
            $this->assertIsArray($data['tips']);
        }
    }

    /**
     * اختبار كود فارغ
     */
    public function test_handles_empty_code()
    {
        $results = $this->scanner->scan('');

        $this->assertEquals(0, $results['total_issues']);
        $this->assertEquals(100, $results['score']);
    }

    /**
     * اختبار كود معقد
     */
    public function test_handles_complex_code()
    {
        $code = '<?php
        namespace App\Http\Controllers;
        
        use App\Models\User;
        use Illuminate\Http\Request;
        
        class UserController extends Controller
        {
            public function show($id)
            {
                $user = User::find($id);
                
                if (!$user) {
                    return redirect()->back();
                }
                
                return view("user.profile", compact("user"));
            }
            
            public function update(Request $request, $id)
            {
                $user = User::find($id);
                $user->update($request->all());
                
                return redirect()->back();
            }
            
            public function search(Request $request)
            {
                $query = $request->input("q");
                $users = DB::raw("SELECT * FROM users WHERE name LIKE \'%" . $query . "%\'");
                
                return view("users.search", compact("users"));
            }
        }
        ';

        $results = $this->scanner->scan($code);

        $this->assertGreaterThan(0, $results['total_issues']);
        $this->assertLessThan(100, $results['score']);
    }
}
