<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Learn Buddy') }} - Never Study Alone</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50 text-gray-900 selection:bg-indigo-500 selection:text-white font-sans">
    
    <!-- NAVIGATION -->
    <header class="fixed inset-x-0 top-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-100">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center max-w-7xl">
            <x-application-logo />
            <div class="flex items-center gap-6">
                @auth
                    <a href="{{ route('lobby') }}" class="text-sm font-bold text-gray-700 hover:text-indigo-600 transition">Go to Lobby &rarr;</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-bold text-gray-700 hover:text-indigo-600 transition hidden sm:block">Log in</a>
                    <a href="{{ route('register') }}" class="px-5 py-2.5 bg-gray-900 text-white text-sm font-bold rounded-full hover:bg-gray-800 transition shadow-sm">Get Started</a>
                @endauth
            </div>
        </nav>
    </header>

    <!-- HERO SECTION -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <div class="container mx-auto px-6 max-w-7xl relative z-10">
            <div class="lg:w-2/3 mx-auto text-center">
                
                <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight text-gray-900 mb-8 leading-tight">
                    Master your skills, <br/>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600">together.</span>
                </h1>
                
                <p class="text-lg md:text-xl text-gray-600 mb-10 max-w-2xl mx-auto leading-relaxed">
                    Stop learning in isolation. Learn Buddy instantly matches you with peers studying the exact same topic. Share resources, log daily standups, and build unstoppable streaks.
                </p>
                
                <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
                    <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-4 bg-indigo-600 text-white font-bold rounded-full hover:bg-indigo-700 transition shadow-lg shadow-indigo-200 text-lg flex items-center justify-center gap-2">
                        Start Studying Free
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </a>
                    <a href="#features" class="w-full sm:w-auto px-8 py-4 bg-white text-gray-700 font-bold rounded-full hover:bg-gray-50 border border-gray-200 transition text-lg text-center">
                        See how it works
                    </a>
                </div>
                
            </div>
        </div>
        
        <!-- Background Decorative Blobs -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-7xl h-full overflow-hidden -z-10 pointer-events-none opacity-40">
            <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-indigo-300 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob"></div>
            <div class="absolute top-[20%] right-[-10%] w-96 h-96 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-2000"></div>
        </div>
    </section>

    <!-- FEATURES SECTION -->
    <section id="features" class="py-24 bg-white border-t border-gray-100">
        <div class="container mx-auto px-6 max-w-7xl">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">Everything you need to stay accountable</h2>
                <p class="mt-4 text-lg text-gray-500">Built for self-taught developers and lifelong learners.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-10">
                <!-- Feature 1 -->
                <div class="bg-gray-50 rounded-2xl p-8 border border-gray-100 hover:shadow-lg transition duration-300">
                    <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Instant Matchmaking</h3>
                    <p class="text-gray-600">Don't have a study buddy? Pick a topic and instantly get teleported into a room with a partner who is ready to learn.</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-gray-50 rounded-2xl p-8 border border-gray-100 hover:shadow-lg transition duration-300">
                    <div class="w-12 h-12 bg-green-100 text-green-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Real-Time Collaboration</h3>
                    <p class="text-gray-600">Experience blazing-fast WebSockets. Chat with your cohort, share resource links, and see activity updates instantly without reloading.</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-gray-50 rounded-2xl p-8 border border-gray-100 hover:shadow-lg transition duration-300">
                    <div class="w-12 h-12 bg-orange-100 text-orange-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Daily Standups & Streaks</h3>
                    <p class="text-gray-600">Log your progress, reply to blockers, and build massive streaks to keep your motivation burning day after day.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- BOTTOM CTA -->
    <section class="py-20 bg-gray-900 text-white">
        <div class="container mx-auto px-6 max-w-4xl text-center">
            <h2 class="text-3xl md:text-4xl font-extrabold mb-6">Ready to crush your goals?</h2>
            <p class="text-lg text-gray-400 mb-10">Join the waitlist or hop straight into a lobby today.</p>
            <a href="{{ route('register') }}" class="px-8 py-4 bg-indigo-500 text-white font-bold rounded-full hover:bg-indigo-400 transition shadow-lg text-lg">
                Create Your Free Account
            </a>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-white py-8 border-t border-gray-200">
        <div class="container mx-auto px-6 max-w-7xl flex flex-col md:flex-row justify-between items-center gap-4">
            <x-application-logo />
            <p class="text-sm text-gray-500">&copy; {{ date('Y') }} {{ config('app.name') }}.</p>
        </div>
    </footer>

</body>
</html>