@extends('layouts.admin')

@php
    $routePrefix = auth()->user()->role->slug === 'hr-manager'
        ? 'hr.'
        : '';
@endphp

@section('title')
    {{ __('Add New Holiday') }}
@endsection

@section('header')
  <div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-3">Add New Holiday</h1>
    <a href="{{ route($routePrefix . 'holiday.index') }}" class="btn btn-secondary shadow-sm">
      <i class="fas fa-arrow-left"></i> Back to List
    </a>
  </div>
@endsection

@section('content')
  <section class="row">
    <div class="col-12 col-md-8 offset-md-2">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom py-3">
          <h5 class="card-title mb-0 text-primary font-weight-bold">Holiday Details</h5>
        </div>
        <div class="card-body p-4">
          <form action="{{ route($routePrefix . 'holiday.store') }}" method="POST">
            @csrf

            <div class="mb-4">
              <label for="name" class="form-label font-weight-bold">Holiday Name <span class="text-danger">*</span></label>
              <input type="text" name="name" id="name" class="form-control form-control-lg @error('name') is-invalid @enderror" placeholder="e.g., New Year's Day" value="{{ old('name') }}" required>
              @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="row">
              <div class="col-md-8 mb-4">
                <label for="date_range" class="form-label font-weight-bold">Select Date Range <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-text bg-light"><i class="fas fa-calendar-alt text-muted"></i></span>
                  <input type="text" id="date_range" class="form-control form-control-lg bg-white @error('start_date') is-invalid @enderror @error('end_date') is-invalid @enderror" placeholder="Choose start and end dates..." readonly required>
                </div>
                @error('start_date') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                @error('end_date') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-4 mb-4">
                <label class="form-label font-weight-bold text-muted">Calculated Duration</label>
                <div class="d-flex align-items-center justify-content-center border rounded bg-light" style="height: calc(3.5rem); min-width: 100px;">
                  <span id="duration_badge" class="h4 mb-0 text-dark font-weight-bold">0 Days</span>
                </div>
              </div>
            </div>

            <input type="hidden" name="start_date" id="start_date" value="{{ old('start_date') }}">
            <input type="hidden" name="end_date" id="end_date" value="{{ old('end_date') }}">

            <div class="mb-4">
              <label for="description" class="form-label font-weight-bold">Description</label>
              <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror" placeholder="Optional notes regarding this holiday...">{{ old('description') }}</textarea>
              @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-4">
              <label for="status" class="form-label font-weight-bold">Status</label>
              <select name="status" id="status" class="form-control form-select form-control-lg">
                <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
              </select>
            </div>

            <div class="pt-2">
              <button type="submit" class="btn btn-primary btn-lg px-4">Save Holiday</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
@endsection

@section('script')
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const rangeInput = document.getElementById('date_range');
      const startHidden = document.getElementById('start_date');
      const endHidden = document.getElementById('end_date');
      const durationBadge = document.getElementById('duration_badge');

      // Initialize Flatpickr in range mode
      const fp = flatpickr(rangeInput, {
        mode: "range",
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "F j, Y",
        allowInput: false,
        onClose: function(selectedDates) {
          if (selectedDates.length === 2) {
            const start = selectedDates[0];
            const end = selectedDates[1];
            
            // Format to Y-m-d strings for hidden inputs
            const pad = num => String(num).padStart(2, '0');
            const formatDateString = (d) => `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;

            startHidden.value = formatDateString(start);
            endHidden.value = formatDateString(end);

            // Calculate inclusive difference in days
            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            
            durationBadge.innerText = `${diffDays} Day${diffDays > 1 ? 's' : ''}`;
            durationBadge.parentElement.classList.replace('bg-light', 'bg-success-light');
            durationBadge.classList.add('text-success');
          } else {
            // Reset if an incomplete range is selected
            startHidden.value = "";
            endHidden.value = "";
            durationBadge.innerText = "0 Days";
            durationBadge.parentElement.className = "d-flex align-items-center justify-content-center border rounded bg-light";
            durationBadge.className = "h4 mb-0 text-dark font-weight-bold";
          }
        }
      });

      // Handle old values if validation failed
      if (startHidden.value && endHidden.value) {
        fp.setDate([startHidden.value, endHidden.value], true);
      }
    });
  </script>
  
  <style>
    /* Styling extension for premium UI look */
    .bg-success-light {
      background-color: rgba(40, 167, 69, 0.1) !important;
      border-color: rgba(40, 167, 69, 0.2) !important;
    }
  </style>
@endsection