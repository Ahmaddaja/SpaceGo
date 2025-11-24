@extends('layouts.main')

@section('title-content')
    <x-breadcrumb page="Edit" />
@endsection

section('content')
<div class="container-fluid">
    <form action="{{ route('gudangs.update', $gudang->id) }}" method="POST" enctype="multipart/form-data" id="update-form">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-lg-8">
                <x-form-basic-info :gudang="$gudang" />
                <x-form-location :gudang="$gudang" />
            </div>

            <div class="col-lg-4">
                <x-form-photo :gudang="$gudang" />
                <x-form-actions submit-text="Update Gudang" :gudang="$gudang" />
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
