<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class RegisterController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this -> entityManager = $entityManager;
    }

    /**
     * @Route("/inscription", name="register")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $encoder
     * @return Response
     */
    public function index(Request $request,EntityManagerInterface $manager,UserPasswordHasherInterface $encoder)
    {
        $user = new User();
        $form = $this -> createForm(RegisterType::class, $user);
        $form -> handleRequest($request);
        if ($form -> isSubmitted() && $form -> isValid()) {
            $hashed=$encoder->hashPassword($user,$user->getPassword());
            $user->setPassword($hashed);
//            dd($user);
            $manager->persist($user);
            $manager->flush();
            $this -> addFlash('succes', 'votre compte a été crée');
        }
        return $this -> render('register/index.html.twig', [
            'form' => $form -> createView()
        ]);
    }
}
