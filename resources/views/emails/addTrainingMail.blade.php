@component('mail::message')
    Hi, {{ $details['user_name'] }},<br>
    I would like to you invite  attend a training on Educloudlabs.
    {{ $details['description'] }}
    @component('mail::button', ['url' => $details['link']])
        Join
    @endcomponent
    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
