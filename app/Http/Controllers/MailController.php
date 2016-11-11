<?php
/**
 * Created by Malik Abiola.
 * Date: 10/11/2016
 * Time: 02:06
 * IDE: PhpStorm
 */

namespace App\Http\Controllers;


use App\Repositories\MailRepository;
use App\Validators\RequestsValidator;
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
            $mail = $this->mailRepository->find($id);

            if ($mail) {
                return $this->respondWithItem($mail, $this->mailRepository->getTransformer(true));
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

    /**
     * Update mail object.
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update($id)
    {
        try {
            $validateUpdateRequest = RequestsValidator::validateMailUpdateRequest($this->request);

            if (! $validateUpdateRequest->passes()) {
                return $this->errorWrongArgs($validateUpdateRequest->errors());
            }

            $mail = $this->mailRepository->find($id);

            if (! $mail) {
                return $this->errorNotFound("Mail Not Found");
            }

            $updateRead = $this->request->get('read', null);

            if ($updateRead) {
                $this->mailRepository->update($id, ['read' => (bool) $updateRead]);
            }

            $updateArchive = $this->request->get('archive', null);

            if ($updateArchive) {
                $this->mailRepository->update($id, ['archived' => (bool) $updateArchive]);
            }

            return $this->respondSuccess();

        } catch (\Exception $exception) {
            $this->logError($exception);
            return $this->errorInternalError();
        }
    }
}
