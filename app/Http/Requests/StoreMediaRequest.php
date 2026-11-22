<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PATCH') || $this->isMethod('PUT');
        $presence = $isUpdate ? 'sometimes' : 'required';
        $media = $this->route('media');
        $mediaId = is_object($media) ? $media->id : $media;

        return [
            'title' => [
                $presence,
                'string',
                'max:255',
                Rule::unique('media', 'title')->ignore($mediaId),
            ],
            'type' => [$presence, 'string', Rule::in(['Anime', 'Manga'])],
            'genre' => [$presence, 'string', 'max:255'],
            'rating' => [$presence, 'numeric', 'min:0', 'max:10'],
            'release_year' => [$presence, 'integer', 'min:1900', 'max:' . ((int) date('Y') + 5)],
            'total_count' => [$presence, 'integer', 'min:1'],
            'creator' => [$presence, 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul media wajib diisi.',
            'title.unique' => 'Judul media sudah terdaftar dan tidak boleh duplikat.',
            'type.required' => 'Jenis media wajib diisi.',
            'type.in' => 'Jenis media harus berupa Anime atau Manga.',
            'genre.required' => 'Genre media wajib diisi.',
            'rating.required' => 'Rating media wajib diisi.',
            'rating.numeric' => 'Rating media harus berupa angka.',
            'rating.min' => 'Rating minimal adalah 0.',
            'rating.max' => 'Rating maksimal adalah 10.',
            'release_year.required' => 'Tahun rilis wajib diisi.',
            'release_year.integer' => 'Tahun rilis harus berupa angka bulat.',
            'total_count.required' => 'Jumlah episode atau chapter wajib diisi.',
            'total_count.integer' => 'Jumlah episode atau chapter harus berupa angka bulat.',
            'total_count.min' => 'Jumlah episode atau chapter minimal adalah 1.',
            'creator.required' => 'Nama studio atau pengarang wajib diisi.',
        ];
    }
}
