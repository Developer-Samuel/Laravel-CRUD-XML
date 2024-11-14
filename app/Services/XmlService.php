<?php

namespace App\Services;

class XmlService
{
    public static function prepareData()
    {
        $filePath = storage_path('app/public/imports/users.xml');

        if (!file_exists($filePath)) {
            return [];
        }

        $xml = simplexml_load_file($filePath);

        return [$xml, $filePath];
    }
}
