@extends('layouts.main')

@section('title-content')
   <x-breadcump-rak page="Edit" module="Rak" routePrefix="raks" />
@endsection

@section('content')
    <div class="container-fluid">
        <form action="{{ route('raks.update', $rak->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-lg-8">
                    @include('admin.raks.partials.form-basic-info')
                    @include('admin.raks.partials.form-specifications')
                </div>

                <div class="col-lg-4">
                    @include('admin.raks.partials.form-location-price')
                    @include('admin.raks.partials.form-photo')
                    @include('admin.raks.partials.form-actions', ['submitText' => 'Update Rak'])
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
