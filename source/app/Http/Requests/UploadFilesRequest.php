<?php

namespace App\Http\Requests;

use App\Enums\RequestTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UploadFilesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->requestType = match($this->requestType) {
            RequestTypeEnum::Aggregation->value => RequestTypeEnum::Aggregation,
            RequestTypeEnum::Merge->value => RequestTypeEnum::Merge,
            default => $this->requestType,
        };
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'files' => 'required',
            'files.*' => 'mimes:xls,xlsx|max:20000',
            'requestType' => [
                'required',
                new Enum(RequestTypeEnum::class),
            ]
        ];
    }
}
