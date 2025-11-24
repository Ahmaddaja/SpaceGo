@extends('layouts.main')

@section('title-content')
    <x-breadcrumb page="Show" />
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">

        <div class="col-lg-8">
            <x-detail-info :gudang="$gudang" />
            <x-detail-rak-list :gudang="$gudang" />
        </div>

        <div class="col-lg-4">
            <x-detail-photo :gudang="$gudang" />
            <x-detail-actions :gudang="$gudang" />
        </div>

    </div>
</div>
@endsection
