<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadPdvLogoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'logo' => ['required', 'file', 'max:10240', 'image:allow_svg'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'logo.required' => 'Escolha um arquivo de imagem.',
            'logo.max' => 'A imagem não pode passar de 10 MB.',
            'logo.image' => 'O arquivo precisa ser uma imagem (PNG, JPG, GIF, WebP, SVG, etc.).',
        ];
    }
}
