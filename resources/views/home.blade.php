@extends('layouts.app')

@push('styles')
    @foreach (['hero', 'profil', 'galeri', 'testimoni', 'program', 'lokasi', 'kontak'] as $css)
        <link rel="stylesheet" href="{{ asset("css/{$css}.css") }}">
    @endforeach
@endpush

@section('content')
    {{-- Beranda = Hero + Profil + Galeri + Testimoni --}}
    <div id="beranda">
        @include('sections.hero')
        @include('sections.profil')
        @include('sections.galeri')
        @include('sections.testimoni')
    </div>

    @include('sections.program')
    @include('sections.lokasi')
    @include('sections.kontak')
@endsection
