<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="{{ asset('csu_logo.png') }}">

        <title>CSU-Aparri DMRS | Document Management and Recording System</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                /* Simple CSS for the university welcome page */
                body {
                    font-family: 'Instrument Sans', sans-serif;
                    background: #FDFDFC;
                    color: #1b1b18;
                    margin: 0;
                    padding: 0;
                    min-height: 100vh;
                    display: flex;
                    flex-direction: column;
                }
                .container {
                    max-width: 1200px;
                    margin: 0 auto;
                    padding: 2rem;
                }
                .header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 2rem;
                }
                .nav-links {
                    display: flex;
                    gap: 1rem;
                }
                .btn {
                    padding: 0.75rem 1.5rem;
                    text-decoration: none;
                    border-radius: 0.375rem;
                    font-weight: 500;
                    transition: all 0.3s;
                    border: 1px solid;
                }
                .btn-primary {
                    background: #1b1b18;
                    color: white;
                    border-color: #1b1b18;
                }
                .btn-primary:hover {
                    background: #000;
                }
                .btn-secondary {
                    background: transparent;
                    color: #1b1b18;
                    border-color: #e5e5e5;
                }
                .btn-secondary:hover {
                    border-color: #1b1b18;
                }
                .main-content {
                    flex: 1;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    text-align: center;
                }
                .hero {
                    max-width: 800px;
                }
                .hero h1 {
                    font-size: 3rem;
                    font-weight: bold;
                    margin-bottom: 1rem;
                    line-height: 1.2;
                }
                .hero h2 {
                    font-size: 1.5rem;
                    color: #666;
                    margin-bottom: 0.5rem;
                }
                .hero .motto {
                    color: #666;
                    margin-bottom: 2rem;
                }
                .divider {
                    width: 80px;
                    height: 4px;
                    background: #1b1b18;
                    margin: 2rem auto;
                }
                .hero h3 {
                    font-size: 2rem;
                    font-weight: 600;
                    margin-bottom: 1rem;
                }
                .hero .description {
                    font-size: 1.125rem;
                    color: #666;
                    margin-bottom: 3rem;
                    line-height: 1.6;
                }
                .features {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                    gap: 2rem;
                    margin-bottom: 3rem;
                }
                .feature-card {
                    background: white;
                    padding: 2rem;
                    border-radius: 0.5rem;
                    border: 1px solid #e5e5e5;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
                }
                .feature-card .icon {
                    font-size: 2rem;
                    margin-bottom: 1rem;
                }
                .feature-card h4 {
                    font-size: 1.125rem;
                    font-weight: 600;
                    margin-bottom: 0.5rem;
                }
                .feature-card p {
                    color: #666;
                    line-height: 1.5;
                }
                .cta-buttons {
                    display: flex;
                    gap: 1rem;
                    justify-content: center;
                    flex-wrap: wrap;
                }
                .footer {
                    text-align: center;
                    padding: 2rem;
                    border-top: 1px solid #e5e5e5;
                    margin-top: 3rem;
                    color: #666;
                }
                .footer p {
                    margin: 0.5rem 0;
                }
                @media (max-width: 768px) {
                    .hero h1 {
                        font-size: 2rem;
                    }
                    .hero h3 {
                        font-size: 1.5rem;
                    }
                    .cta-buttons {
                        flex-direction: column;
                        align-items: center;
                    }
                    .nav-links {
                        flex-direction: column;
                        gap: 0.5rem;
                    }
                    .header {
                        flex-direction: column;
                        gap: 1rem;
                    }
                }
            </style>
        @endif
    </head>
    <body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">
        <header class="w-full lg:max-w-4xl max-w-[335px] text-sm mb-6 not-has-[nav]:hidden">
            @if (Route::has('login'))
                <nav class="flex items-center justify-end gap-4">
                    @auth
                        <a
                            href="{{ url('/dashboard') }}"
                            class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal"
                        >
                            Dashboard
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal"
                        >
                            Log in
                        </a>

                        @if (Route::has('register'))
                            <a
                                href="{{ route('register') }}"
                                class="inline-block px-5 py-1.5 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] hover:bg-black dark:hover:bg-white border border-transparent rounded-sm text-sm leading-normal"
                            >
                                Register
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>

        <main class="flex-1 flex items-center justify-center w-full lg:max-w-4xl max-w-[335px]">
            <div class="text-center">
                <!-- University Logo and Header -->
                <div class="mb-8">
                    <div class="mb-4">
                        <!-- You can add university logo here -->
                        <h1 class="text-4xl lg:text-5xl font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                            CAGAYAN STATE UNIVERSITY
                        </h1>
                        <h2 class="text-xl lg:text-2xl font-medium text-[#706f6c] dark:text-[#A1A09A] mb-1">
                            Aparri Campus
                        </h2>
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                            Excellence • Innovation • Service
                        </p>
                    </div>

                    <div class="w-20 h-1 bg-[#1b1b18] dark:bg-[#EDEDEC] mx-auto mb-6"></div>

                    <h3 class="text-2xl lg:text-3xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">
                        Document Management and Recording System
                    </h3>
                    <p class="text-lg text-[#706f6c] dark:text-[#A1A09A] max-w-2xl mx-auto">
                        Streamline your document workflows with our comprehensive digital solution for efficient document management, tracking, and secure record keeping.
                    </p>
                </div>

                <!-- Feature Cards -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg p-6 shadow-sm">
                        <div class="text-3xl mb-3">📄</div>
                        <h4 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-2">Document Storage</h4>
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Secure digital storage for all university documents with easy organization and retrieval.</p>
                    </div>

                    <div class="bg-white dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg p-6 shadow-sm">
                        <div class="text-3xl mb-3">🔍</div>
                        <h4 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-2">Record Tracking</h4>
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Track document status, approval workflows, and maintain comprehensive audit trails.</p>
                    </div>

                    <div class="bg-white dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg p-6 shadow-sm">
                        <div class="text-3xl mb-3">🔒</div>
                        <h4 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-2">Secure Access</h4>
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Role-based access control ensuring document security and authorized personnel access only.</p>
                    </div>
                </div>

                <!-- Call to Action -->
                @guest
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                        <a
                            href="{{ route('login') }}"
                            class="inline-block px-8 py-3 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] hover:bg-black dark:hover:bg-white border border-transparent rounded-lg text-base font-medium transition-all"
                        >
                            Get Started
                        </a>
                        <a
                            href="#about"
                            class="inline-block px-8 py-3 border border-[#19140035] hover:border-[#1915014a] text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] dark:text-[#EDEDEC] rounded-lg text-base font-medium transition-all"
                        >
                            Learn More
                        </a>
                    </div>
                @endguest
            </div>
        </main>

        <!-- Footer -->
        <footer class="w-full lg:max-w-4xl max-w-[335px] text-center pt-8 border-t border-[#e3e3e0] dark:border-[#3E3E3A] mt-12">
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                © {{ date('Y') }} Cagayan State University - Aparri Campus. All rights reserved.
            </p>
            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-2">
                Aparri Campus, Maura, Aparri, Cagayan, Philippines
            </p>
        </footer>
                </nav>

        </header>


        @if (Route::has('login'))
            <div class="h-14.5 hidden lg:block"></div>
        @endif
    </body>
</html>
