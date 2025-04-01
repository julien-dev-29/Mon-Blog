<?php

namespace Framework;

use DateTime;
use Framework\Validator\ValidationError;
use Psr\Http\Message\UploadedFileInterface;

use PDO;

class Validator
{
    private const MIME_TYPES = [
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
        'pdf' => 'image/pdf'
    ];

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

    /**
     * Vérifie le format de la date
     * @param string $key
     * @param string $format
     * @return Validator
     */
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
     * Vérifie si le fichier a été uploadé avec succès
     * @param string $key
     * @return static
     */
    public function uploaded(string $key): self
    {
        /**
         * @var UploadedFileInterface
         */
        $file = $this->getValue($key);
        if ($file === null || $file->getError() !== UPLOAD_ERR_OK) {
            $this->addError($key, 'uploaded');
        }
        return $this;
    }

    /**
     * Summary of extension
     * @param string $key
     * @param array $extensions
     * @return Validator
     */
    public function extension(string $key, array $extensions): self
    {
        /**
         * @var UploadedFileInterface
         */
        $file = $this->getValue($key);
        if ($file !== null && $file->getError() === UPLOAD_ERR_OK) {
            $type = $file->getClientMediaType();
            $extension = mb_strtolower(pathinfo(
                path: $file->getClientFilename(),
                flags: PATHINFO_EXTENSION
            ));
            $expectedType = self::MIME_TYPES[$extension] ?? null;
            if (!in_array($extension, $extensions)
                || $expectedType !== $type
            ) {
                $this->addError(
                    key: $key,
                    rule: 'filetype',
                    attributes: [join(', ', $extensions)]
                );
            }
        }
        return $this;
    }

    /**
     * Vérifie l'éxistence d'un enregistrement
     * @param string $key
     * @param string $table
     * @param \PDO $pdo
     * @return Validator
     */
    public function exists(string $key, string $table, PDO $pdo): self
    {
        $id = $this->getValue($key);
        $statement = $pdo->prepare("SELECT id FROM $table WHERE id = ?");
        $statement->execute([$id]);
        if ($statement->fetchColumn() === false) {
            $this->addError(
                key: $key,
                rule: 'exists',
                attributes: [$table]
            );
        }
        return $this;
    }

    public function unique(string $key, string $table, PDO $pdo, ?int $exclude = null): self
    {
        $value = $this->getValue($key);
        $query = "SELECT id FROM $table WHERE $key = ?";
        $params = [$value];
        if ($exclude !== null) {
            $query .= " AND id != ?";
            $params[] = $exclude;
        }
        $statement = $pdo->prepare(
            query: $query
        );
        $statement->execute($params);
        if ($statement->fetchColumn() !== false) {
            $this->addError($key, 'unique', [$value]);
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
