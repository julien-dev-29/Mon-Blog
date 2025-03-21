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
     * Génére un champ pour les formulaires
     *
     * @param array $context
     * @param string $key
     * @param mixed $value
     * @param mixed $label
     * @param array $options
     * @return string
     */
    public function field(array $context, string $key, $value, ?string $label = null, array $options = []): string
    {
        $error = $this->getErrors(context: $context, key: $key);
        $error ? $feedback = 'is-invalid' : $feedback = '';
        $attributes = [
            'class' => $options['class'] ?? '',
            'id' => $key,
            'name' => $key
        ];
        $value = $this->convertValue($value);
        $type = $options['type'] ?? 'text';
        if ($type === 'textarea') {
            $html = $this->textarea($key, $value, $feedback);
        } elseif (array_key_exists('options', $options)) {
            $html = $this->select($value, $options['options'], $attributes);
        } else {
            $html = $this->input($attributes, $value, $feedback);
        }
        $html .= $this->getErrorHTMLElement($key, $error);
        return "<label for=\"$key\" class=\"form-label\">$label</label>$html";
    }

    /**
     * Génére un champ input
     *
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
     * Génére un champ testarea
     *
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
     * Génére un champ select
     *
     * @param string $value
     * @param array $options
     * @param array $attributes
     * @return string
     */
    public function select(string $value, array $options, array $attributes)
    {
        $id = $attributes['id'];
        $name = $attributes['name'];
        $htmlOptions = array_reduce(
            array: array_keys($options),
            callback: function (string $html, string $key) use ($options, $value) {
                $selected = $key === $value ? "selected" : "";
                return "$html<option value=\"$key\" $selected>$options[$key]</option>";
            },
            initial: ""
        );
        return "<select class=\"form-control\" id=\"$id\" name=\"$name\">
        $htmlOptions
        </select>";
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
        }
        return null;
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
