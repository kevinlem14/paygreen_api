<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    private $passwordEncoder;

    /**
     * SecurityController constructor.
     * @param $passwordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param Request $request
     * @Route("/login", name="login", methods={"POST"})
     */
    public function login(Request $request)
    {
        $form = $this->createForm(LoginType::class, null, [
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        $errors = [];
        if($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            try {
                $user = $this->verifyCredentials($data['email'], $data['password']);
                $data = [
                    'email' => $user->getEmail(),
                    'apiToken' => $user->getApiToken()
                ];

                return new JsonResponse(['data' => $data], Response::HTTP_OK);
            } catch(\Exception $e){
                $errors['form'] = $e->getMessage();
            }
        }

        if(!$form->isSubmitted()){
            $errors['form'] = 'No data received';
        }
        foreach ($form as $child){
            foreach ($child->getErrors(true) as $error){
                $errors[$child->getName()] = $error->getMessage();
            }
        }

        return new JsonResponse(['errors' => $errors], Response::HTTP_FORBIDDEN);
    }

    /**
     * Check if user exist and password is valid. Return user if OK
     *
     * @param $email
     * @param $password
     * @return User
     * @throws \Exception
     */
    private function verifyCredentials($email, $password)
    {
        $userRepo = $this->getDoctrine()->getManager()->getRepository(User::class);

        $user = $userRepo->findOneBy(['email' => $email]);
        if(!$user){
            throw new \Exception('Email or Password invalid');
        }

        if(!$this->passwordEncoder->isPasswordValid($user, $password)){
            throw new \Exception('Email or Password invalid');
        }

        return $user;
    }
}