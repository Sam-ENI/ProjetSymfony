<?php

namespace App\Controller;


use App\Entity\Participant;
use App\Entity\Rejoindre;
use App\Entity\Site;
use App\Entity\Sortie;


use App\Form\SortieFormType;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main")
     */
    public function index(Request $request, SortieRepository $sr): Response
    {
        $sorties = $this->getDoctrine()->getRepository(Sortie::class)->findBy([], ['dateHeureDebut' => 'asc']);
        $sites = $this->getDoctrine()->getRepository(Site::class)->findAll();
        $rejoindre = $this->getDoctrine()->getRepository(Rejoindre::class)->findAll();

        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'sorties' => $sorties,
            'sites' => $sites,
            "rejoindre" => $rejoindre
        ]);
    }

    /**
     * @Route("/index/selectSite/{id}", name="selectSite")
     */
    public function select(Request $request, SortieRepository $sr, $id): Response
    {
        $sorties = $this->getDoctrine()->getRepository(Sortie::class)->findAll();
        $rejoindre = $this->getDoctrine()->getRepository(Rejoindre::class)->findAll();
        $sortieList = $sr->findBy(["site" => $id]);
        /*   dd($sortieList);*/
        $tab = [];
        foreach ($sortieList as $val) {
            array_push($tab, [
                "sortie_id" => $val->getId(),
                "nomSortie" => $val->getNomSortie(),
                "dateHeureDebut" => $val->getDateHeureDebut(),
                "dateLimiteInscription" => $val->getDateLimiteInscription(),
                "nbInscriptionMax" => $val->getNbInscriptionMax(),
                "etat" => $val->getEtat()->getLibelle(),
                "etatId" => $val->getEtat()->getId(),
                "organisateur" => $val->getOrganisateur()->getNom(),
                "current_user_role" => $this->getUser()->getRoles(),
                'sorties' => $sorties,
                "rejoindre" => $rejoindre
            ]);
        }
        return $this->json(json_encode($tab));

    }

    /**
     * @Route("/index/selectOrga/{bool}", name="selectOrga")
     */
    public function selectOrga(Request $request, SortieRepository $sr, $bool): Response
    {
        if ($bool == "true") {
            $orgaList = $sr->findBy(["organisateur" => $this->getUser()]);
            /* dd("1er if");*/
            $tab = [];
            foreach ($orgaList as $val) {
                array_push($tab, [
                    "sortie_id" => $val->getId(),
                    "nomSortie" => $val->getNomSortie(),
                    "dateHeureDebut" => $val->getDateHeureDebut(),
                    "dateLimiteInscription" => $val->getDateLimiteInscription(),
                    "nbInscriptionMax" => $val->getNbInscriptionMax(),
                    "etat" => $val->getEtat()->getLibelle(),
                    "etatId" => $val->getEtat()->getId(),
                    "organisateur" => $val->getOrganisateur()->getNom(),
                    "current_user_role" => $this->getUser()->getRoles()
                ]);
            }
            return $this->json(json_encode($tab));
        }
        if ($bool == "false") {

            $orgaList = $sr->findAll();
            /* dd("2eme if");*/
            $tab = [];
            foreach ($orgaList as $val) {
                if ($val->getEtat()->getId() == 2 || $val->getEtat()->getId() == 3 || $val->getEtat()->getId() == 4) {
                    array_push($tab, [

                        "sortie_id" => $val->getId(),
                        "nomSortie" => $val->getNomSortie(),
                        "dateHeureDebut" => $val->getDateHeureDebut(),
                        "dateLimiteInscription" => $val->getDateLimiteInscription(),
                        "nbInscriptionMax" => $val->getNbInscriptionMax(),
                        "etat" => $val->getEtat()->getLibelle(),
                        "etatId" => $val->getEtat()->getId(),
                        "organisateur" => $val->getOrganisateur()->getNom(),
                        "current_user_role" => $this->getUser()->getRoles()
                    ]);
                }
            }
            return $this->json(json_encode($tab));
        }
    }

    /**
     * @Route("/index/selectDactylo/{val}", name="selectDactylo")
     */
    public function selectDactylo(Request $request, SortieRepository $sr, $val = ""): Response
    {

        if (!$val == "") {
            /*dd($val);*/
            $dactyloList = $sr->findByName($val);
            /* dd($dactyloList);*/
            $tab = [];
            foreach ($dactyloList as $val) {
                array_push($tab, [
                    "sortie_id" => $val->getId(),
                    "nomSortie" => $val->getNomSortie(),
                    "dateHeureDebut" => $val->getDateHeureDebut(),
                    "dateLimiteInscription" => $val->getDateLimiteInscription(),
                    "nbInscriptionMax" => $val->getNbInscriptionMax(),
                    "etat" => $val->getEtat()->getLibelle(),
                    "organisateur" => $val->getOrganisateur()->getNom(),
                    "current_user_role" => $this->getUser()->getRoles()
                ]);
            }
            return $this->json(json_encode($tab));
        }

        if ($val == "") {
            /*        dd($val);*/
            $dactyloList = $sr->findAll();
            /*  dd($dactyloList);*/
            $tab = [];
            foreach ($dactyloList as $val) {
                if ($val->getEtat()->getId() == 2 || $val->getEtat()->getId() == 3 || $val->getEtat()->getId() == 4) {
                    array_push($tab, [
                        "sortie_id" => $val->getId(),
                        "nomSortie" => $val->getNomSortie(),
                        "dateHeureDebut" => $val->getDateHeureDebut(),
                        "dateLimiteInscription" => $val->getDateLimiteInscription(),
                        "nbInscriptionMax" => $val->getNbInscriptionMax(),
                        "etat" => $val->getEtat()->getLibelle(),
                        "organisateur" => $val->getOrganisateur()->getNom(),
                        "current_user_role" => $this->getUser()->getRoles()
                    ]);
                }
            }
            return $this->json(json_encode($tab));
        }
    }

    /**
     * @Route("/index/selectPassees/{bool}", name="selectPassees")
     */
    public function selectPassees(Request $request, SortieRepository $sr, $bool): Response
    {
        if ($bool == 'true') {
            $passeesList = $sr->findByFormer();
            /* dd($passeesList);*/
            $tab = [];
            foreach ($passeesList as $val) {
                array_push($tab, [
                    "sortie_id" => $val->getId(),
                    "nomSortie" => $val->getNomSortie(),
                    "dateHeureDebut" => $val->getDateHeureDebut(),
                    "dateLimiteInscription" => $val->getDateLimiteInscription(),
                    "nbInscriptionMax" => $val->getNbInscriptionMax(),
                    "etat" => $val->getEtat()->getLibelle(),
                    "organisateur" => $val->getOrganisateur()->getNom(),
                    "current_user_role" => $this->getUser()->getRoles()
                ]);
            }
            return $this->json(json_encode($tab));
        }
        if ($bool == "false") {

            $passeesList = $sr->findAll();
            /* dd("2eme if");*/
            $tab = [];
            foreach ($passeesList as $val) {
                if ($val->getEtat()->getId() == 2 || $val->getEtat()->getId() == 3 || $val->getEtat()->getId() == 4) {
                    array_push($tab, [
                        "sortie_id" => $val->getId(),
                        "nomSortie" => $val->getNomSortie(),
                        "dateHeureDebut" => $val->getDateHeureDebut(),
                        "dateLimiteInscription" => $val->getDateLimiteInscription(),
                        "nbInscriptionMax" => $val->getNbInscriptionMax(),
                        "etat" => $val->getEtat()->getLibelle(),
                        "organisateur" => $val->getOrganisateur()->getNom(),
                        "current_user_role" => $this->getUser()->getRoles()
                    ]);
                }
            }
            return $this->json(json_encode($tab));
        }


    }

    /**
     * @Route("/index/selectInterDate/{dateMin}/{dateMax}", name="selectInterDate")
     */
    public function selectIntervalDate(Request $request, SortieRepository $sr, $dateMin = "", $dateMax = ""): Response
    {

        $intervalDate = $sr->findByDates($dateMin | date('yyyy-MM-dd'), $dateMax | date('yyyy-MM-dd'));

        $tab = [];
        foreach ($intervalDate as $val) {
            array_push($tab, [
                "sortie_id" => $val->getId(),
                "nomSortie" => $val->getNomSortie(),
                "dateHeureDebut" => $val->getDateHeureDebut(),
                "dateLimiteInscription" => $val->getDateLimiteInscription(),
                "nbInscriptionMax" => $val->getNbInscriptionMax(),
                "etat" => $val->getEtat()->getLibelle(),
                "organisateur" => $val->getOrganisateur()->getNom(),
                "current_user_role" => $this->getUser()->getRoles()
            ]);
        }
        return $this->json(json_encode($tab));
    }


    /**
     * @Route("/index/selectInscrit/{bool}", name="selectInscrits")
     */
    public function selectInscrits(Request $request, SortieRepository $sr, $bool): Response
    {
        $sorties = $this->getDoctrine()->getRepository(Sortie::class)->findAll();
     /*   dd($sorties);*/


        if($bool == 'true'){
        $tab = [];
        foreach ($sorties as $val){
            foreach ($val->getRejoindres() as $rejoindre) {
                $inscritsList = $rejoindre->getSonParticipant()->getId();
              /*  dd($inscritsList);*/
                if ($inscritsList == $this->getUser()->getId()) {
                        array_push($tab, [
                            "sortie_id" => $val->getId(),
                            "nomSortie" => $val->getNomSortie(),
                            "dateHeureDebut" => $val->getDateHeureDebut(),
                            "dateLimiteInscription" => $val->getDateLimiteInscription(),
                            "nbInscriptionMax" => $val->getNbInscriptionMax(),
                            "etat" => $val->getEtat()->getLibelle(),
                            "etatId" => $val->getEtat()->getId(),
                            "inscrits" => 'x',
                            "organisateur" => $val->getOrganisateur()->getNom(),
                            "current_user_role" => $this->getUser()->getRoles()
                        ]);
                    }
                }
            return $this->json(json_encode($tab));
            }
        }
        if($bool == 'false'){

            $sorties = $this->getDoctrine()->getRepository(Sortie::class)->findAll();
            $tab = [];
            foreach ($sorties as $val) {
                if ($val->getEtat()->getId() == 2 || $val->getEtat()->getId() == 3 || $val->getEtat()->getId() == 4) {
                    array_push($tab, [
                        "sortie_id" => $val->getId(),
                        "nomSortie" => $val->getNomSortie(),
                        "dateHeureDebut" => $val->getDateHeureDebut(),
                        "dateLimiteInscription" => $val->getDateLimiteInscription(),
                        "nbInscriptionMax" => $val->getNbInscriptionMax(),
                        "etat" => $val->getEtat()->getLibelle(),
                        "etatId" => $val->getEtat()->getId(),
                        "inscrits" => 'x',
                        "organisateur" => $val->getOrganisateur()->getNom(),
                        "current_user_role" => $this->getUser()->getRoles()
                    ]);
                }
            }
                return $this->json(json_encode($tab));

        }
    }


}
