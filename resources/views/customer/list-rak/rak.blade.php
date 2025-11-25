@extends('layouts.app') {{-- sesuaikan dengan layout kamu --}}
@section('content')

<div class="max-w-6xl mx-auto px-4 py-8">

    <h1 class="text-3xl font-bold text-gray-800 mb-6">Rak yang Sudah Dibeli</h1>

    @if($raks->count() == 0)
        <div class="bg-yellow-100 text-yellow-700 px-4 py-3 rounded">
            Anda belum membeli rak apa pun.
        </div>
    @else

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        @foreach($raks as $rak)
        <div class="bg-white shadow rounded-xl overflow-hidden">
            
            {{-- Foto Rak --}}
            <img src="{{ $rak->foto ? asset('storage/' . $rak->foto) : 'https://via.placeholder.com/300' }}"
                 class="w-full h-48 object-cover">

            <div class="p-5 space-y-2">
                <h2 class="text-xl font-semibold text-gray-800">{{ $rak->nama_rak }}</h2>
                
                <p class="text-gray-600 text-sm">Kode Rak: <b>{{ $rak->kode_rak }}</b></p>
                <p class="text-gray-600 text-sm">Jenis: <b>{{ $rak->jenis_rak }}</b></p>

                <p class="text-gray-600 text-sm">Lokasi Gudang: 
                    <b>{{ $rak->gudang->nama_gudang ?? $rak->lokasi_gudang }}</b>
                </p>

                <p class="text-gray-600 text-sm">Harga Sewa:
                    <span class="text-green-600 font-bold">
                        Rp {{ number_format($rak->harga_sewa_perbulan, 0, ',', '.') }}/bulan
                    </span>
                </p>

                {{-- Status --}}
                <span class="inline-block px-3 py-1 rounded text-sm 
                    {{ $rak->status == 'terisi' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-700' }}">
                    {{ ucfirst($rak->status) }}
                </span>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $raks->links() }}
    </div>

    @endif

</div>

@endsection
