@extends('layouts.app')
@section('title', 'Beranda · BeasiswaKu')
@section('content')
<div class="container home-page"><section class="hero"><div><h1>Ayo Daftar Beasiswa Sekarang!</h1><p>Temukan berbagai informasi beasiswa yang tersedia untuk mendukung pendidikanmu.</p></div><img src="{{ asset('images/education-award.png') }}" alt="Ilustrasi pendidikan"></section>
<form class="quick-search" action="{{ route('scholarships.search') }}"><span class="search-icon">⌕</span><input name="q" aria-label="Cari beasiswa" placeholder="Cari nama beasiswa atau penyelenggara..."><button class="button" type="submit">Cari <span>→</span></button></form>
<div class="card-grid">@forelse($scholarships as $scholarship)<a class="scholarship-card" href="{{ route('scholarships.show', $scholarship) }}"><div class="card-meta"><span class="level-pill">{{ $scholarship->level }}</span><span class="deadline-pill">◷ {{ $scholarship->deadline->translatedFormat('d M Y') }}</span></div><h2>{{ $scholarship->name }}</h2><span class="provider-pill">♧ {{ $scholarship->provider }}</span><p>{{ $scholarship->summary }}</p></a>@empty<div class="empty">Belum ada beasiswa aktif saat ini.</div>@endforelse</div>
<div class="center"><a class="button" href="{{ route('scholarships.search') }}">Jelajahi semua beasiswa →</a></div></div>
@endsection
