<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - Property Management SaaS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">

    <div class="max-w-4xl mx-auto py-20 px-6 text-center">
        <h1 class="text-5xl font-bold text-gray-900 mb-4">
            {{ config('app.name') }}
        </h1>
        <p class="text-2xl text-gray-600 mb-10">
            Smart Property Management for Property Managers &amp; Landlords
        </p>

        <div class="bg-white shadow-xl rounded-2xl p-10 max-w-lg mx-auto">
            <p class="text-lg text-gray-700 mb-8">
                Run your entire property business in one secure platform.<br>
                Multiple companies can use the system — each seeing only their own data.
            </p>

            <div class="space-y-4">
                <a href="#" 
                   class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-4 rounded-xl text-lg transition">
                    Get Started - 14 Days Free Trial
                </a>
                
                <a href="#" 
                   class="block w-full border border-gray-300 hover:bg-gray-50 font-semibold py-4 rounded-xl text-lg transition">
                    Watch 2-Minute Demo
                </a>
            </div>
        </div>

        <p class="mt-12 text-sm text-gray-500">
            Built with Laravel • Filament • Citus • PostgreSQL
        </p>
    </div>

</body>
</html>