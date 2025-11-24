@extends('layouts.main')

@section('title-content')
    @include('admin.gudangs.partials.breadcrumb', ['page' => 'Show'])
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            @include('admin.gudangs.partials.detail-info', ['gudang' => $gudang])
            @include('admin.gudangs.partials.detail-rak-list', ['gudang' => $gudang])
        </div>

        <div class="col-lg-4">
            @include('admin.gudangs.partials.detail-photo', ['gudang' => $gudang])
            @include('admin.gudangs.partials.detail-actions', ['gudang' => $gudang])
        </div>
    </div>
</div>
@endsection