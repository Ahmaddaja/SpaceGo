@extends('layouts.main')

@section('title-content')
    @include('admin.raks.partials.breadcrumb', ['page' => 'Edit'])
@endsection

@section('content')
<div class="container-fluid">
    <form action="{{ route('raks.update', $rak->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-lg-8">
                @include('admin.raks.partials.form-basic-info', ['rak' => $rak])
                @include('admin.raks.partials.form-specifications', ['rak' => $rak])
            </div>

            <div class="col-lg-4">
                @include('admin.raks.partials.form-location-price', ['rak' => $rak])
                @include('admin.raks.partials.form-photo', ['rak' => $rak])
                @include('admin.raks.partials.form-actions', ['submitText' => 'Update Rak', 'rak' => $rak])
            </div>
        </div>
    </form>
</div>
@endsection