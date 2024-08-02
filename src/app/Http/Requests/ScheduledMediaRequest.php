<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022,BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScheduledMediaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'file'           => 'required|array|min:1',
            'file.*'         => 'required|file|mimetypes:image/jpeg,image/png,image/gif,image/bmp,image/tiff,image/webp,video/mp4,video/quicktime,video/x-msvideo,video/mpeg,video/x-ms-wmv|max:75000',
            'scheduled_date' => 'nullable|date|after_or_equal:now',
            'scheduled_time' => 'nullable|date_format:H:i',
        ];
    }
}