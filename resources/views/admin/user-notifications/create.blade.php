@extends('layouts.admin')

@php
    $routePrefix = auth()->user()->role->slug === 'hr-manager'
        ? 'hr.'
        : '';
@endphp

@section('title') {{ __('Create Notification') }} @endsection

@section('header')
  <div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h3">{{ __('Create New Notification') }}</h1>
    <a href="{{ route("{$routePrefix}user-notifications.index") }}" class="btn btn-light border">Back to List</a>
  </div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route("{$routePrefix}user-notifications.store") }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Notification Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Body Content <span class="text-danger">*</span></label>
                        <textarea name="body" rows="4" class="form-control @error('body') is-invalid @enderror" required>{{ old('body') }}</textarea>
                        @error('body') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                                @foreach($categories as $key => $val)
                                    <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type Preset Asset <span class="text-danger">*</span></label>
                            <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                @foreach($types as $key => $val)
                                    <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-4 border-top pt-3">
                        <h5>Recipient Target Logic</h5>
                        
                        <div class="mb-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input target-toggle" type="radio" name="is_broadcast" id="target_all" value="1" {{ old('is_broadcast', '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-input-label" for="target_all">Broadcast to All Users</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input target-toggle" type="radio" name="is_broadcast" id="target_custom" value="0" {{ old('is_broadcast') == '0' ? 'checked' : '' }}>
                                <label class="form-check-input-label" for="target_custom">Target Specific Demographics</label>
                            </div>
                        </div>

                        <div id="custom-targeting-fields" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">Target Roles</label>
                                <select name="target_roles[]" class="form-select" multiple size="4">
                                    @foreach($roles as $slug => $name)
                                        <option value="{{ $slug }}" {{ is_array(old('target_roles')) && in_array($slug, old('target_roles')) ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Hold Ctrl/Cmd to select multiple targets.</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Target Specific Employees</label>
                                <select name="target_employee_ids[]" class="form-select" multiple size="5">
                                    @foreach($employees as $id => $name)
                                        <option value="{{ $id }}" {{ is_array(old('target_employee_ids')) && in_array($id, old('target_employee_ids')) ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4 border-top pt-3">
                        <h5>Actions & Interactive Links (Optional)</h5>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Action URL</label>
                            <input type="text" name="action_url" class="form-control" value="{{ old('action_url') }}" placeholder="https://...">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Action Label Button</label>
                            <input type="text" name="action_label" class="form-control" value="{{ old('action_label') }}" placeholder="e.g. View Document">
                        </div>
                    </div>

                    <div class="row mb-4 border-top pt-3">
                        <div class="col-md-6">
                            <label class="form-label">Execution Scheduling</label>
                            <input type="datetime-local" name="scheduled_at" class="form-control" value="{{ old('scheduled_at') }}">
                            <small class="text-muted">Required only if opting to "Schedule Execution" layout engine strategy below.</small>
                        </div>
                    </div>

                    <div class="d-flex gap-2 justify-content-end border-top pt-3">
                        <button type="submit" name="action" value="draft" class="btn btn-secondary">
                            <i class="fas fa-save"></i> Save Draft
                        </button>
                        <button type="submit" name="action" value="schedule" class="btn btn-warning text-dark">
                            <i class="fas fa-clock"></i> Schedule Delivery
                        </button>
                        <button type="submit" name="action" value="send" class="btn btn-success">
                            <i class="fas fa-paper-plane"></i> Dispatch Now
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
document.addEventListener("DOMContentLoaded", function() {
    const customTargetGroup = document.getElementById('custom-targeting-fields');
    const toggles = document.querySelectorAll('.target-toggle');

    function checkToggleState() {
        const activeRadio = document.querySelector('.target-toggle:checked');
        if(activeRadio && activeRadio.value === "0") {
            customTargetGroup.style.display = 'block';
        } else {
            customTargetGroup.style.display = 'none';
        }
    }

    toggles.forEach(radio => radio.addEventListener('change', checkToggleState));
    checkToggleState(); // Handle validation fallback state
});
</script>
@endsection