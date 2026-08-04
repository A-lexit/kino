<?php
namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\NameRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Media\ImageMedia;

class UserController extends Controller
{
    protected $imageMedia;

    public function __construct(ImageMedia $imageMedia)
    {
        $this->imageMedia = $imageMedia;
    }

    public function index()
    {
        $users = User::paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function toggle($id)
    {
        $user = User::findOrFail($id);

        if (auth()->id() == $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Ви не можете заблокувати самого себе!'
            ], 422);
        }

        $user->toggleBan();

        return response()->json([
            'success' => true,
            'is_banned' => $user->is_banned,
            'message' => 'Статус користувача змінено.'
        ]);
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'role' => 'required|in:admin,editor,viewer,user',
            'password' => 'required|min:8', // при СТВОРЕННІ пароль обов'язковий
        ]);

        $user = User::add($request->only(['name', 'email', 'password', 'role']));

        if ($request->hasFile('avatar')) {
            $user->avatar = $this->imageMedia->upload($request->file('avatar'), 'avatars');
            $user->save();
        }

        return redirect()->route('admin.users.index')->with('success', 'Користувача додано');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(NameRequest $request, $id)
    {
        $request->validate([
            'role' => 'required|in:admin,editor,viewer,user',
            'password' => 'nullable|min:8', // при РЕДАГУВАННІ пароль опційний
        ]);

        $user = User::findOrFail($id);
        $data = $request->only(['name', 'email', 'password', 'role']);

        // КРИТИЧНО: порожній пароль НЕ повинен перезаписувати існуючий.
        // Без цього кожне редагування користувача без заповненого пароля
        // тихо ламало його логін (хешувався порожній рядок).
        if (empty($data['password'])) {
            unset($data['password']);
        }

        if (auth()->id() == $user->id) {
            unset($data['role']);
        }

        if ($user->role === UserRole::Admin && ($data['role'] ?? null) !== UserRole::Admin->value) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Понизити роль адміністратора можна тільки напряму через базу даних.');
        }

        $user->edit($data);

        if ($request->hasFile('avatar')) {
            $this->imageMedia->delete($user->avatar);
            $user->avatar = $this->imageMedia->upload($request->file('avatar'), 'avatars');
            $user->save();
        }

        return redirect()->route('admin.users.index')->with('success', 'Зміни збережені');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if (auth()->id() == $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Ви не можете видалити самого себе!'
            ], 422);
        }

        if ($user->role === UserRole::Admin) {
            return response()->json([
                'success' => false,
                'message' => 'Адміністратора не можна видалити через інтерфейс. Це можна зробити тільки напряму через базу даних.'
            ], 422);
        }

        $this->imageMedia->delete($user->avatar);
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Користувача успішно видалено.'
        ]);
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
            'action' => 'required|string|in:delete'
        ]);

        $ids = $request->input('ids');

        if (in_array(auth()->id(), $ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Масове видалення скасовано. Ви не можете видалити самого себе!'
            ], 422);
        }

        $adminInBatch = User::whereIn('id', $ids)->where('role', UserRole::Admin->value)->exists();

        if ($adminInBatch) {
            return response()->json([
                'success' => false,
                'message' => 'Серед вибраних є адміністратор(и) — масове видалення скасовано.'
            ], 422);
        }

        foreach ($ids as $id) {
            $user = User::find($id);
            if ($user) {
                $this->imageMedia->delete($user->avatar);
                $user->delete();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Вибраних користувачів успішно видалено.'
        ]);
    }

}
