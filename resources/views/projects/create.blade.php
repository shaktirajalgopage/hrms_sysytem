@extends('layouts.admin')

@section('title')
    {{ __('New Project') }}
@endsection

@section('header')
    <h1 class="h3 mb-3"><strong>Create</strong> Project</h1>
@endsection

@section('content')
    <div class="card panel-card border-0">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('projects.store') }}">
                @csrf
                @include('projects._form')
            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('partials.ptm-styles')
@endsection
