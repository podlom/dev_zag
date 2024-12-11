@component('mail::message')
# Congratulations

New referral has joined your network!

@component('mail::button', ['url' => url('/')])
Go to site
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
