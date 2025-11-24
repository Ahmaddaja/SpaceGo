@extends('layouts.main')

@section('title-content')
    <x-breadcrumb page="Create" module="Gudang" routePrefix="gudangs" />
@endsection

@section('content')
<div class="container-fluid">
    <form action="{{ route('gudangs.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row">
            <div class="col-lg-8">
                @include('admin.gudangs.partials.form-basic-info')
                @include('admin.gudangs.partials.form-location')
            </div>

            <div class="col-lg-4">
                @include('admin.gudangs.partials.form-photo')
                @include('admin.gudangs.partials.form-actions', ['submitText' => 'Simpan Gudang'])
            </div>
        </div>
    </form>
</div>
@endsection
