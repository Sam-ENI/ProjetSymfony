<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Lieu;
use App\Entity\Ville;
use App\Form\LieuFormType;
use App\Form\SortieFormType;
use App\Form\DeleteSortieForm;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping AS ORM;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


class SortieController extends AbstractController
{

    /**
     * @Route ("/sortie/insert",name="insert")
     */
    public function insert(Request $request , EntityManagerInterface $em, EtatRepository $er): Response{
        //redirige vers le login si non connecté
        if(!$this->getUser()){
            return $this->redirectToRoute("app_login");
        }

        $sortie = new Sortie();
        $sortie->setDateHeureDebut(new \DateTime($request->request->get("dateDebut")));
        /*      dd($request->request->get("dateDebut"));*/
        $sortie->setDateLimiteInscription(new \DateTime($request->request->get("dateLimiteInscription")));
        $sortieForm = $this->createForm(SortieFormType::class, $sortie);


        $sortieForm->handleRequest($request);


        $lieu = new Lieu();

        $lieuForm = $this->createForm(LieuFormType::class, $lieu);


        //Vérifie si le bouton enregistrer est cliqué

        if ($request->request->get('save')){

            $etat = $em->getRepository(Etat::class)->findOneBy(['libelle'=>'créée']);
            $this->addFlash('warning', "La sortie a été ajouté à vos créations, pensez à publier !");

        } else if ($request->request->get('cancel')){
            $etat = $em->getRepository(Etat::class)->findOneBy(['libelle'=>'annulée']);
            $this->addFlash('warning', "La création de la sortie a été annulé!");
        } else {

            //vérifie la validation des formulaires avant d'envoyer

            if ($sortieForm->isSubmitted() && $sortieForm->isValid()){
                $now = new \DateTime();
                if($sortie->getDateLimiteInscription() < $now || $sortie->getNbInscriptionMax() == $sortie->getNbInscrits()){
                    $sortie->setEtat($er->find('3'));       /*si date limite d'inscription est passée ou que le nb d'inscrits est atteint, cloturee*/
                }else{
                    $sortie->setEtat($er->find('2'));
                }

                $sortie->setSite($this->getUser()->getSite());
                $participant = $this->getUser();
                $sortie->setOrganisateur($participant);
                $em->persist($sortie);
                /*         $etat = $this->em->getRepository(Etat::class)->findByLibel(Etat::created());
                         //Set status
                         $sortie->setEtat($etat);*/
                $em->flush();
                $this->addFlash("success", "Votre sortie est bien enregistrée");

                return $this->redirectToRoute('detail_sortie', ["id"=>$sortie->getId()]);
            }

        }

        return $this->render('sortie/newSortie.html.twig',[
            'sortieForm'=>$sortieForm->createView(),
            'lieuForm'=>$lieuForm->createView(),
            'site'=>$this->getUser()->getSite()
        ]);
    }

    /**
     * @Route("/sortie/infosLieu/{id}", name="infosLieu")
     */
    public function infosLieu(LieuRepository $repo,$id):Response{
        $lieu=$repo->find($id);
        return $this->json('{"rue":"'.$lieu->getRue().'","lat":"'.$lieu->getLatitude().'","long":"'.$lieu->getLongitude().'"}');
    }

    /**
     * @Route("/sortie/lieu/{id}", name="lieu")
     */
    public function afficherLieu(VilleRepository $repo, $id):Response{
        $ville = $repo->find($id);
        $lieuTab = $ville->getLieu();
        $tab=[];

        foreach ($lieuTab as $val){
            array_push($tab,array("id"=>$val->getId(),"nom"=>$val->getNomLieu()));
        }
        return $this->json(json_encode($tab));
    }

    /**
     * @Route("/sortie/lieu/cp/{id}", name="cp")
     */
    public function afficherCP(VilleRepository $repo, $id):Response{
        $ville = $repo->find($id);
        return $this->json('{"codePostal":"'.$ville->getCodePostal().'"}');
    }

    /**
     * @Route ("/sortie/update/{id}",name="update")
     */
    public function update(Request $request,$id){
        return $this->render('sortie/newSortie.html.twig');
    }
    /**
     * @Route ("/sortie/delete/{id}",name="delete")
     */
    public function delete(Request $request, EntityManagerInterface $em, $id){

        //Recherche les données
        $sortie = $em->getRepository(Sortie::class)->find($id);
        $userCourant = $this->getUser();
        $DeleteSortieFom = $this->createForm(DeleteSortieForm::class,$sortie);
        $DeleteSortieFom->handleRequest($request);

        //Traite le formulaire
        if ($DeleteSortieFom->isSubmitted() && $DeleteSortieFom->isValid()) {
            //Test si le user est organisateur de la sortie
            if ($userCourant == null || $userCourant->getId() != $sortie->getOrganisateur()->getId()) {
                $this->addFlash('warning', "Vous n'êtes pas l'organisateur de la sortie.");
                return $this->redirectToRoute("main");
            } else {

                //Mettre l'état de la sortie en annulée
                $etatAnnule = $em->getRepository(Etat::class)->findOneBy(['libelle' => 'annulée']);
                $sortie->setEtat($etatAnnule);
                $em->persist($sortie);
                $em->flush();
            }
        }
        return $this->render('sortie/deleteSortie.html.twig',[
            'deleteSortieForm'=>$DeleteSortieFom->createView()
        ]);
        }
}
