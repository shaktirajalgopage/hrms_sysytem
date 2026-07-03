@extends('layouts.admin')

@php
    $routePrefix = auth()->user()->role->slug === 'hr-manager' ? 'hr.' : '';
@endphp

@section('title')
    {{ __('Job Positions') }}
@endsection

@section('header')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3">{{ __('Job Positions') }}</h1>
        <a href="{{ route($routePrefix . 'positions.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            <span class="ps-1">{{ __('Create Position') }}</span>
        </a>
    </div>
@endsection

@section('content')
    <section class="row">
        <div class="col-12">
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-hover my-0 align-middle">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Department</th>
                                <th>Location</th>
                                <th>Type</th>
                                <th>Openings</th>
                                <th>Applications</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($positions as $pos)
                                <tr>
                                    <td><strong>{{ $pos->title }}</strong></td>
                                    <td>{{ $pos->department }}</td>
                                    <td>{{ $pos->location ?? '—' }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            {{ $types[$pos->type] ?? $pos->type }}
                                        </span>
                                    </td>
                                    <td>{{ $pos->openings }}</td>
                                    <td>
                                        <a href="{{ route($routePrefix . 'positions.index', ['position' => $pos->id]) }}"
                                            class="badge bg-primary text-decoration-none">
                                            {{ $pos->applications_count }}
                                        </a>
                                    </td>
                                    <td>
                                        @if ($pos->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="{{ route($routePrefix . 'positions.edit', $pos) }}"
                                                class="btn btn-sm btn-light border text-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route($routePrefix . 'positions.destroy', $pos) }}"
                                                method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-light border text-danger"
                                                    onclick="return confirm('Delete this position?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="fas fa-briefcase fa-2x d-block mb-2"></i>
                                        No positions found. Create one to get started.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($positions->hasPages())
                    <div class="card-footer bg-white border-top">
                        {{ $positions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
