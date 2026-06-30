@if (session('status'))
    <p class="flash-message">{{ session('status') }}</p>
@endif

@if (session('error'))
    <p class="flash-error">{{ session('error') }}</p>
@endif
