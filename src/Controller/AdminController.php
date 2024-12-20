<?php 

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


class AdminController extends AbstractController
{
#[Route('/admin', name: 'admin_dashboard')]
public function index(): Response
{
return $this->render('admin/dashboard.html.twig', [
'controller_name' => 'AdminController',
]);
}
}