<?php

use App\Mail;
use App\Repositories\MailRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
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

    protected function getTestRequest($requestMethod, $otherData = [])
    {
        $testRequest = new Request();
        $testRequest->setMethod($requestMethod);
        $testRequest->request->add($otherData);
        $testRequest->headers->add(['Authorization' => '12345678']);

        return $testRequest;
    }

    protected function generateTestMails($count = 5, $additionalAttributes = [])
    {
        return factory(Mail::class, $count)->create($additionalAttributes);
    }

    protected function getExceptionThrowingMockMailRepository($methodToMock)
    {
        $mockedMailRepository = \Mockery::mock(App::make(MailRepository::class))->makePartial();
        $mockedMailRepository->shouldReceive($methodToMock)->withAnyArgs()->andThrow(new Exception("Mock Exception"));

        return $mockedMailRepository;
    }


    /**
     * Turn on middleware for the test.
     *
     */
    protected function turnMiddlewareOn()
    {
        $this->app->instance('middleware.disable', false);
    }
}
