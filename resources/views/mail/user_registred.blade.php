@component('mail::message')
# Hello, {{ $usermeta->firstname }}!

Your account at {!! '<a href="' . config('app.url') . '">' . config('app.name') . '</a>' !!} created successfully.

@component('mail::button', ['url' => url('/')])
Go to site
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
