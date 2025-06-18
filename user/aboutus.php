<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Manon Tours</title>
    <link href="./style.css" rel="stylesheet">

    <style>
        .bg-blue-500-vision {
            background-color: rgb(15 ,23, 42);
        }
    </style>

</head>

<body>

    <!-- navigation -->
    <nav class="bg-gray-800">
        <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
            <div class="relative flex h-16 items-center justify-between">
                <div class="absolute inset-y-0 left-0 flex items-center sm:hidden">
                    <!-- Mobile menu button-->
                    <button type="button"
                        class="relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-700 hover:text-white focus:ring-2 focus:ring-white focus:outline-hidden focus:ring-inset"
                        aria-controls="mobile-menu" aria-expanded="false">
                        <span class="absolute -inset-0.5"></span>
                        <span class="sr-only">Open main menu</span>
                        <!--
            Icon when menu is closed.

            Menu open: "hidden", Menu closed: "block"
          -->
                        <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" aria-hidden="true" data-slot="icon">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                        <!--
            Icon when menu is open.

            Menu open: "block", Menu closed: "hidden"
          -->
                        <svg class="hidden size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" aria-hidden="true" data-slot="icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="flex flex-1 items-center justify-center sm:items-stretch sm:justify-start">
                    <div class="flex shrink-0 items-center">
                        <img class="h-8 w-auto"
                            src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=indigo&shade=500"
                            alt="Your Company">
                    </div>
                    <div class="hidden sm:ml-6 sm:block">
                        <div class="flex space-x-4">
                            <!-- Current: "bg-gray-900 text-white", Default: "text-gray-300 hover:bg-gray-700 hover:text-white" -->
                            <a href="#" class="rounded-md bg-gray-900 px-3 py-2 text-sm font-medium text-white"
                                aria-current="page">Dashboard</a>
                            <a href="#"
                                class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Team</a>
                            <a href="#"
                                class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Projects</a>
                            <a href="#"
                                class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Calendar</a>
                        </div>
                    </div>
                </div>
                <div class="absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0">
                    <button type="button"
                        class="relative rounded-full bg-gray-800 p-1 text-gray-400 hover:text-white focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 focus:outline-hidden">
                        <span class="absolute -inset-1.5"></span>
                        <span class="sr-only">View notifications</span>
                        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                            aria-hidden="true" data-slot="icon">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                    </button>

                    <!-- Profile dropdown -->


                </div>
            </div>
        </div>
        </div>

        <!-- Mobile menu, show/hide based on menu state. -->
        <div class="sm:hidden" id="mobile-menu">
            <div class="space-y-1 px-2 pt-2 pb-3">
                <!-- Current: "bg-gray-900 text-white", Default: "text-gray-300 hover:bg-gray-700 hover:text-white" -->
                <a href="#" class="block rounded-md bg-gray-900 px-3 py-2 text-base font-medium text-white"
                    aria-current="page">Dashboard</a>
                <a href="#"
                    class="block rounded-md px-3 py-2 text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Team</a>
                <a href="#"
                    class="block rounded-md px-3 py-2 text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Projects</a>
                <a href="#"
                    class="block rounded-md px-3 py-2 text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Calendar</a>
            </div>
        </div>
    </nav>
    <!-- navigation -->



    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        secondary: {
                            50: '#f5f3ff',
                            100: '#ede9fe',
                            200: '#ddd6fe',
                            300: '#c4b5fd',
                            400: '#a78bfa',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
                            800: '#5b21b6',
                            900: '#4c1d95',
                        },
                        dark: '#0f172a',
                        light: '#f8fafc'
                    }
                }
            }
        }
    </script>

