<?php

namespace App\Http\Controllers;

use App\Models\MediaItem;

class MediaController extends Controller
{
    public function index()
    {
        $mediaItems = MediaItem::published()
            ->where('media_type', 'video')
            ->latest()
            ->paginate(9);

        return view('media.index', compact('mediaItems'));
    }

    public function gallery()
    {
        $mediaItems = MediaItem::published()
            ->where('media_type', 'photo')
            ->latest()
            ->paginate(12);

        return view('gallery.index', compact('mediaItems'));
    }

    public function videos()
    {
        $mediaItems = MediaItem::published()
            ->where('media_type', 'video')
            ->latest()
            ->paginate(9);

        return view('media.index', compact('mediaItems'));
    }
}
