@extends('layouts.app')

@section('title', 'Invite User')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h4 class="card-title mb-3">Invite a New User</h4>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="inviteForm" method="POST" action="{{ route('invitations.store') }}" novalidate>
                    @csrf

                    @if(auth()->user()->isSuperAdmin())
                        <div class="mb-3">
                            <label for="company_name" class="form-label">New Company Name</label>
                            <input type="text" name="company_name" id="company_name" class="form-control" value="{{ old('company_name') }}">
                        </div>
                        <input type="hidden" name="role" value="admin">
                        <p class="text-muted small">SuperAdmin can only invite an <strong>Admin</strong> to a brand new company.</p>
                    @else
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select name="role" id="role" class="form-select">
                                <option value="">Select role</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="member" {{ old('role') == 'member' ? 'selected' : '' }}>Member</option>
                            </select>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}">
                    </div>

                    <button type="submit" class="btn btn-primary">Send Invitation</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        $('#inviteForm').validate({
            rules: {
                email: { required: true, email: true },
                company_name: { required: true, minlength: 2 },
                role: { required: true }
            },
            messages: {
                email: { required: 'Please enter an email address.', email: 'Please enter a valid email address.' },
                company_name: { required: 'Please enter the company name.' },
                role: { required: 'Please select a role.' }
            },
            errorElement: 'div',
            errorClass: 'invalid-feedback d-block',
            highlight: function (element) { $(element).addClass('is-invalid'); },
            unhighlight: function (element) { $(element).removeClass('is-invalid'); }
        });
    });
</script>
@endpush
