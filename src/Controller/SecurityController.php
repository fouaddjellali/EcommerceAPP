<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
    #[Route('/reset-password', name: 'app_forgot_password')]
    public function requestResetPassword(
        Request $request,
        UserRepository $userRepository,
        TokenGeneratorInterface $tokenGenerator,
        MailerInterface $mailer
    ): Response {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $user = $userRepository->findOneBy(['email' => $email]);

            if ($user) {
                // Générer un token
                $token = $tokenGenerator->generateToken();
                $user->setResetToken($token);
                $user->setTokenRequestedAt(new \DateTime());
                $userRepository->save($user, true);

                // Envoyer un email avec le lien de réinitialisation
                $url = $this->generateUrl('app_reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

                $email = (new Email())
                    ->from('noreply@votre-site.com')
                    ->to($user->getEmail())
                    ->subject('Réinitialisation de mot de passe')
                    ->html(sprintf('<p>Pour réinitialiser votre mot de passe, cliquez sur <a href="%s">ce lien</a>.</p>', $url));

                $mailer->send($email);
            }

            // Affiche un message de confirmation même si l'utilisateur n'existe pas pour éviter de révéler des informations
            $this->addFlash('success', 'Si un compte avec cet email existe, vous recevrez un email avec un lien pour réinitialiser le mot de passe.');

            return $this->redirectToRoute('app_login');
        }
        return $this->render('security/request_reset_password.html.twig');
    }
    #[Route('/reset-password/{token}', name: 'app_reset_password')]
    public function resetPassword(
        string $token,
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = $userRepository->findOneBy(['resetToken' => $token]);
        if (!$user || !$user->isTokenValid()) {
            $this->addFlash('danger', 'Le lien de réinitialisation est invalide ou expiré.');
            return $this->redirectToRoute('app_forgot_password');
        }
        if ($request->isMethod('POST')) {
            $newPassword = $request->request->get('password');
            $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
            $user->setResetToken(null); // Invalider le token après utilisation
            $user->setTokenRequestedAt(null);
            $userRepository->save($user, true);

            $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès.');
            return $this->redirectToRoute('app_login');
        }
        return $this->render('security/reset_password.html.twig', ['token' => $token]);
    }
    #[Route('/update-password', name: 'update_password')]
    public function updatePassword(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour mettre à jour votre mot de passe.');
        }
        $form = $this->createFormBuilder()
            ->add('old_password', PasswordType::class, [
                'label' => 'Ancien mot de passe',
                'mapped' => false,
                'required' => true,
            ])
            ->add('new_password', PasswordType::class, [
                'label' => 'Nouveau mot de passe',
                'mapped' => false,
                'required' => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Mettre à jour',
                'attr' => ['class' => 'btn btn-primary w-100'],
            ])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $oldPassword = $form->get('old_password')->getData();
            $newPassword = $form->get('new_password')->getData();
            // Vérifiez si l'ancien mot de passe est correct
            if (!$passwordHasher->isPasswordValid($user, $oldPassword)) {
                $this->addFlash('danger', 'L\'ancien mot de passe est incorrect.');
                return $this->redirectToRoute('update_password');
            }
            // Hash le nouveau mot de passe et l'enregistre
            $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Mot de passe mis à jour avec succès.');
            return $this->redirectToRoute('homepage'); // Redirige vers une autre page (ex : profil)
        }

        return $this->render('security/update_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
