<?php

namespace App\Controller\Auth;

use App\Entity\PasswordReset;
use App\Entity\User;
use App\Form\Auth\ForgotPasswordType;
use App\Mailer\UserMailer;
use App\Security\TokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ForgotPasswordController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;
    /**
     * @var UserMailer
     */
    private $mailer;

    /**
     * ForgotPasswordController constructor.
     *
     * @param  EntityManagerInterface  $entityManager
     * @param  TokenGenerator          $tokenGenerator
     * @param  UserMailer              $userMailer
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TokenGenerator $tokenGenerator,
        UserMailer $userMailer
    ) {
        $this->entityManager = $entityManager;
        $this->tokenGenerator = $tokenGenerator;
        $this->mailer = $userMailer;
    }

    /**
     * @Route("password/reset", name="app_password_request")
     * @param  Request  $request
     *
     * @return Response
     * @throws \Exception
     */
    public function sendPasswordResetEmail(Request $request)
    {
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->entityManager->getRepository(User::class)
                ->findOneBy([
                    'email' => $form->get('email')->getData(),
                ]);

            $this->mailer->sendConfirmationEmailMessage(
                $this->createPasswordReset($user)
            );

            $this->addFlash('status',
                'We have e-mailed your password reset link!');
        }

        return $this->render('auth/password/email.html.twig',
            ['form' => $form->createView(),]);
    }

    /**
     * @param  User  $user
     *
     * @return PasswordReset
     * @throws \Exception
     */
    private function createPasswordReset(User $user): PasswordReset
    {
        $passwordReset = $user->getPasswordReset() ?? new PasswordReset();
        $passwordReset->setUser($user);
        $passwordReset->setToken($this->tokenGenerator->generateToken());
        $passwordReset->setCreatedAt(new \DateTime('now'));

        $this->entityManager->persist($passwordReset);
        $this->entityManager->flush();

        return $passwordReset;
    }

}
