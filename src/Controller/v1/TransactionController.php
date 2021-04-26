<?php

namespace App\Controller\v1;

use App\Controller\BaseController;
use App\Entity\Transaction;
use App\Form\TransactionType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TransactionController
 * @package App\Controller\v1
 * @Route("/transactions")
 */
class TransactionController extends BaseController
{
    protected $className = Transaction::class;

    protected $formName = TransactionType::class;

    /**
     * @Route("", name="transaction_create", methods={"POST"})
     * @return JsonResponse
     */
    public function _create(Request $request)
    {
        $data = $this->create($request);
        $code = (isset($data['errors'])) ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;
        
        return new JsonResponse($data, $code);
    }
}
