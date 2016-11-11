<?php

use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseTransactions;

class TestCase extends Laravel\Lumen\Testing\TestCase
{
    use DatabaseTransactions;
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    public function setUp()
    {
        parent::setUp();
        $this->refreshApplication();
        $this->withoutMiddleware();
    }

    public function tearDown()
    {
        $this->artisan('migrate:refresh');
        parent::tearDown();
    }

    protected function getTestRequest($requestMethod, $otherData)
    {
        $testRequest = new Request();
        $testRequest->setMethod($requestMethod);
        $testRequest->request->add($otherData);
        $testRequest->headers->add(['Authorization' => '12345678']);

        return $testRequest;
    }

    public function generateTestMails($count = 5, $additionalAttributes = [])
    {
        return factory(\App\Mail::class, $count)->create($additionalAttributes);
    }
}
