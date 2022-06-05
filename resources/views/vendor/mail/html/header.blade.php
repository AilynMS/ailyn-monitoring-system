<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
{{-- @if (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">
@else
{{ $slot }}
@endif --}}
<img src="{{ config('app.client_url')  }}/logo_default.svg" class="logo" alt="AilynMS Logo">

</a>
</td>
</tr>
