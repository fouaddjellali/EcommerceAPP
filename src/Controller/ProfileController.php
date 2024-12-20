<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
#[Route('/profile', name: 'profile')]
public function index(): Response
{
$user = $this->getUser(); // Récupère l'utilisateur connecté

if (!$user) {
return $this->redirectToRoute('app_login'); // Redirige si non connecté
}

return $this->render('profile/index.html.twig', [
'user' => $user,
]);
}
}