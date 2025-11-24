@extends('layouts.main')

@section('title-content')
    <xbreadcrumb page="Create" />
@endsection

@section('content')
<div class="container-fluid">
    <form action="{{ route('raks.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row">
            <div class="col-lg-8">
                <x-form-basic-info />
                <x-form-specifications />
            </div>

            <div class="col-lg-4">
                <x-form-location />
                <x-form-photo />
                <x-form-actions submit-text="Simpan Rak" />
            </div>
        </div>
    </form>
</div>
@endsection