<?php

/**
 * Created by Malik Abiola.
 * Date: 11/11/2016
 * Time: 21:35
 * IDE: PhpStorm
 */

use Illuminate\Http\Response;

class AuthorizationMiddlewareTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->turnMiddlewareOn();
    }

    public function testListMailsWithValidAuthorization()
    {
        $this->get("/mails", ['Authorization' => '12345678']);

        $this->assertResponseOk();
    }

    public function testListMailsWithoutValidAuthorization()
    {
        $this->get("/mails", ['Authorization' => '']);

        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function tearDown()
    {
        parent::tearDown();
    }
}
