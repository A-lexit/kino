<?php

namespace Tests\Unit\Enums;

use App\Enums\UserRole;
use Tests\TestCase;

class UserRoleTest extends TestCase
{
    public function test_labels_are_correct(): void
    {
        $this->assertSame(
            'Адміністратор',
            UserRole::Admin->label()
        );

        $this->assertSame(
            'Редактор',
            UserRole::Editor->label()
        );

        $this->assertSame(
            'Переглядач',
            UserRole::Viewer->label()
        );

        $this->assertSame(
            'Користувач сайту',
            UserRole::User->label()
        );
    }

    public function test_staff_roles_returns_only_staff_roles(): void
    {
        $staffRoles = UserRole::staffRoles();

        $this->assertCount(3, $staffRoles);

        $this->assertContains(UserRole::Admin, $staffRoles);
        $this->assertContains(UserRole::Editor, $staffRoles);
        $this->assertContains(UserRole::Viewer, $staffRoles);

        $this->assertNotContains(UserRole::User, $staffRoles);
    }
}
