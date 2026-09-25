<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShortUrlRequest;
use App\Models\ShortUrl;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ShortUrlController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $query = ShortUrl::query()->with(['user', 'company'])->latest();

        if ($user->isAdmin()) {
            // $query->where('company_id', $user->company_id);
            $query->where(function ($q) use ($user) {

                $q->where(function ($q) use ($user) {
                    // Admin ke apne URLs
                    $q->where('company_id', $user->company_id)
                    ->where('user_id', $user->id);
                })
                ->orWhere(function ($q) use ($user) {
                    // Same company ke members ke URLs
                    $q->where('company_id', $user->company_id)
                    ->whereHas('user', function ($q) {
                        $q->where('role', 'member');
                    });
                });

            });

        } elseif ($user->isMember()) {
            $query->where('user_id', $user->id);
        }
        // superadmin sees every short url, no filter needed

        return view('urls.index', [
            'shortUrls' => $query->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('urls.create');
    }

    public function store(StoreShortUrlRequest $request): RedirectResponse
    {
        $user = $request->user();

        ShortUrl::create([
            'company_id' => $user->company_id,
            'user_id' => $user->id,
            'original_url' => $request->validated('original_url'),
            'short_code' => $this->generateUniqueCode(),
        ]);

        return redirect()->route('urls.index')->with('status', 'Short URL created successfully.');
    }

    public function redirectToOriginal(string $code): RedirectResponse
    {
        $shortUrl = ShortUrl::where('short_code', $code)->firstOrFail();

        $shortUrl->increment('clicks');

        return redirect()->away($shortUrl->original_url);
    }

    private function generateUniqueCode(): string
    {
        do {
            $code = Str::random(6);
        } while (ShortUrl::where('short_code', $code)->exists());

        return $code;
    }
}
