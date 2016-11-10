<?php
/**
 * Created by Malik Abiola.
 * Date: 10/11/2016
 * Time: 02:06
 * IDE: PhpStorm
 */

namespace App\Http\Controllers;


use App\Repositories\MailRepository;
use Illuminate\Http\Request;
use League\Fractal\Manager;

class MailController extends Controller
{
    protected $request;
    protected $mailRepository;

    public function __construct(Request $request, MailRepository $mailRepository)
    {
        parent::__construct(new Manager);
        $this->request = $request;
        $this->mailRepository = $mailRepository;
    }

    /**
     * List all emails.
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $page =  $this->request->get("page", 1);
            $limit = $this->request->get("limit", 15);

            $mails = $this->mailRepository->all($limit, $page);

            return $this->respondWithPaginatedCollection($mails, $this->mailRepository->getTransformer());

        } catch (\Exception $exception) {
            $this->logError($exception);
            return $this->errorInternalError();
        }
    }

    /**
     * Show mail
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $message = $this->mailRepository->find($id);

            if ($message) {
                return $this->respondWithItem($message, $this->mailRepository->getTransformer());
            }

            return $this->errorNotFound("Mail Not Found");
        } catch (\Exception $exception) {
            $this->logError($exception);
            return $this->errorInternalError();
        }
    }

    /**
     * Get archived mails
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function archives()
    {
        try {
            $page =  $this->request->get("page", 1);
            $limit = $this->request->get("limit", 15);

            $mails = $this->mailRepository->getArchived($limit, $page);

            return $this->respondWithPaginatedCollection($mails, $this->mailRepository->getTransformer());

        } catch (\Exception $exception) {
            $this->logError($exception);
            return $this->errorInternalError();
        }
    }
}
