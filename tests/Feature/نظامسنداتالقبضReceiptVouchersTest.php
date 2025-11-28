<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * نظام سندات القبض (Receipt Vouchers) Test - Task 2285
 * Category: Testing
 */
class نظامسنداتالقبضReceiptVouchersTest extends TestCase
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
