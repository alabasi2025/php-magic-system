<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * نظام سندات الصرف (Payment Vouchers) Test - Task 2385
 * Category: Testing
 */
class نظامسنداتالصرفPaymentVouchersTest extends TestCase
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
