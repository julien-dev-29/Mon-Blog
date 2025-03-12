<?php

namespace Tests\Framework\Session;

use Framework\Session\ArraySession;
use Framework\Session\FlashService;
use PHPUnit\Framework\TestCase;

class FlashServiceTest extends TestCase
{
    private $session;
    private $flashService;

    public function setUp(): void
    {
        $this->session = new ArraySession();
        $this->flashService = new FlashService($this->session);
    }

    public function testDeleteFlashMessage()
    {
        $this->flashService->success('yolo');
        $this->assertEquals('yolo', $this->flashService->get('success'));
        $this->assertNull( $this->session->get('flash'));
        $this->assertEquals('yolo', $this->flashService->get('success'));
    }
}