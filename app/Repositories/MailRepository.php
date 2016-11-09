<?php
/**
 * Created by Malik Abiola.
 * Date: 09/11/2016
 * Time: 04:50
 * IDE: PhpStorm
 */

namespace App\Repositories;


use App\Mail;

class MailRepository
{
    protected $model;

    public  function __construct(Mail $mailModel)
    {
        $this->model = $mailModel;
    }

    public function importMessages(array $messages)
    {
        if (! is_array($messages) || empty($messages)) {
            throw new \InvalidArgumentException("Provided Messages Data Is Invalid");
        }

        $this->model->unguard();
        foreach($messages as $message) {
            $this->model->create($message);
        }

        $this->model->reguard();
    }
}
