<x-mail::message>
# SMS Credit Alert

<x-mail::panel>
    **Warning:** Your SMS account balance is running low!
</x-mail::panel>

Hello {{ $admin->name }},

We're writing to inform you that your SMS credit balance is below the recommended threshold.

**Current Balance:** GHS {{ number_format($balance, 3) }} <br>
**Threshold:** GHS {{ number_format($threshold, 3) }}

Please top up your SMS credit to avoid any service disruptions when sending message broadcasts.

To add credit, please visit the admin portal.

<x-mail::button :url="route('filament.admin.resources.message-broadcasts.index')">
Top Up
</x-mail::button>

---

This is an automated notification from {{ config('app.name') }}. Please do not reply to this email.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
