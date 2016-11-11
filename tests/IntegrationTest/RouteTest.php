<?php
use App\Repositories\MailRepository;

/**
 * Created by Malik Abiola.
 * Date: 11/11/2016
 * Time: 20:52
 * IDE: PhpStorm
 */

use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;

class RouteTest extends TestCase
{
    protected $generatedMails;

    public function setUp()
    {
        parent::setUp();
        $this->generatedMails = $this->generateTestMails(10);
    }

    public function testListAllMailsListsAllMails()
    {
        $this->get("/mails");
        $this->assertResponseOk();

        $data = json_decode($this->response->getContent(), true);

        $this->assertCount(10, $data['data']);
    }

    public function testPaginateListAllMailsReturnsPaginatedMails()
    {
        $this->get("/mails?page=1&limit=5");
        $this->assertResponseOk();

        $data = json_decode($this->response->getContent(), true);

        $this->assertCount(5, $data['data']);
    }

    public function testListAllMailsReturnsInternalServerError()
    {
        $this->app->instance(MailRepository::class, $this->getExceptionThrowingMockMailRepository("all"));

        $this->get("/mails");
        $this->assertResponseStatus(Response::HTTP_INTERNAL_SERVER_ERROR);

        $this->app->instance(MailRepository::class, App::make(MailRepository::class));
    }

    public function testGetIndividualMailReturnsIndividualMail()
    {
        $this->get("/mails/{$this->generatedMails[0]->uid}");

        $this->assertResponseOk();

        $data = json_decode($this->response->getContent(), true);

        $this->assertEquals($this->generatedMails[0]->subject, $data['data']['subject']);
    }

    public function testGetIndividualMailReturnsNotFound()
    {
        $this->get("/mails/-1");

        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
    }

    public function testGetIndividualMailReturnInternalServerError()
    {
        $this->app->instance(MailRepository::class, $this->getExceptionThrowingMockMailRepository("find"));

        $this->get("/mails/{$this->generatedMails[0]->uid}");

        $this->assertResponseStatus(Response::HTTP_INTERNAL_SERVER_ERROR);

        $this->app->instance(MailRepository::class, App::make(MailRepository::class));
    }

    public function testListAllArchivedMailsReturnArchivedMails()
    {
        $this->generateTestMails(5, ['archived' => true]);

        $this->get("/archives");
        $this->assertResponseOk();

        $data = json_decode($this->response->getContent(), true);

        $this->assertCount(5, $data['data']);
    }

    public function testGetPaginatedArchivedMailsReturnsPaginatedArchivedMails()
    {
        $this->generateTestMails(10, ['archived' => true]);

        $this->get("/archives?page=1&limit=5");
        $this->assertResponseOk();

        $data = json_decode($this->response->getContent(), true);

        $this->assertCount(5, $data['data']);
        $this->assertArrayHasKey('meta', $data);
        $this->assertEquals(10, $data['meta']['pagination']['total']);
    }

    public function testGetArchivedReturnsInternalServerError()
    {
        $this->generateTestMails(10, ['archived' => true]);
        $this->app->instance(MailRepository::class, $this->getExceptionThrowingMockMailRepository("getArchived"));

        $this->get("/archives");
        $this->assertResponseStatus(Response::HTTP_INTERNAL_SERVER_ERROR);

        $this->app->instance(MailRepository::class, App::make(MailRepository::class));
    }

    public function testSetMailReadStatusToTrueSuccessful()
    {
        $this->patch("/mails/{$this->generatedMails[0]->uid}", ["read" => true]);

        $this->assertResponseOk();

        $mailRepository = App::make(MailRepository::class);
        $mail = $mailRepository->find($this->generatedMails[0]->uid);

        $this->assertTrue($mail->read);
    }

    public function testSetMailArchiveStatusToTrueSuccessful()
    {
        $this->patch("/mails/{$this->generatedMails[0]->uid}", ["archive" => true]);

        $this->assertResponseOk();

        $mailRepository = App::make(MailRepository::class);
        $mail = $mailRepository->find($this->generatedMails[0]->uid);

        $this->assertTrue($mail->archived);
    }

    public function testSetMailReadStatusOfNonExistingMailToTrueReturns404()
    {
        $this->patch("/mails/-1", ["read" => true]);

        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
    }

    public function testSetMailReadStatusWithInvalidParamsReturnsHTTPBadRequest()
    {
        $this->patch("/mails/{$this->generatedMails[0]->uid}", ["read" => "invalid"]);

        $this->assertResponseStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testSetMailReadStatusReturnsInternalServerError()
    {
        $this->app->instance(MailRepository::class, $this->getExceptionThrowingMockMailRepository("update"));

        $this->patch("/mails/{$this->generatedMails[0]->uid}", ["read" => true]);
        $this->assertResponseStatus(Response::HTTP_INTERNAL_SERVER_ERROR);

        $this->app->instance(MailRepository::class, App::make(MailRepository::class));
    }

    public function tearDown()
    {
        parent::tearDown();
    }
}
