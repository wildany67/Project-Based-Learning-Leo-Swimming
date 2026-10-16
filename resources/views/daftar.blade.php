@extends('layouts.app')

@section('title', 'Daftar | Leo Swimming Course')
@section('body_class', 'page-daftar')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/daftar.css') }}">
@endpush

@section('content')
<section class="daftar">
    <div class="container daftar-grid">

        <div class="daftar-intro">
            <p class="eyebrow">Siap memulai?</p>
            <h1 class="daftar-title">Langkah pertamamu dimulai <span class="text-blue">hari ini.</span></h1>
            <p>Isi data singkat untuk memulai konsultasi program dan jadwal latihan melalui WhatsApp.</p>
            <ul class="checks">
                <li>Konsultasi program</li>
                <li>Konfirmasi jadwal &amp; lokasi</li>
                <li>Informasi biaya sebelum latihan</li>
            </ul>
        </div>

        <form class="form-card" method="POST" action="{{ route('daftar.kirim') }}" novalidate>
            @csrf

            @if ($errors->any())
                <div class="form-alert" role="alert">
                    <strong>Mohon lengkapi data berikut:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <fieldset>
                <legend>Data Orang Tua</legend>
                <div class="form-row">
                    <label class="field">
                        <span>Nama orang tua</span>
                        <input type="text" name="parent_name" value="{{ old('parent_name') }}" placeholder="Masukkan nama lengkap orang tua" autocomplete="name" required>
                    </label>
                    <label class="field">
                        <span>Nomor HP</span>
                        <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="Contoh: 081234567890" inputmode="tel" autocomplete="tel" required>
                    </label>
                </div>
            </fieldset>

            <fieldset>
                <legend>Data Anak</legend>
                <div class="form-row">
                    <label class="field">
                        <span>Nama anak</span>
                        <input type="text" name="child_name" value="{{ old('child_name') }}" placeholder="Masukkan nama lengkap anak" required>
                    </label>
                    <div class="field">
                        <span>Jenis kelamin</span>
                        <div class="pills">
                            @foreach (['Laki-laki', 'Perempuan'] as $g)
                                <label class="pill">
                                    <input type="radio" name="gender" value="{{ $g }}" @checked(old('gender') === $g) required>
                                    <b>{{ $g }}</b>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <label class="field">
                        <span>Usia anak</span>
                        <input type="text" name="age" value="{{ old('age') }}" placeholder="Contoh: 6 tahun" required>
                    </label>
                    <label class="field">
                        <span>Pilih paket</span>
                        <select name="package" required>
                            <option value="">Pilih paket</option>
                            @foreach ($packageOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('package', $selectedPackage) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>
            </fieldset>

            <fieldset>
                <legend>Pilihan Kelas</legend>
                <div class="form-row">
                    <label class="field">
                        <span>Lokasi kolam</span>
                        <select name="location" required>
                            <option value="">Pilih lokasi</option>
                            @foreach ($locations as $loc)
                                <option value="{{ $loc['pool'] }}" @selected(old('location') === $loc['pool'])>{{ $loc['pool'] }} ({{ $loc['area'] }})</option>
                            @endforeach
                        </select>
                    </label>
                    <div class="field">
                        <span>Jadwal</span>
                        <div class="pills">
                            @foreach ($days as $day)
                                <label class="pill">
                                    <input type="checkbox" name="schedule[]" value="{{ $day }}" @checked(in_array($day, old('schedule', [])))>
                                    <b>{{ $day }}</b>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <label class="field">
                    <span>Kapan ingin mulai?</span>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" min="{{ date('Y-m-d') }}" required>
                </label>
            </fieldset>

            <div class="form-submit">
                <button type="submit" class="btn btn--primary btn--lg">Daftar Sekarang</button>
            </div>
        </form>
    </div>
</section>
@endsection
