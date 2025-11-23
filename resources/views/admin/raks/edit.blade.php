@extends('layouts.main')

@section('title-content')
    @include('admin.raks.partials.breadcrumb', ['page' => 'Edit'])
@endsection

@section('content')
<div class="container-fluid">
    <form action="{{ route('raks.update', $rak->id) }}" method="POST" enctype="multipart/form-data" id="update-form">
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
    
    <!-- Form Delete Terpisah -->
    <form id="delete-form-{{ $rak->id }}" 
          action="{{ route('raks.destroy', $rak->id) }}" 
          method="POST" 
          style="display: none;">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection