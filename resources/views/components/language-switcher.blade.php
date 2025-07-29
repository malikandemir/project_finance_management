<div class="flex items-center space-x-2">
    <button 
        onclick="switchLanguage('en')" 
        class="flex items-center {{ app()->getLocale() == 'en' ? 'font-bold' : 'opacity-70' }}">
        <span class="ml-1">EN</span>
    </button>
    <span class="text-gray-300">|</span>
    <button 
        onclick="switchLanguage('tr')" 
        class="flex items-center {{ app()->getLocale() == 'tr' ? 'font-bold' : 'opacity-70' }}">
        <span class="ml-1">TR</span>
    </button>
</div>

<script>
    function switchLanguage(locale) {
        // Use direct links with query parameters instead of form submission
        const currentUrl = window.location.href;
        const langSwitchUrl = '{{ route("lang.switch", "__LOCALE__") }}'.replace('__LOCALE__', locale);
        
        // Create the full URL with the redirect parameter
        const redirectUrl = langSwitchUrl + '?redirect=' + encodeURIComponent(currentUrl);
        
        // Navigate to the URL
        window.location.href = redirectUrl;
    }
</script>
