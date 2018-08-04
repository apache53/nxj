<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        //$response = $this->get('http://admin.test.ledu.com/api/newsbulletin/');
        $response = $this->json('get','http://admin.test.ledu.com/api/newsbulletin/');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'error',
                'msg',
                'data'
            ])->assertExactJson([
                'error'=>1,
                'msg'=>'success',
                'data'=>[]
            ]);
    }
}
