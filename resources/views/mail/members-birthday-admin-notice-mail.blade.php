<x-mail::message>
# Birthday Notice for {{ $birthdayMembers->count() }} Member(s)

<x-mail::panel>
{{ $message }}
</x-mail::panel>

@if ($birthdayMembers->isNotEmpty())
<x-mail::table>
| Name | Phone | Email |
|:-----|------:|------:|
@foreach ($birthdayMembers as $member)
| {{ $member->name }} | {{ $member->phone ?: $member->contactPerson?->phone ?? 'N/A' }} | {{ $member->email ?: $member->contactPerson?->email ?? 'N/A' }} |
@endforeach
</x-mail::table>
@else
    No birthday members found.
@endif

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
