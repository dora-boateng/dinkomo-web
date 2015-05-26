<?php namespace App\Traits;

use Symfony\Component\Yaml\Yaml;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

trait ImportableResourceTrait
{
    public static function importFromFormat($data, $format)
    {
        switch ($format)
        {
            case 'json':
                $result = static::importFromJson($data);
                break;

            case 'yml':
            case 'yaml':
                $result = static::importFromYaml($data);
                break;

            default:
                throw new \Exception('Unsupported import format.');
        }

        return $result;
    }

    public static function importFromJson($data) {
        return JsonResponse::create($data)->getData();
    }

    public static function importFromYaml($data) {
        return Yaml::parse($data);
    }
}