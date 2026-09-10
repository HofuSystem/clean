<?php

namespace Core\MediaCenter\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;

class AddMediaCenterRequest extends FormRequest
{
    private const IMAGES = 'jpg,jpeg,png,webp,gif';
    private const TYPES = ['media', 'file', 'gallery', 'image', 'avatar', 'thumbnail', 'pdf', 'excel', 'word'];

    public function authorize()
    {
        return true; // Authentication and staff permissions are enforced by the route.
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('type')) {
            $this->merge(['type' => 'media']);
        }
    }

    public function rules()
    {
        $extensions = match ($this->input('type')) {
            'pdf' => 'pdf',
            'excel' => 'xls,xlsx',
            'word' => 'doc,docx',
            'media', 'file' => self::IMAGES.',pdf,doc,docx,xls,xlsx,ppt,pptx,mp4,mp3,wav',
            default => self::IMAGES,
        };

        return [
            'type' => ['required', 'string', Rule::in(self::TYPES)],
            'files' => ['required', 'array', 'min:1', 'max:10'],
            'files.*' => [
                'bail', 'required', 'file', 'max:5120',
                'extensions:'.$extensions, 'mimes:'.$extensions,
                function ($attribute, $value, $fail) {
                    if (! $value instanceof UploadedFile || ! str_starts_with($value->getMimeType() ?? '', 'image/')) {
                        return;
                    }
                    $size = @getimagesize($value->getPathname());
                    if (! $size || $size[0] > 6000 || $size[1] > 6000 || $size[0] * $size[1] > 16000000) {
                        $fail(__('The image must be valid, at most 6000 pixels per side and 16 megapixels in total.'));
                    }
                },
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $files = $this->file('files', []);
            if (! is_array($files)) {
                return;
            }
            $bytes = 0;
            foreach ($files as $file) {
                if ($file instanceof UploadedFile && $file->isValid()) {
                    $bytes += $file->getSize();
                }
            }
            if ($bytes > 20 * 1024 * 1024) {
                $validator->errors()->add('files', __('The total upload size must not exceed 20 MB.'));
            }
        });
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => $validator->errors()->first(),
            'data' => $validator->errors(),
        ], 422));
    }
}
