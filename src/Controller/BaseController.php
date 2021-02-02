<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

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

    // Entity form name.
    protected $formName;

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

        return $this->serializer->toArray(['data' => $data], $this->buildJmsContext());
    }

    protected function create(Request $request)
    {
        $entity = new $this->className;
        $form = $this->createForm(
            $this->formName,
            $entity,
            [
                'method' => 'POST'
            ]
        );

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($entity);
            $this->em->flush();

            return $this->serializer->toArray(['data' => $entity], $this->buildJmsContext());
        }

        $errors = [];
        if(!$form->isSubmitted()){
            $errors['form'] = 'No data received';
        }
        foreach ($form as $child){
            foreach ($child->getErrors(true) as $error){
                $errors[$child->getName()] = $error->getMessage();
            }
        }

        return ['errors' => $errors];
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
