@component('mail::message')
 Hi,{{ $details['name'] }}
# Introduction
  
{{ $details['message'] }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
