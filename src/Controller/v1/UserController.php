<?php

namespace App\Controller\v1;

use App\Controller\BaseController;
use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TransactionController
 * @package App\Controller\v1
 * @Route("/users")
 */
class UserController extends BaseController
{
    protected $className = User::class;

    /**
     * @Route("", name="user_list", methods={"GET"})
     * @return JsonResponse
     * @IsGranted("ROLE_ADMIN")
     */
    public function _list()
    {
        $data = $this->list();

        return new JsonResponse($data, Response::HTTP_OK);
    }
}
