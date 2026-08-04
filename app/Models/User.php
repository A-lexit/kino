<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use App\Enums\AuthProvider;
use App\Enums\UserRole;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'author_id',
        'user_id',
        'status',
        'is_banned',
        'avatar',
        'provider',
        'provider_id',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];


    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'provider' => AuthProvider::class, // Laravel сам перетворить рядок з БД в Enum
        'role' => UserRole::class,

    ];


    public function films()
    {
        return $this->hasMany(Film::class, 'author_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'user_id', 'id');
    }



    public static function add($fields)
    {
        $user = new static;
        $user->fill($fields);
        $user->save();

        return $user;
    }


    public function edit($fields)
    {
        $this->fill($fields); //name,email

        $this->save();
    }


    public function remove()
    {
        $this->removeAvatar();
        $this->delete();
    }


    public function uploadAvatar($image)
    {
        if($image == null) { return; }

        $this->removeAvatar();

        $filename = Str::random(10) . '.' . $image->extension();
        $image->storeAs('uploads', $filename);
        $this->avatar = $filename;
        $this->save();
    }


    public function removeAvatar()
    {
        if($this->avatar != null)
        {
            Storage::delete('uploads/' . $this->avatar);
        }
    }


    public function ban()
    {
        $this->is_banned = 1;
        $this->save();
    }


    public function unban()
    {
        $this->is_banned = 0;
        $this->save();
    }


    public function toggleBan()
    {
        if ($this->is_banned) {
            return $this->unban();
        }

        return $this->ban();
    }


//Ролі
    // Додати метод-хелпери (зручніше, ніж всюди писати $user->role === UserRole::Admin):
    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isEditor(): bool
    {
        return $this->role === UserRole::Editor;
    }

    public function isViewer(): bool
    {
        return $this->role === UserRole::Viewer;
    }

    public function isStaff(): bool
    {
        return in_array($this->role, UserRole::staffRoles(), true);
    }

// Автоматична синхронізація is_admin <-> role, щоб УВЕСЬ старий код,
// який досі перевіряє $user->is_admin (блейди, інші контролери, які я не бачив),
// продовжував працювати без жодних правок — is_admin тепер завжди
// автоматично відповідає ролі при кожному збереженні моделі.
    protected static function booted(): void
    {
        static::saving(function (User $user) {
            $user->is_admin = $user->role === UserRole::Admin ? 1 : 0;
        });
    }

}
