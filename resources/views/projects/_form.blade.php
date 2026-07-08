@php
    $project = $project ?? null;
    $selectedMembers = $project?->members->pluck('id')->toArray() ?? [];
@endphp

<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label fw-semibold">{{ __('Project Title') }}</label>
        <input type="text" name="title" value="{{ old('title', $project->title ?? '') }}" class="form-control"
            required>
        @error('title')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">{{ __('Client Name') }}</label>
        <input type="text" name="client_name" value="{{ old('client_name', $project->client_name ?? '') }}"
            class="form-control">
    </div>

    <div class="col-12">
        <label class="form-label fw-semibold">{{ __('Description') }}</label>
        <textarea name="description" rows="3" class="form-control">{{ old('description', $project->description ?? '') }}</textarea>
    </div>

    <div class="col-md-3">
        <label class="form-label fw-semibold">{{ __('Department') }}</label>
        <select name="department_id" class="form-select">
            <option value="">{{ __('-- Select --') }}</option>
            @foreach ($departments as $dept)
                <option value="{{ $dept->id }}" @selected(old('department_id', $project->department_id ?? '') == $dept->id)>{{ $dept->title }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">{{ __('Status') }}</label>
        <select name="status" class="form-select" required>
            @foreach (['planning', 'active', 'on_hold', 'completed', 'cancelled'] as $s)
                <option value="{{ $s }}" @selected(old('status', $project->status ?? 'planning') == $s)>
                    {{ ucfirst(str_replace('_', ' ', $s)) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">{{ __('Priority') }}</label>
        <select name="priority" class="form-select" required>
            @foreach (['low', 'medium', 'high', 'urgent'] as $p)
                <option value="{{ $p }}" @selected(old('priority', $project->priority ?? 'medium') == $p)>{{ ucfirst($p) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">{{ __('Budget') }}</label>
        <input type="number" step="0.01" name="budget" value="{{ old('budget', $project->budget ?? '') }}"
            class="form-control">
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">{{ __('Start Date') }}</label>
        <input type="date" name="start_date"
            value="{{ old('start_date', $project?->start_date?->format('Y-m-d') ?? '') }}" class="form-control"
            required>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">{{ __('End Date') }}</label>
        <input type="date" name="end_date"
            value="{{ old('end_date', $project?->end_date?->format('Y-m-d') ?? '') }}" class="form-control">
        class="form-control">
    </div>

    <div class="col-12">
        <label class="form-label fw-semibold">{{ __('Team Members') }}</label>
        <select name="members[]" multiple class="form-select" size="6">
            @foreach ($employees as $emp)
                <option value="{{ $emp->id }}" @selected(in_array($emp->id, old('members', $selectedMembers)))>{{ $emp->name }}</option>
            @endforeach
        </select>
        <div class="form-text">{{ __('Hold Ctrl / Cmd to select multiple collaborators for this project.') }}</div>
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-4">
    <a href="{{ route('projects.index') }}" class="btn btn-light rounded-pill px-4">{{ __('Cancel') }}</a>
    <button type="submit"
        class="btn btn-primary rounded-pill px-4">{{ $project ? __('Update Project') : __('Create Project') }}</button>
</div>
