<?php

namespace Framework\Twig;

use DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FormExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'field',
                [$this, 'field'],
                [
                    'is_safe' =>
                        ['html'],
                    'needs_context' => true
                ]
            )
        ];
    }

    /**
     * Génére un champ de type input ou textarea pour les formulaires
     * @param array $context
     * @param string $key
     * @param mixed $value
     * @param mixed $label
     * @param array $options
     * @return string
     */
    public function field(array $context, string $key, $value, ?string $label = null, array $options = []): string
    {
        // Errors
        $error = $this->getErrors(context: $context, key: $key);
        $error ? $feedback = 'is-invalid' : $feedback = 'is-valid';

        $attributes = [
            'class' => $options['class'] ?? '',
            'id' => $key,
            'name' => $key
        ];

        // Value
        $value = $this->convertValue($value);

        // Type
        $type = $options['type'] ?? 'text';
        $type === 'textarea' ?
            $html = $this->textarea($key, $value, $feedback)
            :
            $html = $this->input($attributes, $value, $feedback);
        $html .= $this->getErrorHTMLElement($key, $error);

        return "<label for=\"$key\" class=\"form-label\">$label</label>$html";
    }

    /**
     * Summary of input
     * @param string $key
     * @param string|null $value
     * @param string|null $feedback
     * @return string
     */
    public function input(array $attributes, ?string $value, ?string $feedback): string
    {
        $id = $attributes['id'];
        $name = $attributes['name'];
        $class = $attributes['class'];
        return "<input type=\"text\" 
        class=\"form-control $feedback $class\" 
        id=\"$id\" name=\"$name\" value=\"$value\" 
        aria-describedby=\"titreHelp\">";
    }

    /**
     * Summary of textarea
     * @param string $key
     * @param mixed $value
     * @param mixed $feedback
     * @return string
     */
    public function textarea(string $key, ?string $value, ?string $feedback)
    {
        return "<textarea class=\"form-control $feedback\" name=\"$key\" rows=\"10\" id=\"$key\">$value</textarea>";
    }

    /**
     * Génére un élément html pour l'erreur
     * @param string $key
     * @param mixed $error
     * @return string
     */
    private function getErrorHTMLElement(string $key, $error)
    {
        if ($error) {
            return "<div id=\"$key\" class=\"invalid-feedback\">$error</div>";
        } else {
            return "<div id=\"$key\" class=\"valid-feedback\">Le champ $key est valide!</div>";
        }
    }

    /**
     * Retourne les erreurs ou false
     * @param array $context
     * @param string $key
     */
    private function getErrors(array $context, string $key)
    {
        return $context['errors'][$key] ?? false;
    }

    private function convertValue($value)
    {
        if ($value instanceof DateTime) {
            return $value->format("Y-m-d H:i:s");
        }
        return $value;
    }
}
