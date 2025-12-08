<?php
declare(strict_types=1);

namespace Tests\Core;

use App\Core\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    private $db;
    private $router;

    protected function setUp(): void
    {
        // Mock the database connection to avoid actual DB calls in this unit test.
        // We can create a mock object that does nothing.
        $this->db = $this->createMock(\mysqli::class);
        
        // Instantiate the router with the mocked database
        $this->router = new Router($this->db);
    }

    /**
     * @test
     */
    public function it_resolves_root_route_without_error(): void
    {
        // We need to capture the output because the HomeController will try to render a view.
        ob_start();
        
        // Dispatch the root path and GET method.
        // If this throws an exception (e.g., NotFoundException), the test will fail.
        $this->router->dispatch('/', 'GET');
        
        // Clean the output buffer.
        ob_end_clean();

        // If the dispatch completed without an exception, the test is successful.
        // We add this assertion to make the test's intention clear.
        $this->assertTrue(true, "Router should successfully dispatch the root route.");
    }
    
    /**
     * @test
     */
    public function it_throws_not_found_exception_for_an_undefined_route(): void
    {
        // We expect a NotFoundException to be thrown for a route that doesn't exist.
        $this->expectException(\App\Exceptions\NotFoundException::class);

        $this->router->dispatch('/some-non-existent-route', 'GET');
    }
}
