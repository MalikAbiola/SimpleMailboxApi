<?php
/**
 * Created by Malik Abiola.
 * Date: 09/11/2016
 * Time: 04:38
 * IDE: PhpStorm
 */

namespace App\Console\Commands;


use App\Repositories\MailRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Mockery\CountValidator\Exception;

class ImportTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:test_messages {file?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import test data';

    protected $mailRepository;

    public function __construct(MailRepository $mailRepository)
    {
        parent::__construct();

        $this->mailRepository = $mailRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $file = is_null($this->argument("file")) ? __DIR__.'/../../../messages_sample.json' : $this->argument("file");

            $messages = json_decode(file_get_contents($file), true);

            $this->mailRepository->importMessages($messages['messages']);

            $this->info("Messages Imported Successfully.");

        } catch (Exception $e) {
            $this->error("An Error Occurred during importation. Please check logs.");
            Log::error(
                $e->getMessage(),
                [
                    'file'  => $e->getFile(),
                    'line'  => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]
            );
        }

        return;
    }
}
