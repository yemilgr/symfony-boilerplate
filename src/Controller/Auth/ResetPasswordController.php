<?php


namespace App\Controller\Auth;


use App\Form\Auth\ResetPasswordType;
use App\Repository\PasswordResetRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordController extends AbstractController
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var PasswordResetRepository
     */
    private $resetRepository;

    /**
     * ResetPasswordController constructor.
     *
     * @param  UserPasswordEncoderInterface  $passwordEncoder
     * @param  PasswordResetRepository       $resetRepository
     * @param  UserRepository                $userRepository
     */
    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        PasswordResetRepository $resetRepository,
        UserRepository $userRepository
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->resetRepository = $resetRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/password/reset/{token}", name="app_password_reset")
     *
     * @param  Request  $request
     * @param  string   $token
     *
     * @return Response
     */
    public function resetPassword(Request $request, string $token)
    {
        $passwordReset = $this->resetRepository->findOneBy([
            'token' => $token,
        ]);

        if ( ! $passwordReset || $passwordReset->isExpired()) {
            $this->addFlash('danger', 'Token invalid/expired.');
            return  $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(ResetPasswordType::class, null, [
            'email' => $request->query->get('email'),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $passwordReset->getUser();
            $password = $form->get('password')->getData();

            $this->userRepository->upgradePassword($user,
                $this->passwordEncoder->encodePassword($user, $password)
            );
            $this->resetRepository->deleteByToken($token);

            $this->addFlash('success', 'Password changed.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('auth/password/reset.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}