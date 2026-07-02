@extends('layouts.portal')

@section('title', 'System Portal')

@section('systems')
    @foreach($systems as $system)
    <a href="{{ isset($system['coming_soon']) && $system['coming_soon'] ? '#' : $system['url'] }}" 
       class="system-card {{ isset($system['coming_soon']) && $system['coming_soon'] ? 'coming-soon' : '' }}"
       @if(isset($system['coming_soon']) && $system['coming_soon']) onclick="return false;" @endif>
        <div class="icon-circle bg-{{ $system['color'] }}">
            @if($system['icon'] == 'calendar')
                <i class="ti ti-calendar"></i>
            @elseif($system['icon'] == 'mail')
                <i class="ti ti-mail"></i>
            @elseif($system['icon'] == 'clipboard')
                <i class="ti ti-clipboard"></i>
            @elseif($system['icon'] == 'users')
                <i class="ti ti-users"></i>
            @elseif($system['icon'] == 'settings')
                <i class="ti ti-settings"></i>
            @else
                <i class="ti ti-apps"></i>
            @endif
        </div>
        <div class="system-name">{{ $system['name'] }}</div>
        <div class="system-desc">{{ $system['description'] }}</div>
        @if(isset($system['coming_soon']) && $system['coming_soon'])
            <span class="badge bg-secondary rounded-sm coming-badge">Coming Soon</span>
        @else
            <span class="btn-access btn-{{ $system['color'] }}">Access System</span>
        @endif
    </a>
    @endforeach
@endsection