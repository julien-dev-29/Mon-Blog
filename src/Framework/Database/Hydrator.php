<?php
namespace Framework\Database;

class Hydrator
{
    public static function hydrate(array $array, $object)
    {
        $instance = is_string($object) ? new $object() : $object;
        foreach ($array as $key => $value) {
            $method = self::getSetter($key);
            if (method_exists($instance, $method)) {
                $instance->$method($value);
            } else {
                $property = lcfirst(self::getProperty($key));
                $instance->$property = $value;
            }
        }
        return $instance;
    }

    private static function getSetter(string $fieldName)
    {
        return 'set' . self::getProperty($fieldName);
    }

    private static function getProperty(string $fieldName)
    {
        return join(
            separator: '',
            array: array_map(
                callback: 'ucfirst',
                array: explode('_', $fieldName)
            )
        );
    }
}
