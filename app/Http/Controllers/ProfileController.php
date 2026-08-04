<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Media\ImageMedia;

class ProfileController extends Controller
{
    protected $imageMedia;

    // Впроваджуємо сервіс обробки зображень
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

        $data = $request->only(['name', 'email', 'password']);

        // Якщо поле пароля залишили порожнім — не чіпаємо існуючий пароль
        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $user->edit($data);

        // Обробка файлу через новий сервіс
        if ($request->hasFile('avatar')) {
            // Видаляємо попередній файл фізично з сервера
            $this->imageMedia->delete($user->avatar);
            // Завантажуємо новий та отримуємо правильний шлях
            $user->avatar = $this->imageMedia->upload($request->file('avatar'), 'avatars');
            $user->save();
        }

        return redirect()->back()->with('success', 'Зміни збережені');
    }

}
