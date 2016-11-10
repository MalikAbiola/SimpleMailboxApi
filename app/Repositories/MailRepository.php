<?php
/**
 * Created by Malik Abiola.
 * Date: 09/11/2016
 * Time: 04:50
 * IDE: PhpStorm
 */

namespace App\Repositories;


use App\Mail;
use App\Transformers\MailTransformer;

class MailRepository
{
    protected $model;

    /**
     * MailRepository constructor.
     * @param Mail $mailModel
     */
    public  function __construct(Mail $mailModel)
    {
        $this->model = $mailModel;
    }

    /**
     * Mass insert an array of messages
     * @param array $messages
     */
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

    /**
     * Get a Paginated list of mails
     *
     * @param  int $perPage
     * @param int $page
     * @param  array $columns
     * @return mixed
     */
    public function all($perPage = 15, $page = 1, $columns = ['*'])
    {
        return $this->model->paginate($perPage, $columns, "page", $page);
    }

    /**
     * Find a model by id
     *
     * @param $id
     * @param  array $columns
     * @return mixed
     */
    public function find($id, $columns = ['*'])
    {
        return $this->model->find($id, $columns);
    }

    /**
     * Get a paginated list of archived mails.
     *
     * @param int $perPage
     * @param int $page
     * @param array $columns
     * @return mixed
     */
    public function getArchived($perPage = 15, $page = 1, $columns = ['*'])
    {
        return $this->model->where("archived", 1)
            ->paginate($perPage, $columns, "page", $page);
    }

    /**
     * Update a mail model
     *
     * @param  array  $data
     * @param  $id
     * @return mixed
     */
    public function update($id, array $data)
    {
        return $this->model->where("uid", $id)->update($data);
    }

    /**
     * Get mail transformer for presentation
     * @param $showArchiveStatus
     * @return MailTransformer
     */
    public function getTransformer($showArchiveStatus = false)
    {
        return new MailTransformer($showArchiveStatus);
    }
}
