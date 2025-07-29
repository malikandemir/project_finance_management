<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Language Test') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-center p-4">
    <div class="bg-white p-8 rounded-lg shadow-md max-w-2xl w-full">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">{{ __('Language Test') }}</h1>
            <x-language-switcher />
        </div>

        <div class="space-y-4">
            <div class="border-b pb-4">
                <h2 class="text-xl font-semibold mb-2">{{ __('Authentication') }}</h2>
                <p><strong>{{ __('Failed:') }}</strong> {{ __('auth.failed') }}</p>
                <p><strong>{{ __('Password:') }}</strong> {{ __('auth.password') }}</p>
                <p><strong>{{ __('Throttle:') }}</strong> {{ __('auth.throttle', ['seconds' => 60]) }}</p>
            </div>

            <div class="border-b pb-4">
                <h2 class="text-xl font-semibold mb-2">{{ __('Pagination') }}</h2>
                <p><strong>{{ __('Previous:') }}</strong> {!! __('pagination.previous') !!}</p>
                <p><strong>{{ __('Next:') }}</strong> {!! __('pagination.next') !!}</p>
            </div>

            <div class="border-b pb-4">
                <h2 class="text-xl font-semibold mb-2">{{ __('Passwords') }}</h2>
                <p><strong>{{ __('Reset:') }}</strong> {{ __('passwords.reset') }}</p>
                <p><strong>{{ __('Sent:') }}</strong> {{ __('passwords.sent') }}</p>
                <p><strong>{{ __('Throttled:') }}</strong> {{ __('passwords.throttled') }}</p>
                <p><strong>{{ __('Token:') }}</strong> {{ __('passwords.token') }}</p>
                <p><strong>{{ __('User:') }}</strong> {{ __('passwords.user') }}</p>
            </div>

            <div>
                <h2 class="text-xl font-semibold mb-2">{{ __('Validation') }}</h2>
                <p><strong>{{ __('Accepted:') }}</strong> {{ __('validation.accepted', ['attribute' => 'terms']) }}</p>
                <p><strong>{{ __('Email:') }}</strong> {{ __('validation.email', ['attribute' => 'email address']) }}</p>
                <p><strong>{{ __('Required:') }}</strong> {{ __('validation.required', ['attribute' => 'name']) }}</p>
            </div>
        </div>

        <div class="mt-6 text-center text-gray-500 text-sm">
            {{ __('Current locale:') }} <strong>{{ app()->getLocale() }}</strong>
        </div>
    </div>
</body>
</html>
