<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin · MyEvents</title>
  <script>
    window.tailwind = window.tailwind || {};
    tailwind.config = { darkMode: 'class' };
  </script>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    :root { --brand: #2563eb; --brand-600:#2563eb; --brand-700:#1d4ed8; }
  </style>
</head>
<body class="bg-gray-50 text-gray-900 dark:bg-gray-900 dark:text-gray-100">
  <div class="min-h-screen grid grid-cols-[260px_1fr]">
    <aside class="bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 p-4">
      <div class="flex items-center gap-2 mb-6">
        <span class="font-bold text-lg text-gray-900 dark:text-gray-100">MyEvents · Admin</span>
      </div>
      <nav class="space-y-1">
        <a class="block px-3 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-100" href="{{ route('admin.home') }}">Dashboard</a>
        <a class="block px-3 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-100" href="{{ route('admin.admins.index') }}">Usuários Admin</a>
        <a class="block px-3 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-100" href="{{ route('admin.users.index') }}">Criadores</a>
        <a class="block px-3 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-100" href="{{ route('admin.analytics.dashboard') }}">Analytics</a>
        <a class="block px-3 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-100" href="{{ route('admin.barbecue.suggestions') }}">Sugestões Churrasco</a>
      </nav>
      <div class="mt-6 text-sm text-gray-600 dark:text-gray-300">
        @if(auth('admin')->check())
          <div class="mb-2">Olá, {{ auth('admin')->user()->name }}</div>
          <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button class="px-3 py-2 rounded bg-red-600 hover:bg-red-700 text-white w-full">Sair</button>
          </form>
        @endif
      </div>
    </aside>
    <main class="p-6">
      @if(session('success'))
        <div class="mb-4 p-3 rounded bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200">{{ session('success') }}</div>
      @endif
      @if($errors->any())
        <div class="mb-4 p-3 rounded bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200">
          <ul class="list-disc pl-4">
            @foreach($errors->all() as $e)
              <li>{{ $e }}</li>
            @endforeach
          </ul>
        </div>
      @endif
      @yield('content')
      <footer class="mt-8 text-sm text-gray-500 dark:text-gray-400">© {{ date('Y') }} MyEvents™</footer>
    </main>
  </div>
  <script>
    (function(){
      const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
      document.documentElement.classList.toggle('dark', prefersDark);
      if (window.matchMedia) {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
          document.documentElement.classList.toggle('dark', e.matches);
        });
      }
    })();
  </script>
</body>
</html>
