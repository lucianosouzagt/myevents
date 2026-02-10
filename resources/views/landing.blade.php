<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'MyEvents') }} - Gestão Inteligente de Eventos</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 dark:bg-gray-900 dark:text-white antialiased">

    <!-- Navbar -->
    <nav class="bg-white/80 backdrop-blur-md fixed w-full z-20 top-0 start-0 border-b border-gray-200 dark:bg-gray-900/80 dark:border-gray-700">
        <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
            <a href="/" class="flex items-center space-x-3 rtl:space-x-reverse">
                <img src="{{ asset('images/myevents-logo-horizoltal.png') }}" alt="MyEvents" class="h-10">
            </a>
            <div class="flex md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse">
                @auth
                    <a href="{{ route('events.my') }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        Ir para o Painel
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-gray-900 hover:text-blue-700 font-medium rounded-lg text-sm px-4 py-2 dark:text-white dark:hover:text-blue-500 mr-2">
                        Entrar
                    </a>
                    <a href="{{ route('register') }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        Começar Grátis
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="bg-white dark:bg-gray-900 pt-32 pb-16 lg:pt-40 lg:pb-24">
        <div class="grid max-w-screen-xl px-4 py-8 mx-auto lg:gap-8 xl:gap-0 lg:py-16 lg:grid-cols-12">
            <div class="mr-auto place-self-center lg:col-span-7">
                <h1 class="max-w-2xl mb-4 text-4xl font-extrabold tracking-tight leading-none md:text-5xl xl:text-6xl dark:text-white">
                    Gerencie seus eventos de forma <span class="text-blue-600 dark:text-blue-500">inteligente</span> e sem estresse.
                </h1>
                <p class="max-w-2xl mb-6 font-light text-gray-500 lg:mb-8 md:text-lg lg:text-xl dark:text-gray-400">
                    Do convite digital ao check-in na portaria. Controle RSVPs, acompanhantes e acessos via QR Code em uma única plataforma simples e poderosa.
                </p>
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-5 py-3 mr-3 text-base font-medium text-center text-white rounded-lg bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-900">
                    Criar meu primeiro evento
                    <svg class="w-5 h-5 ml-2 -mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </a>
            </div>
            <div class="hidden lg:mt-0 lg:col-span-5 lg:flex">
                <div class="bg-blue-50 dark:bg-gray-800 rounded-2xl p-8 w-full shadow-xl transform rotate-3 hover:rotate-0 transition-transform duration-500">
                    <!-- Abstract UI Mockup -->
                    <div class="bg-white dark:bg-gray-700 rounded-lg shadow-sm p-4 mb-4">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center text-2xl">🎉</div>
                            <div>
                                <div class="h-4 bg-gray-200 dark:bg-gray-600 rounded w-32 mb-2"></div>
                                <div class="h-3 bg-gray-100 dark:bg-gray-500 rounded w-24"></div>
                            </div>
                        </div>
                        <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-600 p-3 rounded mb-2">
                            <span class="text-sm font-medium">Confirmados</span>
                            <span class="text-green-600 font-bold">42</span>
                        </div>
                        <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-600 p-3 rounded">
                            <span class="text-sm font-medium">Check-ins</span>
                            <span class="text-blue-600 font-bold">18</span>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-700 rounded-lg shadow-sm p-4 flex items-center gap-3">
                        <div class="bg-green-100 dark:bg-green-900 p-2 rounded">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm font-bold">Novo RSVP Recebido</div>
                            <div class="text-xs text-gray-500">João Silva + 2 acompanhantes</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="bg-gray-50 dark:bg-gray-800 py-16">
        <div class="max-w-screen-xl px-4 mx-auto">
            <div class="text-center mb-16">
                <h2 class="mb-4 text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white">Tudo o que você precisa</h2>
                <p class="text-gray-500 sm:text-xl dark:text-gray-400">Funcionalidades pensadas para organizadores exigentes.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center md:text-left">
                <!-- Feature 1 -->
                <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-900 border border-gray-100 dark:border-gray-700">
                    <div class="flex justify-center items-center mb-4 w-10 h-10 rounded-full bg-blue-100 lg:h-12 lg:w-12 dark:bg-blue-900 mx-auto md:mx-0">
                        <svg class="w-5 h-5 text-blue-600 lg:w-6 lg:h-6 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold dark:text-white">Convites via E-mail e WhatsApp</h3>
                    <p class="text-gray-500 dark:text-gray-400">Envie convites personalizados com link único para cada convidado diretamente por E-mail ou WhatsApp.</p>
                </div>
                <!-- Feature 2 -->
                <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-900 border border-gray-100 dark:border-gray-700">
                    <div class="flex justify-center items-center mb-4 w-10 h-10 rounded-full bg-green-100 lg:h-12 lg:w-12 dark:bg-green-900 mx-auto md:mx-0">
                        <svg class="w-5 h-5 text-green-600 lg:w-6 lg:h-6 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold dark:text-white">Confirmar Presença (RSVP)</h3>
                    <p class="text-gray-500 dark:text-gray-400">Painel completo para acompanhar quem vai. Seus convidados confirmam ou recusam com um clique.</p>
                </div>
                <!-- Feature 3 -->
                <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-900 border border-gray-100 dark:border-gray-700">
                    <div class="flex justify-center items-center mb-4 w-10 h-10 rounded-full bg-purple-100 lg:h-12 lg:w-12 dark:bg-purple-900 mx-auto md:mx-0">
                        <svg class="w-5 h-5 text-purple-600 lg:w-6 lg:h-6 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold dark:text-white">QR Code Check-in</h3>
                    <p class="text-gray-500 dark:text-gray-400">Controle a portaria com segurança. Escaneie o QR Code dos convidados e evite penetras.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- BBQ Calculator Section -->
    <section class="bg-white dark:bg-gray-900 py-16 border-t border-gray-200 dark:border-gray-700">
        <div class="max-w-screen-xl px-4 mx-auto">
            <div class="text-center mb-12">
                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Novo</span>
                <h2 class="mt-2 mb-4 text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white">Vai fazer um Churrasco? 🍖</h2>
                <p class="text-gray-500 sm:text-xl dark:text-gray-400">Use nossa calculadora gratuita e planeje a quantidade ideal de carne e bebida.</p>
            </div>
            
            <div class="max-w-4xl mx-auto bg-gray-50 dark:bg-gray-800 rounded-2xl p-8 shadow-lg border border-gray-100 dark:border-gray-700">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- Inputs -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Homens</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <input type="number" id="men" value="0" min="0" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" oninput="calculateBBQ()">
                        </div>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Mulheres</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <input type="number" id="women" value="0" min="0" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" oninput="calculateBBQ()">
                        </div>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Crianças</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <input type="number" id="children" value="0" min="0" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" oninput="calculateBBQ()">
                        </div>
                    </div>
                </div>

                <!-- Results -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                    <div class="p-6 bg-white dark:bg-gray-700 rounded-xl shadow-sm border border-gray-100 dark:border-gray-600 transition-transform hover:scale-105">
                        <div class="text-4xl mb-3">🥩</div>
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Carne</div>
                        <div class="text-3xl font-extrabold text-gray-900 dark:text-white mt-1"><span id="meatResult">0.0</span> <span class="text-lg font-medium text-gray-500">kg</span></div>
                    </div>
                    <div class="p-6 bg-white dark:bg-gray-700 rounded-xl shadow-sm border border-gray-100 dark:border-gray-600 transition-transform hover:scale-105">
                        <div class="text-4xl mb-3">🍺</div>
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cerveja</div>
                        <div class="text-3xl font-extrabold text-gray-900 dark:text-white mt-1"><span id="beerResult">0</span> <span class="text-lg font-medium text-gray-500">latas</span></div>
                    </div>
                    <div class="p-6 bg-white dark:bg-gray-700 rounded-xl shadow-sm border border-gray-100 dark:border-gray-600 transition-transform hover:scale-105">
                        <div class="text-4xl mb-3">🥤</div>
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Refrigerante</div>
                        <div class="text-3xl font-extrabold text-gray-900 dark:text-white mt-1"><span id="sodaResult">0.0</span> <span class="text-lg font-medium text-gray-500">L</span></div>
                    </div>
                </div>
                
                <p class="mt-6 text-xs text-center text-gray-500 dark:text-gray-400 italic">*Estimativa baseada em consumo médio de 4 horas de festa.</p>
                
                <div class="mt-8 text-center">
                    <p class="text-sm font-medium text-gray-900 dark:text-white mb-3">Gostou da ferramenta? Cadastre-se agora e tenha acesso ao organizador completo!</p>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-center text-white bg-green-600 rounded-lg hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                        Quero organizar meu churrasco
                        <svg class="w-3.5 h-3.5 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <script>
        function calculateBBQ() {
            const men = parseInt(document.getElementById('men').value) || 0;
            const women = parseInt(document.getElementById('women').value) || 0;
            const children = parseInt(document.getElementById('children').value) || 0;

            // Rules:
            // Meat: Man 400g, Woman 300g, Child 200g
            // Beer: Man 12 cans (350ml), Woman 6 cans (Avg)
            // Soda: Man 0.5L, Woman 0.5L, Child 1L

            const meat = (men * 0.4) + (women * 0.3) + (children * 0.2);
            const beer = (men * 12) + (women * 6);
            const soda = (men * 0.5) + (women * 0.5) + (children * 1); // Liters

            // Animation effect
            animateValue(document.getElementById('meatResult'), meat.toFixed(1));
            animateValue(document.getElementById('beerResult'), Math.ceil(beer));
            animateValue(document.getElementById('sodaResult'), soda.toFixed(1));
        }

        function animateValue(obj, end) {
            obj.innerText = end;
        }
    </script>

    <!-- Social Proof / CTA -->
    <section class="bg-white dark:bg-gray-900 py-16">
        <div class="max-w-screen-xl px-4 mx-auto text-center">
            <h2 class="mb-4 text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white">Pronto para organizar seu próximo evento?</h2>
            <p class="mb-8 font-light text-gray-500 sm:text-xl dark:text-gray-400">Junte-se a centenas de organizadores que já modernizaram seus eventos.</p>
            <div class="flex flex-col space-y-4 sm:flex-row sm:justify-center sm:space-y-0 sm:space-x-4">
                <a href="{{ route('register') }}" class="inline-flex justify-center items-center py-3 px-5 text-base font-medium text-center text-white rounded-lg bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-900">
                    Criar conta gratuita
                </a>
                <a href="{{ route('login') }}" class="inline-flex justify-center items-center py-3 px-5 text-base font-medium text-center text-gray-900 rounded-lg border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 dark:text-white dark:border-gray-700 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                    Já tenho conta
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="p-4 bg-white md:p-8 lg:p-10 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
        <div class="mx-auto max-w-screen-xl text-center">
            <a href="#" class="flex justify-center items-center text-2xl font-semibold text-gray-900 dark:text-white">
                <img src="{{ asset('images/myevents-logo-horizoltal.png') }}" alt="MyEvents" class="h-12">
            </a>
            <p class="my-6 text-gray-500 dark:text-gray-400">A plataforma completa para gestão de convidados, RSVPs e controle de acesso.</p>
            <span class="text-sm text-gray-500 sm:text-center dark:text-gray-400">© {{ date('Y') }} <a href="#" class="hover:underline">MyEvents™</a>. Todos os direitos reservados.</span>
        </div>
    </footer>

</body>
</html>
