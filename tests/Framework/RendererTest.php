<?php

namespace Tests\Framework;

use App\Framework\Renderer;
use PHPUnit\Framework\TestCase;

class RendererTest extends TestCase
{
    /**
     * Summary of render
     * @var Renderer
     */
    private $renderer;

    /**
     * Summary of setUp
     * @return void
     */
    public function setUp(): void
    {
        $this->renderer = new Renderer();
        $this->renderer->addPath(__DIR__ . '/views');
    }

    public function testRenderWithTheRightPath()
    {
        $this->renderer->addPath('blog', __DIR__ . '/views');
        $content = $this->renderer->render('@blog/demo');
        $this->assertEquals('Salut les gens', $content);
    }

    public function testRenderWithTheDefaultPath()
    {
        $this->renderer->addPath(__DIR__ . '/views');
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