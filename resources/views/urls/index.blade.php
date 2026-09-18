@extends('layouts.app')

@section('title', 'My Short URLs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">
        @if(auth()->user()->isSuperAdmin())
            All Short URLs (Every Company)
        @elseif(auth()->user()->isAdmin())
            Short URLs &mdash; {{ auth()->user()->company->name }}
        @else
            My Short URLs
        @endif
    </h4>

    @if(in_array(auth()->user()->role, ['admin', 'member']))
        <a href="{{ route('urls.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Create Short URL
        </a>
    @endif
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Short URL</th>
                    <th>Original URL</th>
                    <th>Created By</th>
                    @if(auth()->user()->isSuperAdmin())
                        <th>Company</th>
                    @endif
                    <th>Clicks</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                @forelse($shortUrls as $shortUrl)
                    <tr>
                        <td><a href="{{ $shortUrl->shortUrl() }}" target="_blank">{{ $shortUrl->shortUrl() }}</a></td>
                        <td class="text-truncate" style="max-width: 300px;">{{ $shortUrl->original_url }}</td>
                        <td>{{ $shortUrl->user->name }}</td>
                        @if(auth()->user()->isSuperAdmin())
                            <td>{{ $shortUrl->company->name }}</td>
                        @endif
                        <td>{{ $shortUrl->clicks }}</td>
                        <td>{{ $shortUrl->created_at->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No short URLs found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $shortUrls->links() }}
</div>
@endsection
