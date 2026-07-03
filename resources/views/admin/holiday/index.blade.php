@extends('layouts.admin')

@php
    $routePrefix = auth()->user()->role->slug === 'hr-manager'
        ? 'hr.'
        : '';
@endphp

@section('title')
    {{ __('Manage Holidays') }}
@endsection

@section('header')
  <div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-3">Manage Holidays</h1>
    <a href="{{ route($routePrefix . 'holiday.create') }}" class="btn btn-primary">
      <i class="fas fa-plus"></i>
      <span class="ps-1">{{ __('Add New') }}</span>
    </a>
  </div>
@endsection

@section('content')
  <section class="row">
    <div class="col-12">
      <div class="card flex-fill">
        <div class="card-header">
          <h5 class="card-title mb-0">Holiday DataTable</h5>
        </div>
        <table class="table data-table">
          <thead>
            <tr>
              <th>SL</th>
              <th>Holiday Name</th>
              <th>Start Date</th>
              <th>End Date</th>
              <th>Duration</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($holidays as $holiday)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                  <strong>{{ $holiday->name }}</strong>
                  @if($holiday->description)
                    <br><small class="text-muted">{{ $holiday->description }}</small>
                  @endif
                </td>
                <td>{{ $holiday->start_date->format('Y-m-d') }}</td>
                <td>{{ $holiday->end_date->format('Y-m-d') }}</td>
                <td>{{ $holiday->no_of_days }} Days</td>
                <td>
                  @if($holiday->status)
                    <span class="badge bg-success">Active</span>
                  @else
                    <span class="badge bg-danger">Inactive</span>
                  @endif
                </td>
                <td class="d-flex align-items-center">
                  <a href="{{ route($routePrefix . 'holiday.edit', $holiday->id) }}" class="btn btn-info btn-sm">
                    <i class="fas fa-edit"></i>
                  </a>
                
                  <form action="{{ route($routePrefix . 'holiday.destroy', $holiday->id) }}" method="post">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                      <i class="fas fa-trash-alt"></i>
                    </button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </section>
@endsection

@section('script')
@endsection