<?php


namespace App\Mailer;


use App\Entity\PasswordReset;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserMailer
{

    /**
     * @var MailerInterface
     */
    private $mailer;
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(
        MailerInterface $mailer,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
    }

    public function sendConfirmationEmailMessage(PasswordReset $passwordReset)
    {
        $user = $passwordReset->getUser();
        $resetUrl = $this->urlGenerator->generate('app_password_reset', [
            'token' => $passwordReset->getToken(),
            'email' => $user->getEmail()
        ],UrlGeneratorInterface::ABSOLUTE_URL);

        $email = (new TemplatedEmail())->from('no-reply@symfony.com')
            ->to($user->getEmail())
            ->subject('Reset Password Notification')
            ->textTemplate('auth/email/reset.txt.twig')->context([
                'user_name' => $user->getName(),
                'reset_url'  => $resetUrl
            ]);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
        }
    }

}