<?php
use App\Http\Controllers\MailController;
use App\Repositories\MailRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;

/**
 * Created by Malik Abiola.
 * Date: 11/11/2016
 * Time: 20:41
 * IDE: PhpStorm
 */


class MailControllerTest extends TestCase
{
    protected $generatedMails;
    protected $mailRepository;

    public function setUp()
    {
        parent::setUp();
        $this->generatedMails = $this->generateTestMails(10);
        $this->mailRepository = App::make(MailRepository::class);
    }

    public function testIndexReturnsAllMails()
    {
        $response = $this->getController($this->getTestRequest('GET'))->index();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);

        $this->assertCount(10, $data['data']);
    }

    public function testIndexWithPaginationRequestReturnsPaginatedResponse()
    {
        $response = $this->getController($this->getTestRequest('GET', ['limit' => 5]))->index();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);

        $this->assertCount(5, $data['data']);
        $this->assertArrayHasKey('meta', $data);
        $this->assertEquals(10, $data['meta']['pagination']['total']);
    }

    public function testIndexThrowsException()
    {
        $response = $this->getController(
            $this->getTestRequest('GET', ['limit' => 5]),
            $this->getExceptionThrowingMockMailRepository('all')
        )->index();

        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function testShowReturnsIndividualMail()
    {
        $response = $this->getController($this->getTestRequest('GET'))->show($this->generatedMails[0]->uid);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);

        $this->assertEquals($this->generatedMails[0]->subject, $data['data']['subject']);
        $this->assertArrayHasKey('archived', $data['data']);
    }

    public function testShowCannotFindMail()
    {
        $response = $this->getController($this->getTestRequest('GET'))->show(-1);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());

    }

    public function testShowThrowsException()
    {
        $response = $this->getController(
            $this->getTestRequest('GET'),
            $this->getExceptionThrowingMockMailRepository('find')
        )->show($this->generatedMails[0]->uid);

        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function testArchivesReturnsArchivedMails()
    {
        $this->generateTestMails(5, ['archived' => true]);

        $response = $this->getController($this->getTestRequest('GET'))->archives();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);

        $this->assertCount(5, $data['data']);
    }

    public function testArchivesWithPaginationRequestReturnPaginatedList()
    {
        $this->generateTestMails(5, ['archived' => true]);

        $response = $this->getController($this->getTestRequest('GET', ['limit' => 2]))->archives();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);

        $this->assertCount(2, $data['data']);
        $this->assertEquals(5, $data['meta']['pagination']['total']);
    }

    public function testArchivesThrowException()
    {
        $response = $this->getController(
            $this->getTestRequest('GET'),
            $this->getExceptionThrowingMockMailRepository('getArchived')
        )->archives();

        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function testUpdateMailUpdatesMail()
    {
        $response = $this->getController(
            $this->getTestRequest('PATCH', ['read' => true, 'archive' => true])
        )->update($this->generatedMails[0]->uid);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $mail = $this->mailRepository->find($this->generatedMails[0]->uid);

        $this->assertTrue($mail->read);
        $this->assertTrue($mail->archived);
    }

    public function testUpdateMailWithInvalidParametersFail()
    {
        $response = $this->getController(
            $this->getTestRequest('PATCH', ['read' => "invalid"])
        )->update($this->generatedMails[0]->uid);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testUpdateNonExistentMailFails()
    {
        $response = $this->getController(
            $this->getTestRequest('PATCH', ['read' => true, 'archive' => true])
        )->update(-1);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * Return new instance of MailController
     * @param $request
     * @param null $mailRepository
     * @return MailController
     */
    private function getController($request, $mailRepository = null)
    {
        $mailRepository = is_null($mailRepository) ? $this->mailRepository : $mailRepository;

        return new MailController($request, $mailRepository);
    }

    public function tearDown()
    {
        parent::tearDown();
    }
}
