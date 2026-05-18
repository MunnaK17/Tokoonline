<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('beranda'));
    }

    public function test_frontend_eshop_page_can_be_rendered(): void
    {
        $response = $this->get('/beranda');

        $response->assertOk();
        $response->assertSee('Selamat datang di toko online');
    }

    public function test_backend_login_page_can_be_rendered(): void
    {
        $response = $this->get('/backend/login');

        $response->assertOk();
    }
}
