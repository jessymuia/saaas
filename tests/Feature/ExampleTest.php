<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_the_admin_login_page_returns_a_successful_response(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
    }

    public function test_the_app_login_page_returns_a_successful_response(): void
    {
        $response = $this->get('/app/login');

        $response->assertStatus(200);
    }
}
