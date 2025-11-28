<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * نظام التقارير المالية (Financial Reports) Test - Task 2485
 * Category: Testing
 */
class نظامالتقاريرالماليةFinancialReportsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test basic functionality
     */
    public function test_basic_functionality(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }
}
