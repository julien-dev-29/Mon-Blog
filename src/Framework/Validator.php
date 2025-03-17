<?php

namespace Framework;

use DateTime;
use Framework\Validator\ValidationError;

class Validator
{
    /**
     * @var array
     */
    private $params;

    /**
     * @var string[]
     */
    private $errors = [];
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * Vérifie que les champs sont présent dans le tableau
     * @param string[] $keys
     * @return Validator
     */
    public function required(string ...$keys): self
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if ($value === null) {
                $this->addError($key, 'required');
            }
        }
        return $this;
    }

    public function notEmpty(string ...$keys): self
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if ($value === null || !empty($value)) {
                $this->addError($key, 'empty');
            }
        }
        return $this;
    }

    public function length(string $key, ?int $min, ?int $max = null)
    {
        $value = $this->getValue($key);
        $length = mb_strlen($value);
        if (($min !== null &&
                $max !== null) &&
            ($length <= $min || $length >= $max)
        ) {
            $this->addError($key, 'betweenLength', [$min, $max]);
        }
        if ($min !== null &&
            $length <= $min
        ) {
            $this->addError($key, 'minLength', [$min]);
        }
        if ($max !== null &&
            $length >= $max
        ) {
            $this->addError($key, 'maxLength', [$max]);
        }
        return $this;
    }

    /**
     * Vérifie que l'élément est un slug
     * @param string $key
     * @return Validator
     */
    public function slug(string $key): self
    {
        $value = $this->getValue($key);
        $pattern = '/^[a-z0-9]+(-[a-z0-9]+)*$/';
        if ($value !== null && !preg_match(
            pattern: $pattern,
            subject: $this->params[$key]
        )
        ) {
            $this->addError($key, 'slug');
        }
        return $this;
    }

    public function datetime(string $key, string $format = 'Y-m-d H:i:s'): self
    {
        $value = $this->getValue($key);
        $datetime = DateTime::createFromFormat($format, $value);
        if (!$datetime) {
            $this->addError($key, 'datetime', [$format]);
        }
        return $this;
    }

    /**
     * Retourne true si le tableau d'erreurs est vide
     * @return bool
     */
    public function isValid()
    {
        return empty($this->errors);
    }

    /**
     * Récupère les erreurs
     * @return ValidationError[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Ajoute une erreur
     * @param string $key
     * @param string $rule
     * @return void
     */
    private function addError(string $key, string $rule, ?array $attributes = []): void
    {
        $this->errors[$key] = new ValidationError(
            key: $key,
            rule: $rule,
            attributes: $attributes
        );
    }

    private function getValue(string $key)
    {
        if (array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }
        return null;
    }
}
