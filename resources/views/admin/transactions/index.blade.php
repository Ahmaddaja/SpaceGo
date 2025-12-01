@extends('layouts.main', ['title' => 'Riwayat Transaksi'])

@section('title-content')
    <div class="d-flex justify-content-between align-items-right">
        <h1 class="m-0">Riwayat Transaksi</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 m-0 justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Transaksi</li>
            </ol>
        </nav>
    </div>  
@endsection

@section('content')
    <div class="container-fluid">
        <x-alert />

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0 font-weight-bold">Daftar Transaksi</h5>
                    </div>
                    <div class="col-md-6 text-right">
                        @include('admin.transactions.partials.action-buttons')
                    </div>
                </div>
            </div>

            <div class="card-body">
                @include('admin.transactions.partials.filter-form')
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        @include('admin.transactions.partials.table-header')
                        <tbody>
                            @forelse($transactions as $transaction)
                                @include('admin.transactions.partials.table-row')
                            @empty
                                @include('admin.transactions.partials.empty-state')
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($transactions->hasPages())
                <div class="card-footer bg-white border-0">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection