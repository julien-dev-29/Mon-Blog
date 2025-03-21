<?php

namespace Tests\Framework\Twig;

use Framework\Twig\FormExtension;
use PHPUnit\Framework\TestCase;

/**
 * Summary of FormExtension
 */
class FormExtensionTest extends TestCase
{
    /**
     * @var FormExtension
     */
    private $formExtension;

    /**
     * @return void
     */
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
            <input type=\"text\" class=\"form-control\" id=\"name\" 
            name=\"name\" value=\"demo\" aria-describedby=\"titreHelp\">"),
            actual: $this->trimHTML($html)
        );
    }

    /**
     * @return void
     */
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
            <input type=\"text\" class=\"form-control demo\" id=\"name\" 
            name=\"name\" value=\"demo\" aria-describedby=\"titreHelp\">"),
            actual: $this->trimHTML($html)
        );
    }

    /**
     * @return void
     */
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
            <input type=\"text\" class=\"form-control\" id=\"name\" 
            name=\"name\" value=\"demo\" aria-describedby=\"titreHelp\">"),
            actual: $this->trimHTML($html)
        );
    }

    /**
     * @return void
     */
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

    public function testSelect()
    {
        $html = $this->formExtension->field(
            [],
            'name',
            2,
            'Titre',
            ['options' => [1 => 'Demo1', 2 => 'Demo2']]
        );
        $this->assertEquals(
            expected: $this->trimHTML("<label for=\"name\" class=\"form-label\">Titre</label>
            <select class=\"form-control\" id=\"name\" name=\"name\">
            <option value=\"1\">Demo1</option>
            <option value=\"2\" selected>Demo2</option>
            </select>"),
            actual: $this->trimHTML($html)
        );
    }

    /**
     * @param string $string
     * @return array|string|null
     */
    private function trimHTML(string $string)
    {
        return preg_replace('/\s+/', '', $string);
    }
}