@extends('layouts.main')

@section('title-content')
    <x-breadcrumb page="Edit" module="Gudang" routePrefix="gudangs" />
@endsection

section('content')
<div class="container-fluid">
    <form action="{{ route('gudangs.update', $gudang->id) }}" method="POST" enctype="multipart/form-data" id="update-form">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-lg-8">
                @include('admin.gudangs.partials.form-basic-info')
                @include('admin.gudangs.partials.form-location')
            </div>

            <div class="col-lg-4">
                @include('admin.gudangs.partials.form-photo')
                @include('admin.gudangs.partials.form-actions', ['submitText' => 'Update Gudang'])
            </div>
        </div>
    </form>
    
    <form id="delete-form-{{ $gudang->id }}"
          action="{{ route('gudangs.destroy', $gudang->id) }}"
          method="POST" 
          style="display: none;">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection
