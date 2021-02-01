<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'app:create:user';

    private $passwordEncoder;
    private $em;
    private $params;

    /**
     * CreateUserCommand constructor.
     * @param $passwordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em, ParameterBagInterface $params)
    {
        parent::__construct();
        $this->passwordEncoder = $passwordEncoder;
        $this->em = $em;
        $this->params = $params;
    }

    protected function configure()
    {
        $this->setDescription('Create a new user');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Email
        $helper = $this->getHelper('question');
        $question = new Question('User email : ');
        $question->setValidator(function ($answer) {

            $validator = Validation::createValidator();
            $violations = $validator->validate($answer, [
                new NotBlank(),
                new Email()
            ]);
            if (count($violations) !== 0) {
                throw new \RuntimeException(
                    $violations[0]->getMessage()
                );
            }

            $results = $this->em->getRepository(User::class)->findBy([
                "email" => $answer
            ]);
            if (count($results) !== 0) {
                throw new \RuntimeException(
                    'This email is already used.'
                );
            }

            return $answer;
        });
        $question->setMaxAttempts(3);
        $userEmail = $helper->ask($input, $output, $question);

        // Password
        $question = new Question('User password : ');
        $question->setValidator(function ($answer) {

            $validator = Validation::createValidator();
            $violations = $validator->validate($answer, [
                new NotBlank(),
                new Regex(["value" => "/.{6,}/", "message" => "This password is too short (at least 6 chars)."]),
                new Regex(["value" => '/^[^\\\\"]+$/', "message" => "This password contains bad chars (\\ or \")."])
            ]);

            if (count($violations) !== 0) {
                throw new \RuntimeException(
                    $violations[0]->getMessage()
                );
            }

            return $answer;
        });
        $question->setHidden(true);
        $question->setMaxAttempts(3);
        $userPassword = $helper->ask($input, $output, $question);

        // Password confirmation
        $GLOBALS['userPassword'] = $userPassword;
        $question = new Question('Password confirmation : ');

        $question->setValidator(function ($answer) {

            $userPassword = $GLOBALS['userPassword'];
            if ($answer !== $userPassword) {
                throw new \RuntimeException(
                    'Confirmation does not match the password.'
                );
            }

            return $answer;
        });

        $question->setHidden(true);
        $question->setMaxAttempts(3);
        $helper->ask($input, $output, $question);

        // ROLES
        $rolesParams = $this->params->get('security.role_hierarchy.roles');
        $roles = ['ROLE_USER'];
        foreach ($rolesParams as $key => $value) {
            $roles[] = $key;
        }

        $question = new ChoiceQuestion(
            'Please select a role (default to ROLE_USER)',
            $roles,
            0
        );
        $question->setMaxAttempts(3);
        $userRole = $helper->ask($input, $output, $question);

        // Create user
        $user = new User();
        $user->setEmail($userEmail);
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            $userPassword
        ));
        $user->setApiToken(bin2hex(random_bytes(60)));
        if($userRole !== 'ROLE_USER'){
            $user->setRoles([$userRole]);
        }

        $this->em->persist($user);
        $this->em->flush();

        $output->writeln('<bg=green;fg=white> User created </>');
        return 0;
    }
}
