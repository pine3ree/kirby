<?php

namespace Kirby\Cms;

class UserTestForceLocked extends User
{
    public function isLocked(): bool
    {
        return true;
    }
}

class UserForPanelTest extends TestCase
{
    public function testOptions()
    {
        $user = new User([
            'email' => 'test@getkirby.com',
        ]);

        $user->kirby()->impersonate('kirby');

        $expected = [
            'create'         => true,
            'changeEmail'    => true,
            'changeLanguage' => true,
            'changeName'     => true,
            'changePassword' => true,
            'changeRole'     => false, // just one role
            'delete'         => true,
            'update'         => true,
        ];

        $this->assertEquals($expected, $user->panel()->options());
    }

    public function testOptionsWithLockedUser()
    {
        $user = new UserTestForceLocked([
            'email' => 'test@getkirby.com',
        ]);

        $user->kirby()->impersonate('kirby');

        // without override
        $expected = [
            'create'         => false,
            'changeEmail'    => false,
            'changeLanguage' => false,
            'changeName'     => false,
            'changePassword' => false,
            'changeRole'     => false,
            'delete'         => false,
            'update'         => false,
        ];

        $this->assertEquals($expected, $user->panel()->options());

        // with override
        $expected = [
            'create'         => false,
            'changeEmail'    => true,
            'changeLanguage' => false,
            'changeName'     => false,
            'changePassword' => false,
            'changeRole'     => false,
            'delete'         => false,
            'update'         => false,
        ];

        $this->assertEquals($expected, $user->panel()->options(['changeEmail']));
    }
}
