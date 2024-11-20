<?php

namespace App\Services;

use Illuminate\Http\Request;

use App\Services\XmlService;
use App\Models\User;

use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    public static function list($perPage = 10)
    {
        list($xml) = XmlService::prepareData();

        $users = [];
        foreach ($xml->Users->User as $user) {
            $users[] = [
                'ID' => (int) $user->ID,
                'Firstname' => (string) $user->Firstname,
                'Lastname' => (string) $user->Lastname,
                'Username' => (string) $user->Username,
                'Email' => (string) $user->Email,
                'Gender' => (string) $user->Gender,
            ];
        }

        $usersCollection = collect($users);

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $usersCollection->slice(($currentPage - 1) * $perPage, $perPage)->all();

        $paginator = new LengthAwarePaginator(
            $currentItems,
            count($users),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        return $paginator;
    }

    public static function find($id)
    {
        list($xml) = XmlService::prepareData();

        foreach ($xml->Users->User as $user) {
            if ((int) $user->ID === (int) $id) {
                return new User(
                    (int) $user->ID,
                    (string) $user->Firstname,
                    (string) $user->Lastname,
                    (string) $user->Username,
                    (string) $user->Email,
                    (string) $user->Gender
                );
            }
        }

        return null;
    }

    public static function create(Request $request)
    {
        list($xml, $filePath) = XmlService::prepareData();

        $user = $xml->Users->addChild('User');
        $user->addChild('ID', self::generateNewId($xml));
        $user->addChild('Firstname', $request->input('firstname'));
        $user->addChild('Lastname', $request->input('lastname'));
        $user->addChild('Username', $request->input('username'));
        $user->addChild('Email', $request->input('email'));
        $user->addChild('Gender', ucfirst($request->input('gender')));

        $xml->asXML($filePath);
    }

    public static function update(Request $request, $id)
    {
        list($xml, $filePath) = XmlService::prepareData();

        foreach ($xml->Users->User as $user) {
            if ((int) $user->ID === (int) $id) {
                $user->Firstname = $request->input('firstname');
                $user->Lastname = $request->input('lastname');
                $user->Gender = ucfirst($request->input('gender'));
                break;
            }
        }

        $xml->asXML($filePath);
    }

    public static function delete($id)
    {
        list($xml, $filePath) = XmlService::prepareData();

        $users = $xml->Users;
        $index = 0;

        foreach ($users->User as $user) {
            if ((int)$user->ID === (int)$id) {
                unset($users->User[$index]);
                break;
            }
            $index++;
        }

        $xml->asXML($filePath);
    }

    private static function generateNewId($xml)
    {
        $maxId = 0;

        foreach ($xml->Users->User as $user) {
            $currentId = (int) $user->ID;
            if ($currentId > $maxId) {
                $maxId = $currentId;
            }
        }

        return $maxId + 1;
    }
}
