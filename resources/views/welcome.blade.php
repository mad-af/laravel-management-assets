<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel + Tailwind + DaisyUI</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-base-100">
        <!-- Theme initialization script -->
    <script>
        // Initialize theme before page renders to prevent flash
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
        <!-- Navbar -->
        <div class="navbar bg-primary text-primary-content">
            <div class="flex-1">
                <a class="text-xl btn btn-ghost">Laravel + DaisyUI</a>
            </div>
            <div class="flex-none">
                <div class="dropdown dropdown-end">
                    <div tabindex="0" role="button" class="btn btn-ghost btn-circle">
                        <div class="indicator">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5 6m0 0h9" />
                            </svg>
                            <span class="badge badge-sm indicator-item">8</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hero Section -->
        <div class="hero min-h-96 bg-base-200">
            <div class="text-center hero-content">
                <div class="max-w-md">
                    <h1 class="text-5xl font-bold">Hello DaisyUI!</h1>
                    <p class="py-6">Laravel berhasil diintegrasikan dengan Tailwind CSS dan DaisyUI. Sekarang Anda dapat menggunakan komponen-komponen yang indah dan responsif.</p>
                    <button class="btn btn-primary">Get Started</button>
                </div>
            </div>
        </div>

        <!-- Content Section -->
        <div class="container px-4 py-8 mx-auto">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                <!-- Card 1 -->
                <div class="shadow-xl card bg-base-100">
                    <figure><img src="https://img.daisyui.com/images/stock/photo-1606107557195-0e29a4b5b4aa.jpg" alt="Shoes" /></figure>
                    <div class="card-body">
                        <h2 class="card-title">Shoes!</h2>
                        <p>If a dog chews shoes whose shoes does he choose?</p>
                        <div class="justify-end card-actions">
                            <button class="btn btn-primary">Buy Now</button>
                        </div>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="shadow-xl card bg-base-100">
                    <div class="card-body">
                        <h2 class="card-title">Components</h2>
                        <p>DaisyUI menyediakan berbagai komponen siap pakai:</p>
                        <div class="justify-end card-actions">
                            <div class="badge badge-outline">Buttons</div>
                            <div class="badge badge-outline">Cards</div>
                            <div class="badge badge-outline">Forms</div>
                        </div>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="shadow-xl card bg-base-100">
                    <div class="card-body">
                        <h2 class="card-title">Themes</h2>
                        <p>Pilih dari 30+ tema yang tersedia atau buat tema custom Anda sendiri.</p>
                        <div class="justify-end card-actions">
                            <button class="btn btn-secondary">Explore Themes</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Example -->
            <div class="my-8 divider">Form Components</div>
            <div class="shadow-xl card bg-base-100">
                <div class="card-body">
                    <h2 class="card-title">Contact Form</h2>
                    <form class="space-y-4">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Name</span>
                            </label>
                            <input type="text" placeholder="Your name" class="input input-bordered" />
                        </div>
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Email</span>
                            </label>
                            <input type="email" placeholder="your@email.com" class="input input-bordered" />
                        </div>
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Message</span>
                            </label>
                            <textarea class="textarea textarea-bordered" placeholder="Your message"></textarea>
                        </div>
                        <div class="form-control">
                            <button type="submit" class="btn btn-primary">Send Message</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Alert Examples -->
            <div class="my-8 divider">Alerts</div>
            <div class="space-y-4">
                <div class="alert alert-info">
                    <i data-lucide="info" class="w-6 h-6 stroke-current shrink-0"></i>
                    <span>DaisyUI berhasil diintegrasikan dengan Laravel!</span>
                </div>
                <div class="alert alert-success">
                    <i data-lucide="check-circle" class="w-6 h-6 stroke-current shrink-0"></i>
                    <span>Semua komponen siap digunakan!</span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="p-10 rounded footer footer-center bg-base-200 text-base-content">
            <nav class="grid grid-flow-col gap-4">
                <a class="link link-hover">About us</a>
                <a class="link link-hover">Contact</a>
                <a class="link link-hover">Jobs</a>
                <a class="link link-hover">Press kit</a>
            </nav>
            <nav>
                <div class="grid grid-flow-col gap-4">
                    <a><i data-lucide="twitter" class="w-6 h-6 fill-current"></i></a>
                    <a><i data-lucide="youtube" class="w-6 h-6 fill-current"></i></a>
                    <a><i data-lucide="facebook" class="w-6 h-6 fill-current"></i></a>
                </div>
            </nav>
            <aside>
                <p>Copyright © 2024 - Laravel + Tailwind + DaisyUI</p>
            </aside>
        </footer>
    </body>
</html>
