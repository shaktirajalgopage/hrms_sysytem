@extends('layouts.admin')

@php
    $routePrefix = auth()->user()->role->slug === 'hr-manager'
        ? 'hr.'
        : '';
@endphp

@section('title')
    {{ __('Add Candidate') }}
@endsection

@section('header')
  <div class="d-flex align-items-center justify-content-between mb-4">
    <div>
      <a href="{{ route("{$routePrefix}positions.index") }}" class="text-muted text-decoration-none small">
        <i class="fas fa-arrow-left me-1"></i> Back to Pipeline
      </a>
      <h1 class="h3 mt-1 mb-0">{{ __('Add Candidate') }}</h1>
    </div>
  </div>
@endsection

@section('content')
<form action="{{ route("{$routePrefix}applications.store") }}" method="POST" enctype="multipart/form-data">
@csrf

<div class="row g-4">

  {{-- Left: Candidate Info --}}
  <div class="col-lg-7">

    <div class="card mb-4">
      <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="fas fa-user me-2 text-primary"></i>Candidate Information</h5>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Full Name <span class="text-danger">*</span></label>
            <input type="text" name="candidate_name" class="form-control @error('candidate_name') is-invalid @enderror"
                   value="{{ old('candidate_name') }}" placeholder="e.g. Priya Sharma" required>
            @error('candidate_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Email Address <span class="text-danger">*</span></label>
            <input type="email" name="candidate_email" class="form-control @error('candidate_email') is-invalid @enderror"
                   value="{{ old('candidate_email') }}" placeholder="priya@example.com" required>
            @error('candidate_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Phone Number</label>
            <input type="text" name="candidate_phone" class="form-control"
                   value="{{ old('candidate_phone') }}" placeholder="+91 98765 43210">
          </div>
          <div class="col-md-6">
            <label class="form-label">LinkedIn Profile</label>
            <input type="url" name="linkedin_url" class="form-control"
                   value="{{ old('linkedin_url') }}" placeholder="https://linkedin.com/in/...">
          </div>
          <div class="col-md-6">
            <label class="form-label">Portfolio / Website</label>
            <input type="url" name="portfolio_url" class="form-control"
                   value="{{ old('portfolio_url') }}" placeholder="https://...">
          </div>
          <div class="col-md-6">
            <label class="form-label">Available From</label>
            <input type="date" name="available_from" class="form-control"
                   value="{{ old('available_from') }}">
          </div>
          <div class="col-12">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="3"
                      placeholder="Any additional notes about the candidate...">{{ old('notes') }}</textarea>
          </div>
        </div>
      </div>
    </div>

    {{-- CV Upload --}}
    <div class="card mb-4">
      <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="fas fa-file-upload me-2 text-info"></i>CV / Resume</h5>
      </div>
      <div class="card-body">
        <div class="border border-dashed rounded-3 p-4 text-center" id="cv-drop-zone"
             style="border-color:#0d6efd !important; cursor:pointer" onclick="document.getElementById('cv-file').click()">
          <i class="fas fa-cloud-upload-alt fa-2x text-primary mb-2"></i>
          <div class="fw-semibold text-primary">Click to upload or drag & drop</div>
          <div class="text-muted small mt-1">PDF, DOC, DOCX — max 5MB</div>
          <div id="cv-filename" class="mt-2 small text-success fw-semibold d-none"></div>
        </div>
        <input type="file" id="cv-file" name="cv" class="d-none" accept=".pdf,.doc,.docx"
               onchange="showCvName(this)">
        @error('cv')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
      </div>
    </div>

  </div>

  {{-- Right: Role & Assignment --}}
  <div class="col-lg-5">

    <div class="card mb-4">
      <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="fas fa-briefcase me-2 text-warning"></i>Job Details</h5>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label">Position <span class="text-danger">*</span></label>
          <select name="job_position_id" class="form-select @error('job_position_id') is-invalid @enderror" required>
            <option value="">-- Select Position --</option>
            @foreach($positions as $pos)
              <option value="{{ $pos->id }}" {{ old('job_position_id') == $pos->id ? 'selected' : '' }}>
                {{ $pos->title }} — {{ $pos->department }}
              </option>
            @endforeach
          </select>
          @error('job_position_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
          <label class="form-label">Source</label>
          <select name="source" class="form-select">
            <option value="">-- How did they apply? --</option>
            @foreach($sources as $key => $label)
              <option value="{{ $key }}" {{ old('source') === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Expected Salary (₹)</label>
          <input type="number" name="expected_salary" class="form-control"
                 value="{{ old('expected_salary') }}" placeholder="e.g. 800000">
        </div>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="fas fa-user-tie me-2 text-success"></i>Assignment</h5>
      </div>
      <div class="card-body">
        <div class="mb-0">
          <label class="form-label">Assign To</label>
          <select name="assigned_to" class="form-select">
            <option value="">-- Unassigned --</option>
            @foreach($users as $id => $name)
              <option value="{{ $id }}" {{ old('assigned_to') == $id ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>

    <div class="d-grid gap-2">
      <button type="submit" class="btn btn-primary btn-lg">
        <i class="fas fa-check me-2"></i>Submit Application
      </button>
      <a href="{{ route("{$routePrefix}positions.index") }}" class="btn btn-light border">Cancel</a>
    </div>

  </div>
</div>

</form>

@push('scripts')
<script>
function showCvName(input) {
    const el = document.getElementById('cv-filename');
    if (input.files && input.files[0]) {
        el.textContent = '📎 ' + input.files[0].name;
        el.classList.remove('d-none');
    }
}

const dropZone = document.getElementById('cv-drop-zone');
const fileInput = document.getElementById('cv-file');

['dragenter','dragover'].forEach(e => {
    dropZone.addEventListener(e, ev => {
        ev.preventDefault();
        dropZone.style.background = '#e8f4fd';
    });
});
['dragleave','drop'].forEach(e => {
    dropZone.addEventListener(e, ev => {
        dropZone.style.background = '';
    });
});
dropZone.addEventListener('drop', function(ev) {
    ev.preventDefault();
    fileInput.files = ev.dataTransfer.files;
    showCvName(fileInput);
});
</script>
@endpush

@endsection
