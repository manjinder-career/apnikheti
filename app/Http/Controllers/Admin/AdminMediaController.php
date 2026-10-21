<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AdminMediaController extends Controller
{
    public function index()
    {
        $query = MediaItem::query();
        if (in_array(request('type'), ['photo', 'video'], true)) {
            $query->where('media_type', request('type'));
        }
        $mediaItems = $query->latest()->paginate(15)->withQueryString();

        return view('admin.media.index', compact('mediaItems'));
    }

    public function create()
    {
        return view('admin.media.create', ['item' => new MediaItem]);
    }

    public function store(Request $request)
    {
        $validated = $this->validatedMedia($request);
        $images = $validated['images'] ?? [];
        unset($validated['images']);

        if ($images) {
            unset($validated['image']);
            $validated['is_published'] = $request->boolean('is_published');

            foreach ($images as $image) {
                $imageData = $validated;
                $imageData['url_or_path'] = 'storage/'.$image->store('media', 'public');
                MediaItem::create($imageData);
            }

            return redirect()->route('admin.media.index')->with('success', 'Media items added successfully.');
        }

        $validated = $this->prepareMediaData($request, $validated);
        MediaItem::create($validated);

        return redirect()->route('admin.media.index')->with('success', 'Media item added successfully.');
    }

    public function edit(MediaItem $medium)
    {
        return view('admin.media.create', ['item' => $medium]);
    }

    public function update(Request $request, MediaItem $medium)
    {
        $validated = $this->validatedMedia($request);
        $validated = $this->prepareMediaData($request, $validated);
        $oldPath = $medium->url_or_path;
        $medium->update($validated);

        if ($oldPath !== $medium->url_or_path) {
            $this->deleteUploadedPhoto($oldPath);
        }

        return redirect()->route('admin.media.index')->with('success', 'Media item updated successfully.');
    }

    private function validatedMedia(Request $request): array
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'media_type' => 'required|in:video,photo',
            'url_or_path' => 'nullable|string|max:2048',
            'image' => 'nullable|image|max:5120',
            'images' => 'nullable|array',
            'images.*' => 'image|max:5120',
            'youtube_id' => ['nullable', 'regex:/^[a-zA-Z0-9_-]{11}$/'],
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'tags' => 'nullable|string|max:255',
            'is_published' => 'nullable|boolean',
        ]);

        if (
            $validated['media_type'] === 'photo'
            && ! $request->filled('url_or_path')
            && ! $request->hasFile('image')
            && ! $request->hasFile('images')
        ) {
            throw ValidationException::withMessages(['image' => 'Upload a photo or provide its URL.']);
        }

        if ($validated['media_type'] === 'video') {
            if ($request->hasFile('image') || $request->hasFile('images')) {
                throw ValidationException::withMessages(['image' => 'Photo uploads can only be added to the photo gallery.']);
            }

            $youtubeId = ($validated['youtube_id'] ?? null) ?: $this->youtubeId($validated['url_or_path'] ?? '');
            if (! $youtubeId) {
                throw ValidationException::withMessages(['url_or_path' => 'Enter a valid YouTube video URL or video ID.']);
            }
            $validated['youtube_id'] = $youtubeId;
        } else {
            $validated['youtube_id'] = null;
        }

        return $validated;
    }

    private function prepareMediaData(Request $request, array $validated): array
    {
        if ($request->hasFile('image')) {
            $validated['url_or_path'] = 'storage/'.$request->file('image')->store('media', 'public');
        }

        $validated['is_published'] = $request->boolean('is_published');

        return $validated;
    }

    public function destroy(MediaItem $medium)
    {
        $this->deleteUploadedPhoto($medium->url_or_path);
        $medium->delete();

        return redirect()->route('admin.media.index')->with('success', 'Media item deleted.');
    }

    private function deleteUploadedPhoto(string $path): void
    {
        if (str_starts_with($path, 'storage/media/')) {
            Storage::disk('public')->delete(substr($path, strlen('storage/')));
        }
    }

    private function youtubeId(string $url): ?string
    {
        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $url)) {
            return $url;
        }

        $host = parse_url($url, PHP_URL_HOST);
        if (! $host) {
            return null;
        }

        $host = strtolower($host);
        if (in_array($host, ['youtu.be', 'www.youtu.be'], true)) {
            $id = trim((string) parse_url($url, PHP_URL_PATH), '/');
        } elseif (in_array($host, ['youtube.com', 'www.youtube.com', 'm.youtube.com'], true)) {
            parse_str((string) parse_url($url, PHP_URL_QUERY), $query);
            $path = trim((string) parse_url($url, PHP_URL_PATH), '/');
            if (isset($query['v'])) {
                $id = $query['v'];
            } elseif (preg_match('/^(?:embed|shorts|live)\/([^\/]+)$/', $path, $matches)) {
                $id = $matches[1];
            } else {
                $id = $path;
            }
        } else {
            return null;
        }

        return preg_match('/^[a-zA-Z0-9_-]{11}$/', $id) ? $id : null;
    }
}
