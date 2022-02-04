@component('mail::message')
    {{ $details['name'] }}
    You are addedd in educloud live trainig setion please click on button.
    {{ $details['description'] }}
    @component('mail::button', ['url' => $details['link']])
        Join
    @endcomponent
    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
