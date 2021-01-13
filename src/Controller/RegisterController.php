<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     */
    public function Register(Request $request, UserPasswordEncoderInterface $passwordEncoder, FlashyNotifier $flashyNotifier): Response
    {

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $flashyNotifier->info('Account Created Successfully!');

            return $this->redirectToRoute('blog');
        }
        return $this->render('register/index.html.twig', [
            'registerForm' => $form->createView(),
        ]);
    }
}
