<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Lieu;
use App\Entity\Ville;
use App\Form\LieuFormType;
use App\Form\SortieFormType;
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
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class SortieController extends AbstractController
{
    /**
     * @Route ("/sortie/insert",name="insert")
     */
    public function insert(Request $request , EntityManagerInterface $em):Response{
        if(!$this->getUser()){
            return $this->redirectToRoute("app_login");
        }
        $sortie = new Sortie();

//        $sortie->setSite( $this->getUser()->getSite());
        $sortieForm = $this->createForm(SortieFormType::class, $sortie);
//        if($request->request->get('sortie_form')){
//            $sortie_form =  $request->request->get('sortie_form');
//            $sortie_form["site"]=$this->getUser()->getSite()->getId();
//            $request->request->set('sortie_form',$sortie_form);
//        }
//        dd($sortie_form);
        $sortieForm->handleRequest($request);

        $lieu = new Lieu();
        $lieuForm = $this->createForm(LieuFormType::class, $lieu);
        $lieuForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid() && $lieuForm->isSubmitted() && $lieuForm->isValid()){
            $em->persist($sortie);
            $em->persist($lieu);
            $em->flush();
            $this->addFlash("success", "Votre sortie est bien enregistrée");

            return $this->redirectToRoute('detail_sortie');
        }


        return $this->render('sortie/newSortie.html.twig',[
            'sortieForm'=>$sortieForm->createView(),
          'lieuForm'=>$lieuForm->createView(),
            "site"=>$this->getUser()->getSite()
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
    public function delete(Request $request,$id){
        return $this->render('sortie/newSortie.html.twig');
    }
}
