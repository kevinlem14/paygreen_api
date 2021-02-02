<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends AbstractController
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    // Entity class name.
    protected $className;

    /**
     * BaseController constructor.
     * @param $serializer
     */
    public function __construct(SerializerInterface $serializer, EntityManagerInterface $em)
    {
        $this->serializer = $serializer;
        $this->em = $em;
    }

    protected function list()
    {
        $repo = $this->em->getRepository($this->className);
        $data = $repo->findAll();

        return new JsonResponse($this->serializer->toArray(['data' => $data], $this->buildJmsContext()), Response::HTTP_OK);
    }

    /**
     * @return SerializationContext
     */
    protected function buildJmsContext()
    {
        $context = SerializationContext::create();

        $context->setGroups(['default']);
        $context->setSerializeNull(true);
        $context->enableMaxDepthChecks();

        return $context;
    }
}
