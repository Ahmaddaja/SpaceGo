@extends('layouts.main')

@section('title-content')
    @include('admin.gudangs.partials.breadcrumb', ['page' => 'Edit'])
@endsection

@section('content')
<div class="container-fluid">
    <form action="{{ route('gudangs.update', $gudang->id) }}" method="POST" enctype="multipart/form-data" id="update-form">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-lg-8">
                @include('admin.gudangs.partials.form-basic-info', ['gudang' => $gudang])
                @include('admin.gudangs.partials.form-location', ['gudang' => $gudang])
            </div>

            <div class="col-lg-4">
                @include('admin.gudangs.partials.form-photo', ['gudang' => $gudang])
                @include('admin.gudangs.partials.form-actions', ['submitText' => 'Update Gudang', 'gudang' => $gudang])
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