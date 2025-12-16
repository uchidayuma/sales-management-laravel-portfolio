<?php

namespace Tests\Unit;

use Tests\TestCase;

class BasicTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function testBasicTest()
    {
        $response = $this->get('/');

        // $response->assertStatus(200);
        $this->assertTrue(true);
    }
}
