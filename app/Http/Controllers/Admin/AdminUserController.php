<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRoleEnum;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserController extends Controller
{
    public function index()
    {
        $admins = User::role(UserRoleEnum::ADMIN->value)
            ->latest()
            ->get();

        return view('admin.admins.index', compact('admins'));
    }

    public function create()
    {
        return view('admin.admins.create');
    }

    public function store(Request $request)
    {
        // Приводим номер телефона к единому формату перед валидацией
        $phone = preg_replace('/[^0-9]/', '', $request->phone ?? '');

        if (str_starts_with($phone, '994')) {
            $phone = '+' . $phone;
        } elseif (str_starts_with($phone, '0')) {
            $phone = '+994' . substr($phone, 1);
        }

        // Мержим отформатированный номер обратно в request
        $request->merge(['phone' => $phone]);

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone',
            'password' => 'nullable|string|min:8',
        ];

        // Если пароль указан, требуется подтверждение
        if ($request->filled('password')) {
            $rules['password'] .= '|confirmed';
        }

        $data = $request->validate($rules);

        $plainPassword = $data['password'] ?? Str::random(8);

        DB::transaction(function () use ($data, $plainPassword) {
            // Создаём пользователя
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => Hash::make($plainPassword),
                'active' => true,
                'email_verified_at' => now(),
            ]);

            // Присваиваем роль админа
            $user->assignRole(UserRoleEnum::ADMIN->value);
        });

        $passwordMessage = isset($data['password']) 
            ? 'Администратор создан успешно!' 
            : 'Администратор создан успешно! Пароль: ' . $plainPassword;

        return redirect()->route('dashboard.admins.index')
            ->with('alert', [
                'type' => 'success',
                'message' => $passwordMessage,
            ]);
    }

    public function toggleStatus(Request $request, User $user)
    {
        // Проверяем, что пользователь является админом
        if (!$user->hasRole(UserRoleEnum::ADMIN->value)) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь не является администратором',
            ], 422);
        }

        // Не позволяем деактивировать самого себя
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Вы не можете деактивировать себя',
            ], 422);
        }

        $user->update([
            'active' => !$user->active,
        ]);

        return response()->json([
            'success' => true,
            'active' => $user->active,
            'message' => $user->active ? 'Администратор успешно активирован' : 'Администратор успешно деактивирован',
        ]);
    }
}

