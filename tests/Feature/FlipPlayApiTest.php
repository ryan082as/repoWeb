<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FlipPlayApiTest extends TestCase
{
    use DatabaseTransactions;

    public function test_can_list_and_search_media(): void
    {
        $response = $this->getJson('/api/media');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'type', 'genre', 'rating', 'release_year', 'total_count', 'creator', 'users'],
                ],
                'meta',
            ]);

        $searchResponse = $this->getJson('/api/media?search=Grand');
        $searchResponse->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Grand Blue Dreaming');

        $typeResponse = $this->getJson('/api/media?type=Manga');
        $typeResponse->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_can_create_show_update_and_delete_media(): void
    {
        $payload = [
            'title' => 'Solo Leveling',
            'type' => 'Anime',
            'genre' => 'Action / Fantasy',
            'rating' => 8.7,
            'release_year' => 2024,
            'total_count' => 12,
            'creator' => 'A-1 Pictures',
        ];

        $createResponse = $this->postJson('/api/media', $payload);
        $createResponse->assertStatus(201)
            ->assertJsonPath('data.title', 'Solo Leveling')
            ->assertJsonPath('data.type', 'Anime');

        $mediaId = $createResponse->json('data.id');

        $showResponse = $this->getJson("/api/media/{$mediaId}");
        $showResponse->assertStatus(200)
            ->assertJsonPath('data.id', $mediaId)
            ->assertJsonStructure(['data' => ['id', 'title', 'users']]);

        $updateResponse = $this->patchJson("/api/media/{$mediaId}", [
            'title' => 'Solo Leveling',
            'rating' => 9.1,
        ]);
        $updateResponse->assertStatus(200)
            ->assertJsonPath('data.rating', 9.1);

        $deleteResponse = $this->deleteJson("/api/media/{$mediaId}");
        $deleteResponse->assertStatus(200)
            ->assertJson(['message' => 'Media berhasil dihapus']);

        $this->getJson("/api/media/{$mediaId}")->assertStatus(404);
    }

    public function test_validation_errors_return_422(): void
    {
        $emptyResponse = $this->postJson('/api/media', [
            'title' => '',
            'type' => 'Novel',
            'rating' => 15,
        ]);
        $emptyResponse->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'type', 'genre', 'rating', 'release_year', 'total_count', 'creator']);

        $duplicateResponse = $this->postJson('/api/media', [
            'title' => 'Grand Blue Dreaming',
            'type' => 'Anime',
            'genre' => 'Comedy',
            'rating' => 8.0,
            'release_year' => 2018,
            'total_count' => 12,
            'creator' => 'Zero-G',
        ]);
        $duplicateResponse->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    public function test_not_found_returns_404(): void
    {
        $this->getJson('/api/media/99999')->assertStatus(404);
        $this->getJson('/api/users/99999')->assertStatus(404);
    }

    public function test_user_collection_attach_duplicate_409_and_detach(): void
    {
        $user = User::where('email', 'tugas@flipplay.test')->firstOrFail();
        $frieren = Media::where('title', 'Sousou no Frieren')->firstOrFail();

        $this->getJson("/api/users/{$user->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.name', 'Tugas')
            ->assertJsonStructure(['data' => ['id', 'name', 'email', 'media']]);

        $this->getJson("/api/users/{$user->id}/collection")
            ->assertStatus(200)
            ->assertJsonCount(2, 'data');

        $attachResponse = $this->postJson("/api/users/{$user->id}/collection", [
            'media_id' => $frieren->id,
        ]);
        $attachResponse->assertStatus(201)
            ->assertJson(['message' => 'Media berhasil ditambahkan ke koleksi pengguna']);

        $duplicateAttach = $this->postJson("/api/users/{$user->id}/collection", [
            'media_id' => $frieren->id,
        ]);
        $duplicateAttach->assertStatus(409)
            ->assertJson(['message' => 'Media sudah ada di koleksi pengguna']);

        $detachResponse = $this->deleteJson("/api/users/{$user->id}/collection/{$frieren->id}");
        $detachResponse->assertStatus(200)
            ->assertJson(['message' => 'Media berhasil dihapus dari koleksi pengguna']);

        $detachAgain = $this->deleteJson("/api/users/{$user->id}/collection/{$frieren->id}");
        $detachAgain->assertStatus(404)
            ->assertJson(['message' => 'Media tidak ditemukan di koleksi pengguna']);
    }
}
