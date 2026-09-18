@extends('layouts.app')

@section('title', 'Accept Invitation')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm mt-5">
            <div class="card-body p-4">
                <h4 class="card-title mb-1 text-center">Complete Your Registration</h4>
                <p class="text-muted text-center small">
                    You are joining <strong>{{ $invitation->company->name }}</strong> as
                    <span class="badge bg-secondary text-uppercase">{{ $invitation->role }}</span>
                </p>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="registerForm" method="POST" action="{{ route('invitations.complete', $invitation->token) }}" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="{{ $invitation->email }}" disabled>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}">
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Create Account</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        $('#registerForm').validate({
            rules: {
                name: { required: true, minlength: 2 },
                password: { required: true, minlength: 8 },
                password_confirmation: { required: true, equalTo: '#password' }
            },
            messages: {
                name: { required: 'Please enter your full name.' },
                password: { required: 'Please enter a password.', minlength: 'Password must be at least 8 characters.' },
                password_confirmation: { required: 'Please confirm your password.', equalTo: 'Passwords do not match.' }
            },
            errorElement: 'div',
            errorClass: 'invalid-feedback d-block',
            highlight: function (element) { $(element).addClass('is-invalid'); },
            unhighlight: function (element) { $(element).removeClass('is-invalid'); }
        });
    });
</script>
@endpush
