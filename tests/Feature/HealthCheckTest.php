<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    /**
     * Test the application health endpoint.
     */
    public function test_application_health_check(): void
    {
        $response = $this->get('/health');
        
        $response->assertStatus(200);
    }

    /**
     * Test the application is running.
     */
    public function test_application_is_running(): void
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
    }
}
