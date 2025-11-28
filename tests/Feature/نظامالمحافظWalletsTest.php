<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * نظام المحافظ (Wallets) Test - Task 2185
 * Category: Testing
 */
class نظامالمحافظWalletsTest extends TestCase
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
