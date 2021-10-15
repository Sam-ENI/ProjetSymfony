<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\UpdateParticipantType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="profile")
     */
    public function index(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface): Response
    {
        $user = $this->getUser();
        $user->setPassword("");
        $form = $this->createForm(UpdateParticipantType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasherInterface->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            //$entityManager->persist($user);
            $entityManager->flush();

        }
        return $this->render('profile/newSortie.html.twig', [
                        'ProfileForm' => $form->createView(),
                    ]);
    }
}
