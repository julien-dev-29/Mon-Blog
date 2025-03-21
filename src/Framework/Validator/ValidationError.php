<?php

namespace Framework\Validator;

class ValidationError
{
    private $key;
    private $rule;
    private $attributes;
    private $messages = [
        'required' => 'Le champ %s est requis',
        'empty' => 'Le champ %s ne peut pas être vide',
        'slug' => 'Le champ %s n\'est pas un slug valide',
        'minLength' => 'Le champ %s doit contenir plus de %d caractères',
        'maxLength' => 'Le champ %s doit contenir moins de %d caractères',
        'betweenLength' => 'Le champ %s doit contenir entre %d et %d caractères',
        'datetime' => 'Le champ %s doit être une date valide',
        'exists' => 'Le champ %s n\'éxiste pas dans la table %s',
        'unique' => 'Le champ %s doit être unique'
    ];
    public function __construct(string $key, string $rule, array $attributes = [])
    {
        $this->key = $key;
        $this->rule = $rule;
        $this->attributes = $attributes;
    }

    public function __toString()
    {
        $params = array_merge([
            $this->messages[$this->rule],
            $this->key
        ], $this->attributes);
        return call_user_func_array('sprintf', $params);
    }
}
