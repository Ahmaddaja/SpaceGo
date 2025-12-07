@extends('layouts.main')

@section('title-content')
    <x-breadcump-rak page="Edit" module="Rak" routePrefix="raks" />
@endsection

@section('content')
    <div class="container-fluid">
        @php
            // Cek apakah rak memiliki transaksi aktif
            $hasActiveTransaction = \App\Models\Transaction::where('rak_id', $rak->id)
                ->whereIn('transaction_status', ['capture', 'settlement'])
                ->exists();
        @endphp

        {{-- Alert untuk rak yang sedang terisi --}}
        @if ($rak->status === 'terisi' && $hasActiveTransaction)
            <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle fa-2x mr-3"></i>
                    <div>
                        <h5 class="alert-heading mb-1">Perhatian!</h5>
                        <p class="mb-0">
                            Rak ini sedang dalam status <strong>TERISI</strong> dan memiliki transaksi aktif.
                            Status tidak dapat diubah hingga masa sewa berakhir.
                        </p>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <form action="{{ route('raks.update', $rak->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-lg-8">
                    @include('admin.raks.partials.form-basic-info')
                    @include('admin.raks.partials.form-specifications')
                </div>

                <div class="col-lg-4">
                    @include('admin.raks.partials.form-location-price')
                    @include('admin.raks.partials.form-photo')
                    @include('admin.raks.partials.form-actions', ['submitText' => 'Update Rak'])
                </div>
            </div>
        </form>

        <!-- Form Delete Terpisah -->
        <form id="delete-form-{{ $rak->id }}" action="{{ route('raks.destroy', $rak->id) }}" method="POST"
            style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    </div>
@endsection
