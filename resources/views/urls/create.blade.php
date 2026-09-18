@extends('layouts.app')

@section('title', 'Create Short URL')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h4 class="card-title mb-3">Create Short URL</h4>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="urlForm" method="POST" action="{{ route('urls.store') }}" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="original_url" class="form-label">Original URL</label>
                        <input type="text" name="original_url" id="original_url" class="form-control" placeholder="https://example.com/some/long/link" value="{{ old('original_url') }}">
                    </div>

                    <button type="submit" class="btn btn-primary">Generate Short URL</button>
                    <a href="{{ route('urls.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        $('#urlForm').validate({
            rules: {
                original_url: { required: true, url: true }
            },
            messages: {
                original_url: {
                    required: 'Please enter a URL to shorten.',
                    url: 'Please enter a valid URL, including http:// or https://.'
                }
            },
            errorElement: 'div',
            errorClass: 'invalid-feedback d-block',
            highlight: function (element) { $(element).addClass('is-invalid'); },
            unhighlight: function (element) { $(element).removeClass('is-invalid'); }
        });
    });
</script>
@endpush
