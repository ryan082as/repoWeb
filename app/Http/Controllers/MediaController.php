<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMediaRequest;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MediaController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $media = Media::with('users')
            ->search($request->query('search'))
            ->byType($request->query('type'))
            ->paginate(10);

        return MediaResource::collection($media);
    }

    public function store(StoreMediaRequest $request): JsonResponse
    {
        $media = Media::create($request->validated());

        return (new MediaResource($media))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Media $media): MediaResource
    {
        return new MediaResource($media->load('users'));
    }

    public function update(StoreMediaRequest $request, Media $media): MediaResource
    {
        $media->update($request->validated());

        return new MediaResource($media->load('users'));
    }

    public function destroy(Media $media): JsonResponse
    {
        $media->delete();

        return response()->json([
            'message' => 'Media berhasil dihapus',
        ]);
    }
}
