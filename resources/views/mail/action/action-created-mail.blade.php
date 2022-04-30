@component('mail::message')
# Hi {{ $notifiable->first_name }}

Your approval is required on a pending action.

@component('mail::button', ['url' => ''])
Login
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
