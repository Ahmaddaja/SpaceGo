@extends('layouts.main')

@section('title-content')
    <div class="d-flex justify-content-between align-items-right">
        <h1 class="m-0">Detail Transaksi</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 m-0 justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.transactions.index') }}">Transaksi</a></li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>
        </nav>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <x-alert />

    <div class="row">
        <div class="col-lg-8">
            @include('admin.transactions.partials.detail-info')
            @include('admin.transactions.partials.detail-payment')
        </div>

        <div class="col-lg-4">
            @include('admin.transactions.partials.detail-customer')
            @include('admin.transactions.partials.detail-rak')
            @include('admin.transactions.partials.detail-actions')
        </div>
    </div>
</div>
@endsection