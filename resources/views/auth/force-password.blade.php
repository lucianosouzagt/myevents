@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white dark:bg-gray-800 p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Defina uma nova senha</h2>
    <form method="POST" action="{{ route('password.force.update') }}">
        @csrf
        <div class="mb-4">
            <label class="block text-sm mb-1">Nova senha</label>
            <div class="relative">
                <input id="new-password" type="password" name="password" class="w-full rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 pr-10" required>
                <button type="button" id="toggleNew" class="absolute inset-y-0 right-0 px-3 text-sm">👁️</button>
            </div>
            <div id="pwd-strength" class="text-xs mt-1 text-gray-500"></div>
            <p class="text-xs text-gray-500 mt-1">Mín. 8, com maiúscula, minúscula, número e símbolo.</p>
            @error('password')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-6">
            <label class="block text-sm mb-1">Confirmar senha</label>
            <div class="relative">
                <input id="confirm-password" type="password" name="password_confirmation" class="w-full rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 pr-10" required>
                <button type="button" id="toggleConfirm" class="absolute inset-y-0 right-0 px-3 text-sm">👁️</button>
            </div>
            <div id="pwd-match" class="text-xs mt-1"></div>
        </div>
        <button class="px-4 py-2 rounded bg-blue-600 text-white">Atualizar senha</button>
    </form>
    <p class="text-xs text-gray-500 mt-4">Por segurança, você precisa alterar a senha no primeiro acesso.</p>
 </div>
<script>
const np = document.getElementById('new-password');
const cp = document.getElementById('confirm-password');
const strength = document.getElementById('pwd-strength');
const match = document.getElementById('pwd-match');
document.getElementById('toggleNew').onclick = () => np.type = np.type === 'password' ? 'text' : 'password';
document.getElementById('toggleConfirm').onclick = () => cp.type = cp.type === 'password' ? 'text' : 'password';
function score(v){
  let s=0;
  if(v.length>=8)s++;
  if(/[A-Z]/.test(v))s++;
  if(/[a-z]/.test(v))s++;
  if(/[0-9]/.test(v))s++;
  if(/[^A-Za-z0-9]/.test(v))s++;
  return s;
}
function label(s){return ['Muito fraca','Fraca','Média','Forte','Muito forte'][Math.max(0,Math.min(s-1,4))];}
np.addEventListener('input',()=>{strength.textContent='Força: '+label(score(np.value));});
function checkMatch(){match.textContent = np.value && cp.value ? (np.value===cp.value?'Confere':'Não confere') : ''; match.style.color = np.value===cp.value?'green':'red';}
np.addEventListener('input',checkMatch); cp.addEventListener('input',checkMatch);
</script>
@endsection
