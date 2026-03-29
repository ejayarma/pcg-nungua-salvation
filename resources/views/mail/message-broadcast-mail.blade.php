<x-mail::message>
# {{ $messageBroadcast->title }}

{{-- <x-mail::panel class="p-4 text-gray-700 bg-gray-100 border-l-4 border-gray-500"> --}}
<x-mail::panel >
{{ $messageBroadcast->message }}
</x-mail::panel>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
