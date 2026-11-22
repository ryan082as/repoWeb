<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\MediaResource;
use App\Http\Resources\UserResource;
use App\Models\Media;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $users = User::with('media')->paginate(10);

        return UserResource::collection($users);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password'] ?? 'password123');

        $user = User::create($data);

        return (new UserResource($user))
            ->response()
            ->setStatusCode(201);
    }

    public function show(User $user): UserResource
    {
        return new UserResource($user->load('media'));
    }

    public function update(StoreUserRequest $request, User $user): UserResource
    {
        $data = $request->validated();
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return new UserResource($user->load('media'));
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json([
            'message' => 'Pengguna berhasil dihapus',
        ]);
    }

    public function getCollection(User $user): AnonymousResourceCollection
    {
        return MediaResource::collection($user->media()->paginate(10));
    }

    public function addToCollection(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate(
            [
                'media_id' => ['required', 'integer', 'exists:media,id'],
            ],
            [
                'media_id.required' => 'ID media wajib diisi.',
                'media_id.integer' => 'ID media harus berupa angka bulat.',
                'media_id.exists' => 'Media tidak ditemukan di katalog.',
            ]
        );

        if ($user->media()->wherePivot('media_id', $validated['media_id'])->exists()) {
            return response()->json([
                'message' => 'Media sudah ada di koleksi pengguna',
            ], 409);
        }

        $user->media()->attach($validated['media_id']);

        return response()->json([
            'message' => 'Media berhasil ditambahkan ke koleksi pengguna',
            'data' => new UserResource($user->load('media')),
        ], 201);
    }

    public function removeFromCollection(User $user, Media $media): JsonResponse
    {
        if (! $user->media()->wherePivot('media_id', $media->id)->exists()) {
            return response()->json([
                'message' => 'Media tidak ditemukan di koleksi pengguna',
            ], 404);
        }

        $user->media()->detach($media->id);

        return response()->json([
            'message' => 'Media berhasil dihapus dari koleksi pengguna',
            'data' => new UserResource($user->load('media')),
        ]);
    }
}
