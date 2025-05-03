<x-layouts.app.header />

<!-- Hero Section -->
<div class="relative min-h-[85vh] flex items-start">
    <div class="absolute inset-0 overflow-hidden">
        <img src="{{ asset('images/artcaffe.jpg') }}" class="w-full h-full object-cover opacity-20" alt="Background">
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12">
        <div class="lg:flex lg:items-start lg:space-x-12">
            <div class="lg:w-1/2">
                <p class="text-xl text-amber-600 mb-4">Nga kokërr në filxhan</p>
                <h1 class="text-5xl md:text-7xl font-bold text-gray-900 dark:text-white leading-tight">
                    Nga kokrra në <span class="text-amber-600">filxhan</span>
                </h1>
                <p class="mt-6 text-xl text-gray-600 dark:text-gray-300">
                    Përjetoni përzierjen e përkryer të kafesë artizanale dhe përsosmërisë kulinare në një atmosferë ku kreativiteti takon komoditetin.
                </p>
                <div class="mt-10">
                    <a href="#menu" class="bg-amber-600 text-white px-8 py-4 rounded-full font-medium hover:bg-amber-700 transition-colors inline-block">
                        Shiko Menynë
                    </a>
                </div>
            </div>
            <div class="hidden lg:block lg:w-2/3">
                <img src="{{ asset('images/artcaffe-bg.png') }}" alt="Coffee Art" class="w-full">
            </div>
        </div>
    </div>
</div>

<!-- QR Code Section -->
<section class="py-20 bg-amber-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white sm:text-4xl">Menu & Porosia</h2>
            <p class="mt-4 text-xl text-gray-600 dark:text-gray-300">Zgjidhni dhe porosisni lehtësisht</p>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <!-- QR Code -->
            <div class="flex flex-col items-center justify-center space-y-6">
                <div class="w-72 h-72 bg-white p-6 rounded-2xl shadow-xl transform hover:scale-[1.02] transition-transform">
                    <img src="{{ asset('images/frame.png') }}" alt="Skano për të Porositur" class="w-full h-full">
                </div>
                <div class="text-center space-y-2">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Porositni Online</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Skano kodin QR për të<br>porositur direkt nga telefoni
                    </p>
                </div>
            </div>

            <!-- Menu Image -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-xl transform hover:scale-[1.02] transition-transform">
                <img src="{{ asset('images/kaffeturke.png') }}" alt="Menu Preview" class="w-full h-[500px] object-cover">
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section id="services" class="py-24 bg-gray-50 dark:bg-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white sm:text-4xl">Shërbimet Tona të Veçanta</h2>
            <p class="mt-4 text-gray-600 dark:text-gray-300">Zbuloni ofertat tona të përzgjedhura me kujdes</p>
        </div>
        <div class="mt-20 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
            <!-- Service Cards -->
            <!-- Kafe Artizanale -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl p-8 shadow-xl transform hover:-translate-y-1 transition-all">
                <div class="w-14 h-14 bg-amber-600 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M18 8h1a4 4 0 0 1 0 8h-1M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Kafe Artizanale</h3>
                <p class="mt-4 text-gray-600 dark:text-gray-400">
                    Pije kafeje të përgatitura me mjeshtëri duke përdorur kokrra premium dhe metoda inovative përgatitjeje.
                </p>
            </div>

            <!-- Ëmbëlsira -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl p-8 shadow-xl transform hover:-translate-y-1 transition-all">
                <div class="w-14 h-14 bg-amber-600 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 4c3.31 0 6 2.69 6 6v2H6v-2c0-3.31 2.69-6 6-6zm-7 10h14v2a6 6 0 0 1-6 6H11a6 6 0 0 1-6-6v-2z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Ëmbëlsira të Freskëta</h3>
                <p class="mt-4 text-gray-600 dark:text-gray-400">
                    Ëmbëlsira dhe pasta të pjekura çdo ditë që plotësojnë përsosmërisht pijet tona.
                </p>
            </div>

            <!-- Pije të Veçanta -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl p-8 shadow-xl transform hover:-translate-y-1 transition-all">
                <div class="w-14 h-14 bg-amber-600 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 3v18M5 8h14M3 14h18"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Pije të Veçanta</h3>
                <p class="mt-4 text-gray-600 dark:text-gray-400">
                    Krijime unike pijesh që tregojnë qasjen tonë inovative ndaj përgatitjes së pijeve.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-gray-900 text-gray-300 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <h3 class="text-2xl font-bold text-amber-600 mb-4">ArtCaffe</h3>
                <p class="text-sm">Nga kokërr në filxhan</p>
            </div>
            <div>
                <h4 class="font-semibold mb-4">Orari i Punës</h4>
                <p class="text-sm">E Hënë - E Premte: 7:00 - 22:00</p>
                <p class="text-sm">E Shtunë - E Diel: 8:00 - 23:00</p>
            </div>
            <div>
                <h4 class="font-semibold mb-4">Kontakti</h4>
                <p class="text-sm">Rruga e Kafesë 123</p>
                <p class="text-sm">kontakt@artcaffe.com</p>
                <p class="text-sm">+355 69 XXX XXXX</p>
            </div>
        </div>
    </div>
</footer>

