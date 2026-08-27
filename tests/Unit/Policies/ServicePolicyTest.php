<?php

namespace Tests\Unit\Policies;

use App\Enums\UserRole;
use App\Models\User;
use App\Policies\ServicePolicy;
use Tests\TestCase;

class ServicePolicyTest extends TestCase
{
    protected ServicePolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new ServicePolicy();
    }

    protected function makeUser(UserRole $role): User
    {
        $user = new User();
        $user->role = $role;

        return $user;
    }

    protected function reference(): object
    {
        return new \stdClass();
    }

    public function test_view_any_is_allowed_for_admin_and_viewer(): void
    {
        $this->assertTrue(
            $this->policy->viewAny(
                $this->makeUser(UserRole::Admin)
            )
        );

        $this->assertTrue(
            $this->policy->viewAny(
                $this->makeUser(UserRole::Viewer)
            )
        );

        $this->assertFalse(
            $this->policy->viewAny(
                $this->makeUser(UserRole::Editor)
            )
        );

        $this->assertFalse(
            $this->policy->viewAny(
                $this->makeUser(UserRole::User)
            )
        );
    }

    public function test_view_is_allowed_for_admin_and_viewer(): void
    {
        $reference = $this->reference();

        $this->assertTrue(
            $this->policy->view(
                $this->makeUser(UserRole::Admin),
                $reference
            )
        );

        $this->assertTrue(
            $this->policy->view(
                $this->makeUser(UserRole::Viewer),
                $reference
            )
        );

        $this->assertFalse(
            $this->policy->view(
                $this->makeUser(UserRole::Editor),
                $reference
            )
        );

        $this->assertFalse(
            $this->policy->view(
                $this->makeUser(UserRole::User),
                $reference
            )
        );
    }

    public function test_create_is_allowed_only_for_admin(): void
    {
        $this->assertTrue(
            $this->policy->create(
                $this->makeUser(UserRole::Admin)
            )
        );

        $this->assertFalse(
            $this->policy->create(
                $this->makeUser(UserRole::Editor)
            )
        );

        $this->assertFalse(
            $this->policy->create(
                $this->makeUser(UserRole::Viewer)
            )
        );

        $this->assertFalse(
            $this->policy->create(
                $this->makeUser(UserRole::User)
            )
        );
    }

    public function test_update_is_allowed_only_for_admin(): void
    {
        $reference = $this->reference();

        $this->assertTrue(
            $this->policy->update(
                $this->makeUser(UserRole::Admin),
                $reference
            )
        );

        $this->assertFalse(
            $this->policy->update(
                $this->makeUser(UserRole::Editor),
                $reference
            )
        );

        $this->assertFalse(
            $this->policy->update(
                $this->makeUser(UserRole::Viewer),
                $reference
            )
        );

        $this->assertFalse(
            $this->policy->update(
                $this->makeUser(UserRole::User),
                $reference
            )
        );
    }

    public function test_delete_is_allowed_only_for_admin(): void
    {
        $reference = $this->reference();

        $this->assertTrue(
            $this->policy->delete(
                $this->makeUser(UserRole::Admin),
                $reference
            )
        );

        $this->assertFalse(
            $this->policy->delete(
                $this->makeUser(UserRole::Editor),
                $reference
            )
        );

        $this->assertFalse(
            $this->policy->delete(
                $this->makeUser(UserRole::Viewer),
                $reference
            )
        );

        $this->assertFalse(
            $this->policy->delete(
                $this->makeUser(UserRole::User),
                $reference
            )
        );
    }
}
