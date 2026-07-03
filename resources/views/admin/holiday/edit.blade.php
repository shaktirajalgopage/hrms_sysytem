@extends('layouts.admin')

@php
    $routePrefix = auth()->user()->role->slug === 'hr-manager'
        ? 'hr.'
        : '';
@endphp

@section('title')
    {{ __('Edit Holiday') }}
@endsection

@section('header')
  <div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-3">Edit Holiday</h1>
    <a href="{{ route($routePrefix . 'holiday.index') }}" class="btn btn-secondary">
      <i class="fas fa-arrow-left"></i> Back to List
    </a>
  </div>
@endsection

@section('content')
  <section class="row">
    <div class="col-12 col-md-8 offset-md-2">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">Modify Holiday Details</h5>
        </div>
        <div class="card-body">
          <form action="{{ route($routePrefix . 'holiday.update', $holiday->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
              <label for="name" class="form-label">Holiday Name <span class="text-danger">*</span></label>
              <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $holiday->name) }}" required>
              @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', $holiday->start_date ? $holiday->start_date->format('Y-m-d') : '') }}" required>
                @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-6 mb-3">
                <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date', $holiday->end_date ? $holiday->end_date->format('Y-m-d') : '') }}" required>
                @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>

            <div class="mb-3">
              <label for="no_of_days" class="form-label">Number of Days <span class="text-danger">*</span></label>
              <input type="number" name="no_of_days" id="no_of_days" min="1" class="form-control @error('no_of_days') is-invalid @enderror" value="{{ old('no_of_days', $holiday->no_of_days) }}" required>
              @error('no_of_days') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
              <label for="description" class="form-label">Description</label>
              <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $holiday->description) }}</textarea>
              @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
              <label for="status" class="form-label">Status</label>
              <select name="status" id="status" class="form-control">
                <option value="1" {{ old('status', $holiday->status) == '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('status', $holiday->status) == '0' ? 'selected' : '' }}>Inactive</option>
              </select>
            </div>

            <button type="submit" class="btn btn-success">Update Holiday</button>
          </form>
        </div>
      </div>
    </div>
  </section>
@endsection