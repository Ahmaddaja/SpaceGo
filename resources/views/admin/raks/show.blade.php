@extends('layouts.main')

@section('title-content')
    <x-breadcrumb page="Show" />
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">

        <div class="col-lg-8">
            <x-detail-info :rak="$rak" />
            <x-detail-specifications :rak="$rak" />
        </div>

        <div class="col-lg-4">
            <x-detail-photo :rak="$rak" />
            <x-detail-actions :rak="$rak" />
        </div>

    </div>
</div>
@endsection
