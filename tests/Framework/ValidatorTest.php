<?php

namespace Tests\Framework;

use Framework\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    private function createValidator(array $params)
    {
        return new Validator(params: $params);
    }

    public function testRequiredFailed()
    {
        $errors = $this->createValidator(params: [
            'name' => 'joe'
        ])
            ->required('name', 'content')
            ->getErrors();
        $this->assertCount(1, $errors);
        $this->assertEquals("Le champs content est requis", (string) $errors['content']);
    }

    public function testRequiredSuccess()
    {
        $errors = $this->createValidator(params: [
            'name' => 'joe',
            'content' => 'content'
        ])
            ->required('name', 'content')
            ->getErrors();
        $this->assertCount(0, $errors);
    }

    public function testSlugSuccess()
    {
        $errors = $this->createValidator(params: [
            'slug' => 'slug'
        ])
            ->slug('slug')
            ->getErrors();
        $this->assertCount(0, $errors);
    }

    public function testSlugError()
    {
        $errors = $this->createValidator(params: [
            'slug1' => 'yolo-yolo-Yolo35',
            'slug2' => 'yolo-yolo_yolo35',
            'slug3' => 'yolo--yolo-yolo35'
        ])
            ->slug('slug1')
            ->slug('slug2')
            ->slug('slug3')
            ->slug('slug4')
            ->getErrors();
        $this->assertCount(3, $errors);
    }

    public function testNotEmpty()
    {
        $errors = $this->createValidator([
            'name' => 'Jurol',
            'content' => ''
        ])
            ->notEmpty('name', 'content')
            ->getErrors();
        $this->assertCount(1, $errors);
    }

    public function testLength()
    {
        $params = [
            'slug' => '123456789'
        ];
        $errors = $this->createValidator($params)->length('slug', 12)->getErrors();
        $this->assertCount(1, $errors);
        $this->assertEquals('Le champs slug doit contenir plus de 12 caractères', (string) $errors['slug']);
        $this->assertCount(0, $this->createValidator($params)->length('slug', 3)->getErrors());
        $this->assertCount(1, $this->createValidator($params)->length('slug', 12)->getErrors());
        $this->assertCount(1, $this->createValidator($params)->length('slug', 3, 4)->getErrors());
        $this->assertCount(0, $this->createValidator($params)->length('slug', 3, 20)->getErrors());
        $this->assertCount(0, $this->createValidator($params)->length('slug', null, 20)->getErrors());
        $this->assertCount(1, $this->createValidator($params)->length('slug', null, 8)->getErrors());
    }

    public function testDatetime()
    {
        $params1 = ['date' => '2021-12-12 11:12:13'];
        $params2 = ['date' => '2021-12-12 00:00:00'];
        $params3 = ['date' => '2013-02-29 11:12:13'];
        $this->assertCount(0, $this->createValidator($params1)->datetime('date')->getErrors());
        $this->assertCount(0, $this->createValidator($params2)->datetime('date')->getErrors());
        $this->assertCount(0, $this->createValidator($params3)->datetime('date')->getErrors());
    }
}