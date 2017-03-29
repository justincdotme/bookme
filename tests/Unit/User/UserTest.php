<?php

namespace Tests\Unit\User;

use App\Core\Role;
use App\Core\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * @test
     */
    public function it_can_check_if_user_is_admin()
    {
        $user = factory(User::class)->states(['admin'])->make();

        $this->assertTrue($user->isAdmin());
        $this->assertFalse($user->isStandard());
    }

    /**
     * @test
     */
    public function it_can_check_if_user_is_standard()
    {
        $user = factory(User::class)->states(['standard'])->make();

        $this->assertTrue($user->isStandard());
        $this->assertFalse($user->isAdmin());
    }

    /**
     * @test
     */
    public function it_can_get_correct_name_of_role()
    {
        $standardRole = factory(Role::class)->states(['standard'])->make();
        $adminRole = factory(Role::class)->states(['admin'])->make();

        $standardUser = factory(User::class)
            ->states(['standard'])
            ->make()
            ->setRelation('role', $standardRole);

        $adminUser = factory(User::class)
            ->states(['admin'])
            ->make()
            ->setRelation('role', $adminRole);

        $this->assertEquals('standard', $standardUser->getRole());
        $this->assertEquals('admin', $adminUser->getRole());
    }

    /**
     * @test
     */
    public function it_can_create_a_standard_user()
    {
        $user = User::createStandardUser([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test.user@justinc.me',
            'password' => 'abc123',
        ]);

        $this->assertEquals(1, $user->fresh()->role_id);
    }
}
