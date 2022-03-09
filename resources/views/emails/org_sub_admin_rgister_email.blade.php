@component('mail::message')
  Hi, {{ $details['name'] }},<br>

  {{ $details['description'] }}

@component('mail::button', ['url' => $details['link']])
 Login
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
