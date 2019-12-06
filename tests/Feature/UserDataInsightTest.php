<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserDataInsightTest extends TestCase
{

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testHighChartUserData()
    {
        $request = $this->get('/user_data_insight');
        $response = $request->decodeResponseJson();

        // get first element of the response data
        $data = $response['data'][0];

        $request->assertStatus(200);
        $this->assertArrayHasKey('name', $data);
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('color', $data);
    }
}
