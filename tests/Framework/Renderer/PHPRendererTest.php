<?php

namespace Tests\Framework\Renderer;

use Framework\Renderer\PHPRenderer;
use PHPUnit\Framework\TestCase;

class PHPRendererTest extends TestCase
{
    /**
     * Summary of render
     * @var PHPRenderer
     */
    private $renderer;

    /**
     * Summary of setUp
     * @return void
     */
    public function setUp(): void
    {
        $this->renderer = new PHPRenderer();
        $this->renderer->addPath(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views');
    }

    public function testRenderWithTheRightPath()
    {
        $this->renderer->addPath('blog', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views');
        $content = $this->renderer->render('@blog/demo');
        $this->assertEquals('Salut les gens', $content);
    }

    public function testRenderWithTheDefaultPath()
    {
        $this->renderer->addPath(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views');
        $content = $this->renderer->render('demo');
        $this->assertEquals('Salut les gens', $content);
    }

    public function testRenderWithParams()
    {
        $content = $this->renderer->render('demoparams', [
            'nom' => 'Marc'
        ]);
        $this->assertEquals('Salut Marc', $content);
    }

    public function testGlobalParameters()
    {
        $this->renderer->addGlobal('nom', 'Jurol');
        $content = $this->renderer->render('demoparams');
        $this->assertEquals('Salut Jurol', $content);
    }
}