<?php

namespace Tests\Framework\Twig;

use Framework\Twig\FormExtension;
use PHPUnit\Framework\TestCase;

/**
 * Summary of FormExtension
 */
class FormExtensionTest extends TestCase
{
    private $formExtension;

    public function setUp(): void
    {
        $this->formExtension = new FormExtension();
    }

    /**
     * @return void
     */
    public function testField()
    {
        $html = $this->formExtension->field(
            context: [],
            key: 'name',
            value: 'demo',
            label: 'Titre'
        );
        $this->assertEquals(
            expected: $this->trimHTML("<label for=\"name\" class=\"form-label\">Titre</label>
            <input type=\"text\" class=\"form-control is-valid\" id=\"name\" 
            name=\"name\" value=\"demo\" aria-describedby=\"titreHelp\"><div id=\"name\" class=\"valid-feedback\">Le champ name estvalide!</div>"),
            actual: $this->trimHTML($html)
        );
    }

    public function testFieldWithClass()
    {
        $html = $this->formExtension->field(
            context: [],
            key: 'name',
            value: 'demo',
            label: 'Titre',
            options: ['class' => 'demo']
        );
        $this->assertEquals(
            expected: $this->trimHTML("<label for=\"name\" class=\"form-label\">Titre</label>
            <input type=\"text\" class=\"form-control is-valid demo\" id=\"name\" 
            name=\"name\" value=\"demo\" aria-describedby=\"titreHelp\"><div id=\"name\" class=\"valid-feedback\">Le champ name estvalide!</div>"),
            actual: $this->trimHTML($html)
        );
    }

    public function testTextarea()
    {
        $html = $this->formExtension->field(
            context: [],
            key: 'name',
            value: 'demo',
            label: 'Titre'
        );
        $this->assertEquals(
            expected: $this->trimHTML("<label for=\"name\" class=\"form-label\">Titre</label>
            <input type=\"text\" class=\"form-control is-valid\" id=\"name\" 
            name=\"name\" value=\"demo\" aria-describedby=\"titreHelp\"><div id=\"name\" class=\"valid-feedback\">Le champ name est valide!</div>"),
            actual: $this->trimHTML($html)
        );
    }

    public function testFieldWithErrors()
    {
        $context = ['errors' => ['name' => 'erreur']];
        $html = $this->formExtension->field(
            context: $context,
            key: 'name',
            value: 'demo',
            label: 'Titre'
        );
        $this->assertEquals(
            expected: $this->trimHTML("<label for=\"name\" class=\"form-label\">Titre</label>
            <input type=\"text\" class=\"form-control is-invalid\" id=\"name\" 
            name=\"name\" value=\"demo\" aria-describedby=\"titreHelp\"><div id=\"name\" class=\"invalid-feedback\">
            erreur</div>"),
            actual: $this->trimHTML($html)
        );
    }

    private function trimHTML(string $string)
    {
        return preg_replace('/\s+/', '', $string);
    }
}