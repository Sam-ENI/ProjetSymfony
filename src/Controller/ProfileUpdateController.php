<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\ParticipantPictureName;
use App\Form\EditUserType;
use App\Form\ParticipantPictureFileType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use http\Client\Curl\User;
use Monolog\Handler\IFTTTHandler;
use PhpParser\Node\Stmt\TryCatch;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProfileUpdateController extends AbstractController
{
    /**
     * @Route("/profile/update/{id}", name="profile_update")
     * */
    public function editUser (Request $request, EntityManagerInterface $em, ParticipantRepository $repo,
                              UserPasswordHasherInterface $userPasswordHasherInterface, $id) :Response
    {

        //Instanciation de la classe Participant
        $participant = $repo->find($id);
        $form = $this->createForm(EditUserType::class, $participant);
        $form->handleRequest($request);

        //$entityManager = $this->getDoctrine()->getManager();

        //TODO Image upload

        if ($form->isSubmitted() && $form->isValid()){
            $participant->setAdministrateur(false);
            $participant->setActif(false);
            $participant->setPassword(
                $userPasswordHasherInterface->hashPassword(
                    $participant,
                    $form->get('password')->getData()
                )
            );

            //insert en base de données

            $em->persist($participant);
            $em->flush();

            //message
                $this->addFlash('success', 'Utilisateur modifié avec succès');
                return $this->redirectToRoute('main');
            }

        $em->flush();
        return $this->render('profile_update/index.html.twig', [
            'editUserType' => $form->createView(),
            /*'participantPictureFormView' => $participantPictureFileType->createView(),*/
            /*'user' => $user,*/
        ]);
    }
}
