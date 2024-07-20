<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Propaganistas\LaravelPhone\Rules\Phone;
use BADDIServices\ClnkGO\Models\ScheduledPost;

class ScheduledPostRequest extends FormRequest
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
        $rules = [
            ScheduledPost::SUMMARY_COLUMN       => 'required|string|min:1|max:5000',
            ScheduledPost::ACTION_TYPE_COLUMN   => sprintf(
                'required|string|in:%s', implode(',', array_keys(ScheduledPost::ACTION_TYPES))
            ),
            ScheduledPost::ACTION_URL_COLUMN    => 'required|url',
            'scheduled_date'                    => 'nullable|date|after:now',
            'scheduled_time'                    => 'nullable|date_format:H:i',
        ];

        if ($this->input(ScheduledPost::ACTION_TYPE_COLUMN) === ScheduledPost::CALL_ACTION_TYPE) {
            $rules[ScheduledPost::ACTION_URL_COLUMN] = ['required', new Phone()];
        }

        return $rules;
    }
}