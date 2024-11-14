<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use App\Services\XmlService;

class UserCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'firstname' => 'required|string|max:100',
            'lastname' => 'required|string|max:100',
            'username' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'gender' => 'required|in:male,female,other',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            list($xml) = XmlService::prepareData();

            foreach ($xml->Users->User as $user) {
                if ((string) $user->Username === $this->username) {
                    $validator->errors()->add('username', 'The username has already been taken.');
                }

                if ((string) $user->Email === $this->email) {
                    $validator->errors()->add('email', 'The email has already been taken.');
                }
            }
        });
    }

}
