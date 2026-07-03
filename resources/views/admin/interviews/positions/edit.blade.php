@extends('layouts.admin')

@php
    $routePrefix = auth()->user()->role->slug === 'hr-manager'
        ? 'hr.'
        : '';
@endphp

@section('title')Edit Position @endsection

@section('header')
  <div class="d-flex align-items-center justify-content-between mb-4">
    <div>
      <a href="{{ route($routePrefix . 'positions.index') }}" class="text-muted text-decoration-none small">
        <i class="fas fa-arrow-left me-1"></i> Back to Positions
      </a>
      <h1 class="h3 mt-1 mb-0">Edit: {{ $position->title }}</h1>
    </div>
  </div>
@endsection

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="fas fa-briefcase me-2 text-warning"></i>Position Details</h5>
      </div>
      <div class="card-body">
        <form action="{{ route($routePrefix . 'positions.update', $position) }}" method="POST">
          @csrf @method('PUT')
          <div class="mb-3">
            <label class="form-label">Job Title <span class="text-danger">*</span></label>
            <input type="text" name="title" class="form-control"
                   value="{{ old('title', $position->title) }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Department <span class="text-danger">*</span></label>
            <input type="text" name="department" class="form-control"
                   value="{{ old('department', $position->department) }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Location</label>
            <input type="text" name="location" class="form-control"
                   value="{{ old('location', $position->location) }}">
          </div>
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label">Employment Type</label>
              <select name="type" class="form-select">
                @foreach($types as $key => $label)
                  <option value="{{ $key }}" {{ $position->type === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Open Positions</label>
              <input type="number" name="openings" class="form-control"
                     value="{{ old('openings', $position->openings) }}" min="1">
            </div>
          </div>
          <div class="mb-3">
            <div class="form-check form-switch">
              <input type="hidden" name="is_active" value="0">
              <input type="checkbox" name="is_active" value="1"
                     class="form-check-input" id="is_active"
                     {{ $position->is_active ? 'checked' : '' }}>
              <label class="form-check-label" for="is_active">Position is active</label>
            </div>
          </div>
          <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save me-1"></i>Save Changes
            </button>
            <a href="{{ route($routePrefix . 'positions.index') }}" class="btn btn-light border">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
