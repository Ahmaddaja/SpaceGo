@extends('layouts.main')

@section('title-content')
    <x-breadcrumb page="Create" />
@endsection

@section('content')
<div class="container-fluid">
    <form action="{{ route('gudangs.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row">
            <div class="col-lg-8">
                <x-form-basic-info />
                <x-form-location />
            </div>

            <div class="col-lg-4">
                <x-form-photo />
                <x-form-actions submit-text="Simpan Gudang" />
            </div>
        </div>
    </form>
</div>
@endsection
