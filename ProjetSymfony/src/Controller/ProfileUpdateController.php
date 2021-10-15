<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\EditUserType;
use http\Client\Curl\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProfileUpdateController extends AbstractController
{
    /**
     * @Route("/profile/update/{id}", name="profile_update")
     */
    public function editUser (Request $request, Participant $user) :Response
    {

        $form = $this->createForm(EditUserType::class,$user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur modifié avec succès');
            return $this->redirectToRoute('main');

        }


        return $this->render('profile_update/index.html.twig', [
            'editUserType' => $form->createView(),
        ]);
    }
}
