@component('mail::message')
Hi {{$name}},

We received a request for reset your Educloudlabs account password. To reset your password, please click the button below.

@component('mail::button', ['url' => $url])
Reset Password
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