<div class="bg-light font-sans antialiased">

    <!-- Hero Section -->
    <!-- <header class="bg-gradient-to-r from-dark to-primary-900 text-white py-20">
        <div class="container mx-auto px-6 text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">About SynthMind AI</h1>
            <p class="text-xl md:text-2xl max-w-3xl mx-auto opacity-90">Where human creativity meets artificial intelligence to solve tomorrow's challenges</p>
        </div>
    </header> -->

    <!-- Main Content -->
    <main class="container mx-auto px-6 py-16">
        <!-- Our Story -->
        <section class="mb-20">
            <div class="flex flex-col md:flex-row items-center gap-12">
                <div class="md:w-1/2">
                    <h2 class="text-3xl font-bold text-dark mb-6">Our Story</h2>
                    <p class="text-gray-700 mb-4">SynthMind AI was founded in 2019 with a radical idea: that artificial intelligence should enhance human decision-making rather than replace it. Our team of neuroscientists and machine learning experts set out to create a new paradigm in AI.</p>
                    <p class="text-gray-700 mb-4">Today, we're recognized as pioneers in cognitive computing, with our technology powering some of the world's most innovative companies across healthcare, finance, and creative industries.</p>
                    <p class="text-gray-700">Our name reflects our philosophy - we synthesize human-like understanding with machine precision to create truly intelligent systems.</p>
                </div>
                <div class="md:w-1/2">
                    <img src="https://images.unsplash.com/photo-1629904853893-c2c8981a1dc5?ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80" alt="AI Neural Network Visualization" class="rounded-lg shadow-xl w-full h-auto">
                </div>
            </div>
        </section>

        <!-- Our Mission -->
        <section class="bg-primary-50 rounded-xl p-12 mb-20">
            <div class="text-center max-w-4xl mx-auto">
                <h2 class="text-3xl font-bold text-dark mb-6">Our Goals</h2>
                <p class="text-xl text-gray-700 mb-8">"To create symbiotic intelligence systems that amplify human potential while maintaining ethical boundaries and transparency."</p>
                <div class="flex flex-wrap justify-center gap-8">
                    <div class="bg-white p-6 rounded-lg shadow-md w-full sm:w-64">
                        <div class="text-primary-600 mb-4">
                            <i class="fas fa-lightbulb text-3xl"></i>
                        </div>
                        <h3 class="font-bold text-dark mb-2">Augmented Intelligence</h3>
                        <p class="text-gray-600">We build tools that enhance human cognition, not replace it.</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-md w-full sm:w-64">
                        <div class="text-primary-600 mb-4">
                            <i class="fas fa-shield-alt text-3xl"></i>
                        </div>
                        <h3 class="font-bold text-dark mb-2">Ethical Framework</h3>
                        <p class="text-gray-600">Every system undergoes rigorous ethical review before deployment.</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-md w-full sm:w-64">
                        <div class="text-primary-600 mb-4">
                            <i class="fas fa-project-diagram text-3xl"></i>
                        </div>
                        <h3 class="font-bold text-dark mb-2">Neural Synthesis</h3>
                        <p class="text-gray-600">Our proprietary architecture mimics human neural pathways.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-blue-500-vision text-white py-12 px-4">
            <h2 class="text-2xl font-bold text-center">Our Vision</h2>
            <p class="mt-4 text-center max-w-2xl mx-auto">
            Healthcare anytime, anywhere. We aim to revolutionize the healthcare industry by making quality healthcare accessible to everyone.
            </p>
        </section>

        <section class="bg-blue-500-vision text-white py-12 px-4">
            <h2 class="text-2xl font-bold text-center">Our Mission</h2>
            <p class="mt-4 text-center max-w-2xl mx-auto">
            Healthcare anytime, anywhere. We aim to revolutionize the healthcare industry by making quality healthcare accessible to everyone.
            </p>
        </section>

        <!-- About Section -->
    <section id="about" class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="md:flex items-center">
                <div class="md:w-1/2 mb-8 md:mb-0 md:pr-8">
                    <img src="../images/about.webp" alt="About Us" class="rounded-lg shadow-lg w-full">
                </div>
                <div class="md:w-1/2">
                    <h2 class="text-3xl font-bold mb-6 text-black">About AnimalVenture</h2>
                    <p class="text-gray-600 mb-4">
                        AnimalVenture was created to combine a love for animals with opportunities to earn rewards. 
                        Our platform supports real-world animal conservation efforts by donating a portion of all revenue.
                    </p>
                    <p class="text-gray-600 mb-6">
                        Since 2022, we've helped users earn over $50,000 in rewards while contributing $10,000 to animal charities worldwide.
                    </p>
                    <!-- <div class="flex space-x-4">
                        <button class="bg-green-700 hover:bg-green-800 text-white py-2 px-6 rounded-full">
                            Our Mission
                        </button>
                        <button class="border-2 border-green-700 text-green-700 hover:bg-green-50 py-2 px-6 rounded-full">
                            Charity Partners
                        </button>
                    </div> -->
                </div>
            </div>
        </div>
    </section>

        <!-- Achievements -->
        <section class="bg-dark text-white rounded-xl p-12 mb-20">
            <h2 class="text-3xl font-bold mb-12 text-center">Our Impact</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="text-5xl font-bold text-primary-400 mb-2">37+</div>
                    <div class="text-gray-300">Peer-Reviewed Papers</div>
                </div>
                <div>
                    <div class="text-5xl font-bold text-primary-400 mb-2">84+</div>
                    <div class="text-gray-300">Patents Granted</div>
                </div>
                <div>
                    <div class="text-5xl font-bold text-primary-400 mb-2">22M+</div>
                    <div class="text-gray-300">Daily Predictions</div>
                </div>
                <div>
                    <div class="text-5xl font-bold text-primary-400 mb-2">98%</div>
                    <div class="text-gray-300">Client Retention</div>
                </div>
            </div>
        </section>
    </div>
    </main>

    <!-- footer -->

    <div class="bg-white">

        <!-- Placeholder content above the footer -->
        <!-- <div class="h-[30px] bg-gray-100 flex items-center justify-center">
            <span class="text-gray-400">Page Content Area</span>
        </div> -->

        <footer class="bg-black text-gray-300 relative">
            <!-- Overlapping CTA Section -->
            <div class="relative max-w-4xl mx-auto -mb-16 md:-mb-20 z-10 px-4 pt-24 md:pt-32">
                <div
                    class="bg-gradient-to-r from-yellow-100 via-blue-100 to-pink-100 p-8 md:p-12 rounded-3xl shadow-xl text-center">
                    <h2 class="text-2xl md:text-3xl font-semibold text-gray-900 mb-6">
                        Get to know the world-class board of directors governing our organization.
                    </h2>
                    <button
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-8 rounded-full transition duration-200 shadow-md">
                        Meet our Board
                    </button>
                </div>
            </div>

            <!-- Main Footer Content -->
            <div class="max-w-7xl mx-auto px-6 lg:px-8 pt-24 md:pt-32 pb-12">
                <div class="grid grid-cols-2 md:grid-cols-6 gap-8">

                    <!-- Column 1: Newsletter & Social -->
                    <div class="col-span-2 md:col-span-2">
                        <p class="text-xs uppercase font-semibold text-gray-400 mb-3 tracking-wider">Sign up for NewsL
                            TR</p>
                        <input type="email" placeholder="Enter your email..."
                            class="w-full bg-gray-800 border border-gray-700 rounded-full py-2 px-4 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 mb-3 appearance-none">
                        <button
                            class="w-full bg-gray-700 hover:bg-gray-600 text-gray-300 rounded-full py-2 px-4 text-sm transition duration-200 mb-6">
                            Next
                        </button>

                        <p class="text-xs uppercase font-semibold text-gray-400 mb-3 tracking-wider">Follow us</p>
                        <div class="flex space-x-4 social-icon">
                            <a href="#" class="text-gray-400 hover:text-white">
                                <svg fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-white">
                                <svg fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path
                                        d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z">
                                    </path>
                                </svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-white">
                                <svg fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M12.315 2c2.43 0 2.784.01 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.05 1.024.06 1.378.06 3.808s-.01 2.784-.06 3.808c-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.024.05-1.378.06-3.808.06s-2.784-.01-3.808-.06c-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.05-1.024-.06-1.378-.06-3.808s.01-2.784.06-3.808c.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.01 9.255 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.06-1.03.048-1.634.208-2.121.41a3.109 3.109 0 00-1.162.763 3.109 3.109 0 00-.763 1.162c-.202.487-.362 1.09-.41 2.121-.05 1.023-.06 1.351-.06 3.807v.468c0 2.456.01 2.784.06 3.807.048 1.03.208 1.634.41 2.121a3.109 3.109 0 00.763 1.162 3.109 3.109 0 001.162.763c.487.202 1.09.362 2.121.41 1.023.05 1.351.06 3.807.06h.468c2.456 0 2.784-.01 3.807-.06 1.03-.048 1.634-.208 2.121-.41a3.109 3.109 0 001.162-.763 3.109 3.109 0 00.763-1.162c.202-.487.362-1.09.41-2.121.05-1.023.06-1.351.06-3.807v-.468c0-2.456-.01-2.784-.06-3.807-.048-1.03-.208-1.634-.41-2.121a3.109 3.109 0 00-.763-1.162 3.109 3.109 0 00-1.162-.763c-.487-.202-1.09-.362-2.121-.41-1.023-.05-1.351-.06-3.807-.06zm-1.928 2.81a6.156 6.156 0 100 12.312 6.156 6.156 0 000-12.312zm0 1.802a4.354 4.354 0 110 8.708 4.354 4.354 0 010-8.708zm6.441-2.553a1.44 1.44 0 100-2.88 1.44 1.44 0 000 2.88z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Column 2: Start Learning -->
                    <div>
                        <p class="text-xs uppercase font-semibold text-gray-400 mb-3 tracking-wider">Start Learning</p>
                        <nav class="flex flex-col space-y-2">
                            <a href="#" class="text-sm hover:text-white transition duration-150">UX/UI Design</a>
                            <a href="#" class="text-sm hover:text-white transition duration-150">Software
                                Development</a>
                            <a href="#" class="text-sm hover:text-white transition duration-150">Workplace Skills</a>
                            <a href="#" class="text-sm hover:text-white transition duration-150">Job Search</a>
                            <a href="#" class="text-sm hover:text-white transition duration-150">Digital Freelancing</a>
                        </nav>
                    </div>

                    <!-- Column 3: Open Study Hub -->
                    <div>
                        <p class="text-xs uppercase font-semibold text-gray-400 mb-3 tracking-wider">Open Study Hub</p>
                        <nav class="flex flex-col space-y-2">
                            <a href="#" class="text-sm hover:text-white transition duration-150">Job Search</a>
                            <a href="#" class="text-sm hover:text-white transition duration-150">Soft Skills</a>
                            <a href="#" class="text-sm hover:text-white transition duration-150">Workplace Success</a>
                            <a href="#" class="text-sm hover:text-white transition duration-150">Tech careers</a>
                        </nav>
                    </div>

                    <!-- Column 4: Other Resources -->
                    <div>
                        <p class="text-xs uppercase font-semibold text-gray-400 mb-3 tracking-wider">Other Resources</p>
                        <nav class="flex flex-col space-y-2">
                            <a href="#" class="text-sm hover:text-white transition duration-150">Events</a>
                        </nav>
                    </div>

                    <!-- Column 5: About & Take Action -->
                    <div>
                        <p class="text-xs uppercase font-semibold text-gray-400 mb-3 tracking-wider">About Re:Coded</p>
                        <nav class="flex flex-col space-y-2 mb-6">
                            <a href="#" class="text-sm hover:text-white transition duration-150">Mission</a>
                            <a href="#" class="text-sm hover:text-white transition duration-150">Blog</a>
                        </nav>

                        <p class="text-xs uppercase font-semibold text-gray-400 mb-3 tracking-wider">Take Action</p>
                        <nav class="flex flex-col space-y-2">
                            <a href="#" class="text-sm hover:text-white transition duration-150">Donate</a>
                            <a href="#" class="text-sm hover:text-white transition duration-150">Partner</a>
                        </nav>
                    </div>

                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="border-t border-gray-800 mt-8 md:mt-12 py-6">
                <div
                    class="max-w-7xl mx-auto px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center text-xs text-gray-500">
                    <div class="flex flex-wrap justify-center sm:justify-start gap-x-4 gap-y-1 mb-3 sm:mb-0">
                        <a href="#" class="hover:text-gray-300">Privacy Policy</a>
                        <a href="#" class="hover:text-gray-300">Terms of Use</a>
                        <a href="#" class="hover:text-gray-300">Cookies policy</a>
                        <a href="#" class="hover:text-gray-300">Media Kit</a>
                        <a href="#" class="hover:text-gray-300">US Financials</a>
                    </div>
                    <div class="flex items-center gap-2 text-center sm:text-right">
                        <span class="font-bold text-sm text-white">your company</span>
                        <span>Copyright 2025 @ Company . All rights reserved.</span>
                    </div>
                </div>
            </div>
        </footer>

    </div>
    <!-- footer -->

    <script src="tailwind.js"></script>
</body>

</html>