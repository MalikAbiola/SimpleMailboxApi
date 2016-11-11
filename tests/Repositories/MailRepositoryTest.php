<?php

/**
 * Created by Malik Abiola.
 * Date: 11/11/2016
 * Time: 02:23
 * IDE: PhpStorm
 */

use App\Mail;
use App\Repositories\MailRepository;
use Illuminate\Support\Facades\App;

class MailRepositoryTest extends TestCase
{
    protected $mailRepository;

    public function setUp()
    {
        parent::setUp();
        $this->mailRepository = App::make(MailRepository::class);
    }

    public function testImportMessagesImportsMessages()
    {
        $testImportMessages = [
            [
                "uid" => "1",
                "sender" => "Virgina Woolf",
                "subject" => "debt",
                "message" => "The story is about an obedient midwife and a graceful scuba diver who is in debt to a fence.",
                "time_sent" => 1456767867
            ],
            [
                "uid" => "2",
                "sender" => "George Orwell",
                "subject" => "chemist",
                "message" => "This is a tale about degeneracy. The story is about a chemist. ",
                "time_sent" => 1456767867
            ]
        ];

        $this->mailRepository->importMessages($testImportMessages);

        $this->assertCount(2, $this->mailRepository->all());
        $this->assertNotNull($this->mailRepository->find($testImportMessages[0]['uid']));
    }

    public function testImportMessagesThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->mailRepository->importMessages([]);
    }

    public function testGetAllMailsGetsAllMails()
    {
        $this->generateTestMails(10);

        $mails = $this->mailRepository->all(5);

        $this->assertCount(5, $mails);
    }

    public function testFindReturnsMail()
    {
        $generatedMail = $this->generateTestMails(1, ['uid' => 1]);
        $mail = $this->mailRepository->find(1);

        $this->assertNotNull($mail);
        $this->assertEquals($generatedMail->sender, $mail->sender);
    }

    public function testGetArchivedReturnsArchivedMailsOnly()
    {
        $this->generateTestMails(10);
        $this->generateTestMails(1, ['archived' => true]);

        $this->assertCount(1, $this->mailRepository->getArchived());
    }

    public function testUpdateMailUpdatesMail()
    {
        $this->generateTestMails(1, ['uid' => 1]);

        $this->mailRepository->update(1, ['read' => true]);

        $retrievedMail = $this->mailRepository->find(1);

        $this->assertTrue($retrievedMail->read);
    }

    public function testGetTransformerReturnsMailTransformer()
    {
        $this->assertInstanceOf(\App\Transformers\MailTransformer::class, $this->mailRepository->getTransformer());
    }

    public function tearDown()
    {
        parent::tearDown();
    }
}
