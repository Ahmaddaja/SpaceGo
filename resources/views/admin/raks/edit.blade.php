@extends('layouts.main')

@section('title-content')
    <x-breadcrumb page="Edit" />
@endsection

@section('content')
<div class="container-fluid">
    <form action="{{ route('raks.update', $rak->id) }}" method="POST" enctype="multipart/form-data" id="update-form">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-lg-8">
                <x-form-basic-info :rak="$rak" />
                <x-form-specifications :rak="$rak" />
            </div>

            <div class="col-lg-4">
                <x-form-location-price :rak="$rak" />
                <x-form-photo :rak="$rak" />
                <x-form-actions submit-text="Update Rak" :rak="$rak" />
            </div>
        </div>
    </form>

    <form id="delete-form-{{ $rak->id }}"
          action="{{ route('raks.destroy', $rak->id) }}"
          method="POST"
          style="display: none;">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection
