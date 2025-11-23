@extends('layouts.main')

@section('title-content')
    @include('admin.raks.partials.breadcrumb', ['page' => 'Create'])
@endsection

@section('content')
<div class="container-fluid">
    <form action="{{ route('raks.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row">
            <div class="col-lg-8">
                @include('admin.raks.partials.form-basic-info')
                @include('admin.raks.partials.form-specifications')
            </div>

            <div class="col-lg-4">
                @include('admin.raks.partials.form-location-price')
                @include('admin.raks.partials.form-photo')
                @include('admin.raks.partials.form-actions', ['submitText' => 'Simpan Rak'])
            </div>
        </div>
    </form>
</div>
@endsection