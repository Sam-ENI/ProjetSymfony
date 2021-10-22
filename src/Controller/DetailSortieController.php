<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Rejoindre;
use App\Entity\Sortie;
use App\Entity\Lieu;
use App\Entity\Ville;
use App\Entity\Site;
use App\Entity\Participant;
use App\Repository\RejoindreRepository;
use Doctrine\ORM\EntityManagerInterface;
use http\Env\Request;
use Monolog\Handler\IFTTTHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DetailSortieController extends AbstractController
{
    /**
     * @Route("sortie/{id}", name="detail_sortie", requirements={"id":"\d+"})
     * @param $id
     * @param EntityManagerInterface $emi
     * @return Response
     */
    public function detailSortie ($id, EntityManagerInterface $emi)
    {
        $sortie = $emi->getRepository(Sortie::class)->find($id);
        $rejoindre = $emi->getRepository(Rejoindre::class)->findAll();

/*    dd($rejoindre);*/
        $lieu = $emi->getRepository(Lieu::class)->find($id);

        $sonParticipant = $emi->getRepository(Rejoindre::class)->find($id);


        if ($sortie ==null){
            throw $this ->createNotFoundException("La sortie est absente de la base de données.");
        }
        return $this->render('detail_sortie/index.html.twig', [
            'infosSortie' => $sortie,
            'infosLieu'=>$lieu,
            'infosParticipant'=>$sonParticipant,
            'rejoindre'=>$rejoindre,
        ]);
    }

    /**
     * @Route ("/rejoindre_sortie/{id}", name="rejoindre_sortie")
     * @param EntityManagerInterface $emi
     * @param Sortie $sortie
     */
    public function rejoindre(EntityManagerInterface $emi, Sortie $sortie){

        $sortieRepo = $this->getDoctrine()->getRepository(Rejoindre::class)->findOneBy(['sonParticipant'=>$this->getUser(), 'saSortie'=>$sortie]);
        $rejoindre = new Rejoindre();
        $rejoindre->setSonParticipant($this->getUser());
        $etatSortie = $sortie->getEtat()->getId();

        // Test si le participant est déjà inscrit
        if ($sortieRepo!==null){
            $this->addFlash('warning', 'Participant déjà inscrit à la sortie');

            return $this->redirectToRoute('main');


        } elseif ($sortie->getNbInscrits() == $sortie->getNbInscriptionMax()){
        // Test si le nombre max est atteint et cloture la sortie

            //Clôture la sortie
            $etatCloturee = $emi -> getRepository(Etat::class)->findOneBy(['id'=>3]);
            $sortie->setEtat($etatCloturee);
            $emi->persist($sortie);
            $emi->flush();
            //dd($sortie);
            $this->addFlash('alert', "Nombre maximum d'inscriptions atteint");

            return $this->redirectToRoute('main');

        }

        if ($etatSortie !==  2){
        //Test si l'état de la sortie est publiée
            $this->addFlash('warning', "Inscription impossible. La sortie n'est plus disponible.");

            return $this->redirectToRoute('main');


        }

        $sortie->setNbInscrits($sortie->getNbInscrits()+1);
        $rejoindre->setSaSortie($sortie);
        $rejoindre->setDateInscription(new \DateTime());

        //sauvegarder les données en base
        $emi->persist($rejoindre);
        $emi->flush();

        $this->addFlash('success', 'Participant inscrit à la sortie');

        return $this->redirectToRoute('main');

    }

    /**
     * @Route ("/desister_sortie/{id}", name="desister_sortie")
     * @param EntityManagerInterface $emi
     * @param Sortie $sortie
     */
    public function desister(EntityManagerInterface $emi,Sortie $sortie,  $id, RejoindreRepository $rr)
    {
        $rejoindre = $rr->findOneBy(['sonParticipant' => $this->getUser()->getId(), 'saSortie' => $id]);

        //récupérer la sortie en base de données et...
        /*$sortieRepo = $this->getDoctrine()->getRepository(Rejoindre::class)->findOneBy();*/
/*        dd($sortieRepo);*/
        // Retire un participant en base de données et change l'état de la sortie en publié


            $sortie->setNbInscrits($sortie->getNbInscrits() - 1);

            /*if ($sortie->getNbInscrits() < $sortie->getNbInscriptionMax()) {
                $etatOuverte = $emi->getRepository(Etat::class)->findOneBy(['libelle' => 2]);
                $sortie->setEtat($etatOuverte);

                return $this->redirectToRoute('main');

            }*/


            //l'annuler en base de données
            $emi->remove($rejoindre);
            $emi->flush();

            $this->addFlash('success', 'Vous vous êtes désinscrit de la sortie');

            return $this->redirectToRoute('main');
        }


}



