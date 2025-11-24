@extends('layouts.main', ['title' => 'Profile'])

@section('styles')
    @include('admin.profile.partials.styles')
@endsection

@section('title-content')
<div class="d-flex justify-content-between align-items-right">
    <h1 class="m-0">Profile</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent p-0 m-0 justify-content-end small">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item active">Profile</li>
        </ol>
    </nav>
</div>
@endsection

@section('content')
<div class="row">
    {{-- Profile Card --}}
    @include('admin.profile.partials.profile-card')

    {{-- Edit Profile + Change Password --}}
    <div class="col-lg-8 col-12 mb-3 ml-auto">
        @include('admin.profile.partials.edit-profile-form')
        @include('admin.profile.partials.change-password-form')
    </div>
</div>

@include('admin.profile.partials.scripts')
@endsection
