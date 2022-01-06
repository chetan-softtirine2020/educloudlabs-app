@component('mail::message')
{{ $details['name'] }}
# Introduction
  
{{ $details['message'] }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
