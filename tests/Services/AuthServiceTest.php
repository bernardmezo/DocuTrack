<?php
declare(strict_types=1);

namespace Tests\Services;

use App\Services\AuthService;
use App\Models\LoginModel;
use PHPUnit\Framework\TestCase;

class AuthServiceTest extends TestCase
{
    private $loginModelMock;
    private $authService;

    protected function setUp(): void
    {
        // Create a mock for LoginModel. We will control its behavior.
        $this->loginModelMock = $this->createMock(LoginModel::class);
        
        // AuthService depends on a database connection, but since we mock the model
        // that uses the DB, we can pass null or a generic mock for the DB connection itself.
        // The model mock will intercept calls before they reach the database.
        $dbMock = $this->createMock(\mysqli::class);

        // Instantiate AuthService with the mocked model.
        $this->authService = new AuthService($dbMock);

        // Use Reflection to replace the internal model with our mock
        $reflection = new \ReflectionClass(AuthService::class);
        $property = $reflection->getProperty('loginModel');
        $property->setAccessible(true);
        $property->setValue($this->authService, $this->loginModelMock);
    }

    /**
     * @test
     */
    public function login_succeeds_with_valid_credentials(): void
    {
        $email = 'test@example.com';
        $password = 'password123';
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $fakeUser = [
            'userId' => 1,
            'nama' => 'Test User',
            'password' => $hashedPassword,
            'namaRole' => 'admin'
        ];

        // Configure the mock: when getUserByEmail is called with $email, return our fake user.
        $this->loginModelMock->method('getUserByEmail')
                             ->with($email)
                             ->willReturn($fakeUser);

        // Call the method we are testing
        $result = $this->authService->login($email, $password);

        // Assertions: Check if the result is as expected
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('user', $result);
        $this->assertEquals(1, $result['user']['userId']);
    }

    /**
     * @test
     */
    public function login_fails_with_invalid_password(): void
    {
        $email = 'test@example.com';
        $correctPassword = 'password123';
        $wrongPassword = 'wrong_password';
        $hashedPassword = password_hash($correctPassword, PASSWORD_DEFAULT);
        
        $fakeUser = [
            'userId' => 1,
            'nama' => 'Test User',
            'password' => $hashedPassword,
            'namaRole' => 'admin'
        ];

        // Configure the mock to return the user
        $this->loginModelMock->method('getUserByEmail')
                             ->with($email)
                             ->willReturn($fakeUser);

        // Call with the WRONG password
        $result = $this->authService->login($email, $wrongPassword);

        // Assertions
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Password salah.', $result['message']);
    }

    /**
     * @test
     */
    public function login_fails_with_non_existent_user(): void
    {
        $email = 'notfound@example.com';
        $password = 'anypassword';

        // Configure the mock: when getUserByEmail is called, return null.
        $this->loginModelMock->method('getUserByEmail')
                             ->with($email)
                             ->willReturn(null);

        // Call the service
        $result = $this->authService->login($email, $password);

        // Assertions
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Email tidak terdaftar.', $result['message']);
    }
}