<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'URL Shortener')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

@auth
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('urls.index') }}"><i class="bi bi-link-45deg"></i> URL Shortener</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('urls.index') }}">My URLs</a>
                </li>
                @if(in_array(auth()->user()->role, ['admin', 'member']))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('urls.create') }}">Create Short URL</a>
                    </li>
                @endif
                @if(in_array(auth()->user()->role, ['superadmin', 'admin']))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('invitations.create') }}">Invite User</a>
                    </li>
                @endif
            </ul>
            <span class="navbar-text text-light me-3">
                {{ auth()->user()->name }}
                <span class="badge bg-secondary text-uppercase">{{ auth()->user()->role }}</span>
            </span>
            <form method="POST" action="{{ route('logout') }}" class="d-flex">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
            </form>
        </div>
    </div>
</nav>
@endauth

<div class="container">
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if (session('invite_link'))
        <div class="alert alert-info">
            Share this invite link with the invited user:<br>
            <code>{{ session('invite_link') }}</code>
        </div>
    @endif

    @yield('content')
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.20.1/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
