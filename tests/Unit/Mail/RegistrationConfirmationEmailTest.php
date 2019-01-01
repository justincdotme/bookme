<?php

namespace Tests\Unit\Mail;

use App\Core\User;
use App\Mail\UserRegistrationConfirmation;
use Tests\TestCase;

class RegistrationConfirmationEmailTest extends TestCase
{
    /**
     * @test
     */
    public function email_contains_first_and_last_name()
    {
        $user = factory(User::class)->make([
            'first_name' => 'Test',
            'last_name' => 'User'
        ]);

        $email = new UserRegistrationConfirmation($user);
        $rendered = $this->renderMailable($email);

        $this->assertContains($user->first_name, $rendered);
        $this->assertContains($user->last_name, $rendered);
    }

    /**
     * @test
     */
    public function email_has_subject()
    {
        $user = factory(User::class)->make();

        $email = new UserRegistrationConfirmation($user);

        $this->assertEquals('Registration Confirmation', $email->build()->subject);
    }
}
