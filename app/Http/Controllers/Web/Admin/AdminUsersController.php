<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminRole;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AdminUsersController extends Controller
{
    public function index()
    {
        $users = AdminUser::with('roles')->orderBy('name')->paginate(20);
        $roles = AdminRole::orderBy('name')->get();
        return view('admin.users.index', compact('users','roles'));
    }

    public function create()
    {
        $roles = AdminRole::orderBy('name')->get();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','unique:admin_users,email'],
            'password' => ['required','string','min:8','regex:/[A-Z]/','regex:/[a-z]/','regex:/[0-9]/','regex:/[^A-Za-z0-9]/'],
            'roles' => ['required','array'],
            'roles.*' => ['integer','exists:admin_roles,id'],
        ]);
        $user = AdminUser::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'must_change_password' => true,
        ]);
        $user->roles()->sync($data['roles']);
        DB::table('admin_audit_logs')->insert([
            'actor_id' => auth('admin')->id(),
            'action' => 'admin_user.create',
            'ip' => $request->ip(),
            'meta' => json_encode(['target' => $user->email]),
            'created_at' => now(), 'updated_at' => now(),
        ]);
        return redirect()->route('admin.users.index')->with('success', 'Usuário admin criado.');
    }

    public function edit(AdminUser $user)
    {
        $roles = AdminRole::orderBy('name')->get();
        return view('admin.users.edit', compact('user','roles'));
    }

    public function update(AdminUser $user, Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email',"unique:admin_users,email,{$user->id},id"],
            'password' => ['nullable','string','min:8','regex:/[A-Z]/','regex:/[a-z]/','regex:/[0-9]/','regex:/[^A-Za-z0-9]/'],
            'roles' => ['required','array'],
            'roles.*' => ['integer','exists:admin_roles,id'],
        ]);
        $payload = ['name' => $data['name'], 'email' => $data['email']];
        if (!empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
            $payload['must_change_password'] = true;
        }
        $user->update($payload);
        $user->roles()->sync($data['roles']);
        DB::table('admin_audit_logs')->insert([
            'actor_id' => auth('admin')->id(),
            'action' => 'admin_user.update',
            'ip' => $request->ip(),
            'meta' => json_encode(['target' => $user->email]),
            'created_at' => now(), 'updated_at' => now(),
        ]);
        return redirect()->route('admin.users.index')->with('success', 'Usuário admin atualizado.');
    }

    public function destroy(AdminUser $user, Request $request)
    {
        $email = $user->email;
        $user->delete();
        DB::table('admin_audit_logs')->insert([
            'actor_id' => auth('admin')->id(),
            'action' => 'admin_user.delete',
            'ip' => $request->ip(),
            'meta' => json_encode(['target' => $email]),
            'created_at' => now(), 'updated_at' => now(),
        ]);
        return redirect()->route('admin.users.index')->with('success', 'Usuário admin removido.');
    }

    public function reset(AdminUser $user, Request $request)
    {
        $temp = bin2hex(random_bytes(4)).'@A1';
        $user->update([
            'password' => Hash::make($temp),
            'must_change_password' => true,
        ]);
        Mail::raw("Sua senha temporária: {$temp}", function ($m) use ($user) {
            $m->to($user->email)->subject('Redefinição de senha (Admin)');
        });
        DB::table('admin_audit_logs')->insert([
            'actor_id' => auth('admin')->id(),
            'action' => 'admin_user.reset_password',
            'ip' => $request->ip(),
            'meta' => json_encode(['target' => $user->email]),
            'created_at' => now(), 'updated_at' => now(),
        ]);
        return back()->with('success', 'Senha temporária enviada por e-mail.');
    }
}

