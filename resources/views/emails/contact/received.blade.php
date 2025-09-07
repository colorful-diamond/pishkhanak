<x-mail::message>
# New Contact Form Submission

A new message has been submitted through the contact form on your website.

**Name:** {{ $messageData->name }}
**Email:** {{ $messageData->email }}
**Subject:** {{ $messageData->subject }}

**Message:**
<x-mail::panel>
{{ $messageData->message }}
</x-mail::panel>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message> 