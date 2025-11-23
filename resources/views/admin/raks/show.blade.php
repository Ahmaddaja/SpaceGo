@extends('layouts.main')

@section('title-content')
    @include('admin.raks.partials.breadcrumb', ['page' => 'Show'])
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            @include('admin.raks.partials.detail-info', ['rak' => $rak])
            @include('admin.raks.partials.detail-specifications', ['rak' => $rak])
        </div>

        <div class="col-lg-4">
            @include('admin.raks.partials.detail-photo', ['rak' => $rak])
            @include('admin.raks.partials.detail-actions', ['rak' => $rak])
        </div>
    </div>
</div>
@endsection