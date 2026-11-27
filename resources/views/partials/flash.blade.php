{{-- Notifikasi toast (pojok kanan atas). Sumber: session('toast'), session('success'), session('error') --}}
@php
    $toasts = [];
    if (is_array(session('toast'))) {
        $toasts[] = session('toast');
    }
    if (session('success')) {
        $toasts[] = ['type' => 'success', 'title' => null, 'message' => session('success')];
    }
    if (session('error')) {
        $toasts[] = ['type' => 'error', 'title' => null, 'message' => session('error')];
    }
@endphp
<div class="toasts" aria-live="polite" aria-atomic="false">
    @foreach ($toasts as $t)
        <div class="toast toast-{{ $t['type'] }}" role="{{ $t['type'] === 'error' ? 'alert' : 'status' }}">
            <span class="toast-icon" aria-hidden="true">
                @switch($t['type'])
                    @case('login')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><path d="m10 17 5-5-5-5"/><path d="M15 12H3"/></svg>
                        @break
                    @case('logout')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/></svg>
                        @break
                    @case('error')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 7.5v5.5M12 16.5v.01"/></svg>
                        @break
                    @default
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg>
                @endswitch
            </span>
            <div class="toast-body">
                @if (! empty($t['title']))<b>{{ $t['title'] }}</b>@endif
                <span>{{ $t['message'] }}</span>
            </div>
            <button type="button" class="toast-close" aria-label="Tutup notifikasi">&times;</button>
            <span class="toast-timer" aria-hidden="true"></span>
        </div>
    @endforeach
</div>
