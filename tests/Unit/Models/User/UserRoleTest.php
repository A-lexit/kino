<?php
namespace Tests\Unit\Enums;

use App\Enums\UserRole;
use PHPUnit\Framework\TestCase;

class UserRoleTest extends TestCase
{
    public function test_enum_contains_expected_values(): void
    {
        $this->assertSame('admin', UserRole::Admin->value);
        $this->assertSame('editor', UserRole::Editor->value);
        $this->assertSame('viewer', UserRole::Viewer->value);
        $this->assertSame('user', UserRole::User->value);
    }


    public function test_try_from_returns_correct_enum(): void
    {
        $this->assertSame(UserRole::Admin, UserRole::tryFrom('admin'));
        $this->assertSame(UserRole::Editor, UserRole::tryFrom('editor'));
        $this->assertSame(UserRole::Viewer, UserRole::tryFrom('viewer'));
        $this->assertSame(UserRole::User, UserRole::tryFrom('user'));
    }


    public function test_try_from_returns_null_for_invalid_value(): void
    {
        $this->assertNull(
            UserRole::tryFrom('invalid')
        );
    }


    public function test_label_returns_expected_values(): void
    {
        $this->assertSame('Адміністратор', UserRole::Admin->label());
        $this->assertSame('Редактор', UserRole::Editor->label());
        $this->assertSame('Переглядач', UserRole::Viewer->label());
        $this->assertSame('Користувач сайту', UserRole::User->label());
    }


    public function test_staff_roles_returns_expected_roles(): void
    {
        $this->assertSame(
            [
                UserRole::Admin,
                UserRole::Editor,
                UserRole::Viewer,
            ],
            UserRole::staffRoles()
        );
    }

}
