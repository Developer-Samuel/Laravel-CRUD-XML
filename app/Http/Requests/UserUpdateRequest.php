<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use App\Services\UserService;
use App\Services\XmlService;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = UserService::find($this->route('id'));

        if (!$user) {
            return ['id' => 'User not found'];
        }

        list($xml) = XmlService::prepareData();

        return [
            'firstname' => 'required|string|max:100',
            'lastname' => 'required|string|max:100',
            'username' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'gender' => 'required|in:male,female,other',
        ];
    }
}
