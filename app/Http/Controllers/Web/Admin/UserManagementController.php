<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserManagementController extends Controller
{
    public function index()
    {
        $creators = User::whereHas('roles', fn ($q) => $q->where('slug', 'creator'))->get();
        return view('admin.users.index', compact('creators'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'string', 'min:8', 'max:64', 'regex:/[A-Z]/', 'regex:/[a-z]/', 'regex:/[0-9]/', 'regex:/[^A-Za-z0-9]/'],
        ]);
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'must_change_password' => true,
        ]);
        $creator = Role::firstOrCreate(['slug' => 'creator'], ['name' => 'Criador']);
        $user->roles()->syncWithoutDetaching([$creator->id]);
        DB::table('audit_logs')->insert([
            'actor_id' => Auth::id(),
            'target_user_id' => $user->id,
            'action' => 'creator.create',
            'meta' => json_encode(['email' => $user->email]),
            'created_at' => now(), 'updated_at' => now(),
        ]);
        return back()->with('success', 'Usuário criador criado com sucesso.');
    }

    public function activate(User $user)
    {
        $user->update(['deleted_at' => null]);
        DB::table('audit_logs')->insert([
            'actor_id' => Auth::id(),
            'target_user_id' => $user->id,
            'action' => 'creator.activate',
            'meta' => json_encode([]),
            'created_at' => now(), 'updated_at' => now(),
        ]);
        return back()->with('success', 'Usuário ativado.');
    }

    public function deactivate(User $user)
    {
        $user->tokens()->delete();
        $user->update(['remember_token' => null]);
        $user->delete();
        DB::table('audit_logs')->insert([
            'actor_id' => Auth::id(),
            'target_user_id' => $user->id,
            'action' => 'creator.deactivate',
            'meta' => json_encode([]),
            'created_at' => now(), 'updated_at' => now(),
        ]);
        return back()->with('success', 'Usuário desativado.');
    }

    public function resetPassword(User $user, Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'max:64', 'regex:/[A-Z]/', 'regex:/[a-z]/', 'regex:/[0-9]/', 'regex:/[^A-Za-z0-9]/'],
        ]);
        $hash = Hash::make($request->password);
        // impedir reutilização: verificar últimos 5
        $recent = DB::table('password_histories')->where('user_id', $user->id)->orderByDesc('id')->limit(5)->pluck('password_hash')->all();
        foreach ($recent as $old) {
            if (Hash::check($request->password, $old)) {
                return back()->withErrors(['password' => 'A nova senha não pode ser igual às 5 últimas.']);
            }
        }
        DB::transaction(function () use ($user, $hash) {
            $user->update(['password' => $hash, 'must_change_password' => true]);
            DB::table('password_histories')->insert([
                'user_id' => $user->id,
                'password_hash' => $hash,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        });
        DB::table('audit_logs')->insert([
            'actor_id' => Auth::id(),
            'target_user_id' => $user->id,
            'action' => 'creator.password.reset',
            'meta' => json_encode([]),
            'created_at' => now(), 'updated_at' => now(),
        ]);
        return back()->with('success', 'Senha redefinida. O usuário deverá trocá-la no próximo login.');
    }
}
