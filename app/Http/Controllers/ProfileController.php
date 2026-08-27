<?php
namespace App\Http\Controllers;

use App\Media\ImageMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    protected $imageMedia;

    public function __construct(ImageMedia $imageMedia)
    {
        $this->imageMedia = $imageMedia;
    }

    public function edit()
    {
        $user = Auth::user();

        return view('users.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'avatar' => 'nullable|image',
        ]);

        $data = $request->only([
            'name',
            'email',
            'password',
        ]);

        // Якщо пароль не вказаний — залишаємо старий.
        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $user->edit($data);

        // Обробка нового аватара.
        if ($request->hasFile('avatar')) {
            // Видаляємо попередній файл фізично з сервера.
            $this->imageMedia->delete($user->avatar);

            // Завантажуємо новий аватар.
            $user->avatar = $this->imageMedia->upload(
                $request->file('avatar'),
                'avatars'
            );

            $user->save();
        }

        return redirect()
            ->back()
            ->with('success', 'Зміни збережені');
    }

}
