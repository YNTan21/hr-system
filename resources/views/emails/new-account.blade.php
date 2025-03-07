<x-mail::message>
# Welcome to HR System

Hello {{ $user->username }},

Your account has been created by the administrator. Here are your login details:

**Email:** {{ $user->email }}
**Temporary Password:** {{ $password }}

Please login and change your password immediately for security purposes.

<x-mail::button :url="route('login')">
Login to Your Account
</x-mail::button>

If you have any questions, please contact your administrator.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message> 