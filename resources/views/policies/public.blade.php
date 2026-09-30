@extends('layouts.master')

@section('title', $policy->title)

@section('content')
<div class="container-fluid px-2">
    <x-breadcrumb :items="[
        ['name' => 'University Policies', 'url' => route('policies.index')],
        ['name' => $policy->code, 'url' => '#']
    ]" />

    <x-page-header title="{{ strtoupper($policy->categoryLabel()) }}" description="{{ $policy->code }} — v{{ $policy->version }}" backUrl="{{ route('policies.index') }}">
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm rounded-sm"><i class="ti ti-printer me-1"></i> Print</button>
    </x-page-header>

    @php
    $pages = [];
    $pageByNumber = [];
    foreach ($policy->sections ?? [] as $section) {
        if (empty($section['number']) && empty($section['title']) && empty($section['content'])) {
            continue;
        }
        $content = $section['content'] ?? '';
        $parts = preg_split('/\r?\n(?=\d+\.\d+(?:\.\d+)?\s+)/', $content, -1, PREG_SPLIT_NO_EMPTY) ?: [$content];
        $mainNumber = $section['number'] ?? '';
        $mainTitle = $section['title'] ?? '';
        $pages[] = [
            'number' => $mainNumber,
            'title' => $mainTitle,
            'content' => trim($parts[0] ?? ''),
            'is_main' => true,
        ];
        $pageByNumber[$mainNumber] = count($pages) - 1;
        for ($i = 1; $i < count($parts); $i++) {
            $part = trim($parts[$i]);
            if (preg_match('/^(\d+\.\d+(?:\.\d+)?)\s+([^\n]+)/', $part, $m)) {
                $subNumber = $m[1];
                $subTitle = trim($m[2]);
                $subContent = trim(substr($part, strlen($m[0])));
                $pages[] = [
                    'number' => $subNumber,
                    'title' => $subTitle,
                    'content' => $subContent,
                    'is_main' => false,
                ];
                $pageByNumber[$subNumber] = count($pages) - 1;
            }
        }
    }
    $currentNumber = request('page');
    $currentIndex = is_string($currentNumber) && isset($pageByNumber[$currentNumber]) ? $pageByNumber[$currentNumber] : 0;
    $currentPage = $pages[$currentIndex] ?? ['number' => '', 'title' => '', 'content' => '', 'is_main' => true];
    $totalPages = count($pages);
    $linkRegulations = function ($text) use ($pageByNumber, $currentNumber) {
        return preg_replace_callback('/\b(\d+\.\d+(?:\.\d+)*)\b/', function ($m) use ($pageByNumber, $currentNumber) {
            $n = $m[1];
            if (! isset($pageByNumber[$n])) {
                return $n;
            }
            if ((string) $currentNumber === (string) $n) {
                return '<span class="fw-semibold text-primary text-decoration-underline">'.$n.'</span>';
            }
            return '<a href="?page='.$n.'" class="fw-semibold text-primary text-decoration-none">'.$n.'</a>';
        }, $text);
    };
    @endphp

    <div class="row g-3 mb-3">
        <div class="col-md-3 col-lg-2">
            <div class="card rounded-sm mb-3">
                <div class="card-header py-1 px-2 bg-light">
                    <span style="font-size: 0.7rem; font-weight: 600;">Contents</span>
                </div>
                <div class="list-group list-group-flush" style="max-height: 70vh; overflow-y: auto; font-size: 0.65rem;">
                    @foreach($pages as $idx => $p)
                    <a href="?page={{ $p['number'] }}" class="list-group-item list-group-item-action py-1 px-2 {{ $idx === $currentIndex ? 'active' : '' }}">
                        @if($p['is_main'])
                        <strong>{{ $p['number'] }} — {{ $p['title'] }}</strong>
                        @else
                        <span class="ms-2">{{ $p['number'] }} {{ $p['title'] }}</span>
                        @endif
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-md-9 col-lg-10">
            <div class="book-page p-4 p-md-5 mb-3" style="background: #fff; min-height: 70vh; border-radius: 0.5rem; border: 1px solid #e0e0e0; box-shadow: 0 8px 30px rgba(0,0,0,0.12);">
                <div class="mb-4 border-bottom pb-3">
                    <h1 class="fw-bold" style="font-size: 1.1rem; color: #0d6efd;">{{ $currentPage['number'] }} — {{ $currentPage['title'] }}</h1>
                </div>
                <div style="font-size: 0.8rem; line-height: 1.75; white-space: normal;">
                    {!! nl2br($linkRegulations(e($currentPage['content']))) !!}
                </div>
                <div class="text-center text-muted mt-5 pt-3 border-top" style="font-size: 0.7rem;">
                    Page {{ $currentIndex + 1 }} of {{ $totalPages }}
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                @if($currentIndex > 0)
                <a href="?page={{ $pages[$currentIndex - 1]['number'] }}" class="btn btn-outline-secondary btn-sm rounded-sm">← Previous</a>
                @else
                <span></span>
                @endif
                @if($currentIndex < $totalPages - 1)
                <a href="?page={{ $pages[$currentIndex + 1]['number'] }}" class="btn btn-outline-secondary btn-sm rounded-sm">Next →</a>
                @endif
            </div>
        </div>
    </div>


    @if($policy->document_path)
    <div class="card rounded-sm mb-3">
        <div class="card-header py-1 px-2 bg-light">
            <span style="font-size: 0.7rem; font-weight: 600;">Attached Document</span>
        </div>
        <div class="card-body py-2 px-3" style="font-size: 0.75rem;">
            <a href="{{ asset('storage/'.$policy->document_path) }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-sm">
                <i class="ti ti-download me-1"></i> Download Document
            </a>
        </div>
    </div>
    @endif
</div>
@endsection
