<?php

namespace Tests\Feature\Resources;

use App\Http\Resources\V1\LoginResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Tests\TestCase;

class LoginResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_resource_to_Array()
    {
        // Create a user
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => bcrypt('secret'),
        ]);

        // Load the user resource
        $userResource = new LoginResource($user);

        // Transform the resource into an array
        $userArray = $userResource->toArray(new Request());

        // Assert that the array contains the expected values
        $this->assertEquals($user->id, $userArray['userId']);
        $this->assertEquals(strtoupper($user->name), $userArray['name']);
        $this->assertEquals($user->email, $userArray['email']);

        // Ensure that the access tokens are present
        $this->assertArrayHasKey('accessToken', $userArray);
        $this->assertArrayHasKey('updateToken', $userArray);
        $this->assertArrayHasKey('basic', $userArray);

        // Optionally, you can further test the structure of the access tokens
        $this->assertIsString($userArray['accessToken']);
        $this->assertIsString($userArray['updateToken']);
        $this->assertIsString($userArray['basic']);
    }
}
