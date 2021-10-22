<?php


namespace App\Service;


use App\Entity\Etat;
use App\Entity\Sortie;
use Doctrine\Persistence\ManagerRegistry;

class EtatManager
{

    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine =$doctrine;
    }

    /**
     * Retourne un OBJET Libelle en fonction de son nom
     *
     * @param string $libelle
     * @return object|null
     */
    public function getEtatByLibelle (string $libelle){

        $etatRepo = $this->doctrine->getRepository(Etat::class);
        $etat = $etatRepo->findOneBy(['libelle' => $libelle]);

        return $etat;
    }

    /**
     *
     * Change l'état en base de donnée
     *
     * @param Sortie $sortie
     * @param string $newEtatLibelle
     */
    public function changeLibelleEtat (Sortie $sortie, string $newEtatLibelle){

        $newEtat = $this->getEtatByLibelle($newEtatLibelle);

        $sortie->setEtat($newEtat);

        $em = $this ->doctrine->getManager();
        $em ->persist($sortie);
        $em ->flush();

    }

    /**
     *
     * Retourne un booléen si la sortie doit être historisée
     *
     * @param Sortie $sortie
     * @return bool
     */
    public function doitEtreHistorisee (Sortie $sortie):bool
    {
        $oneMonthAgo = new \DateTime("-1 month");

        if (
            $sortie->getDateLimiteInscription() < $oneMonthAgo
            && $sortie->getEtat()->getLibelle() !== "historisée"

        ) {

            return true;

        }

        return false;

    }

    /**
     *
     * Retourne un booléen si la sortie doit être historisée
     *
     * @param Sortie $sortie
     * @return bool
     */
    public function doitEtreEnCours (Sortie $sortie) : bool
    {
        $now = new \DateTime();
        if(
            $sortie->getEtat()->getLibelle() === "clôturée" &&
            $sortie->getDateHeureDebut() < $now &&
            $sortie->getEndDate() > $now &&
            $sortie->getEtat()->getLibelle() !== "en cours"

        ){

            return true;

        }

        return false;

    }

    /**
     *
     * Retourne un booléen si la sortie doit être clôturée
     *
     * @param Sortie $sortie
     * @return bool
     */
    public function doitEtreCloturee (Sortie $sortie):bool{

        $now = new \DateTime();
        $oneMonthAgo = new \DateTime("-1 month");

        if(
            $sortie->getEtat()->getLibelle() === "ouverte" &&
            $sortie->getDateLimiteInscription() <= $now &&
            $sortie->getDateHeureDebut() > $now &&
            $sortie->getEtat()->getLibelle() !== "clôturée"
        ){

            echo $sortie->getDateLimiteInscription()->format("Y-m-d H:i"). "<=" . $now->format("Y-m-d H:i")."\r\n";
            echo "clôturée";
            return true;
        }

        return false;
    }

    /**
     *
     * Retourne true si la sortie peut être publiée
     *
     * @param Sortie $sortie
     * @return bool
     */
    public function peutEtrePubliee (Sortie $sortie){
        //Doit être en statut créée pour pouvoir retourner true
        return $sortie->getEtat()->getLibelle()==="créée";
    }

    /**
     *
     * Retourne true si la sortie peut être annulée
     *
     * @param Sortie $sortie
     * @return bool
     */
    public function peutEtreAnnulee (Sortie $sortie):bool{
        //doit être en statut ouvert ou clôturer pour retourner true
        return $sortie->getEtat()->getLibelle()==="créée" || $sortie->getEtat()->getLibelle() ==="clôturée";

    }

    /**
     *
     * devine l'état d'une sortie, utile pour les fixtures
     *
     * @param Sortie $sortie
     * @return string
     */
    public function devineEtatSortie (Sortie $sortie):string
    {
        $now = new \DateTime();
        $oneMonthAgo = new \DateTime("-1 month");

        if($sortie->getEndDate() < $oneMonthAgo){

            return "historisée";

        }

        if($sortie->getEndDate() >= $oneMonthAgo && $sortie->getEndDate()<=$now){

            return "terminée";

        }

        if($sortie->getDateHeureDebut() <= $now && $sortie->getEndDate() >$now){

            return "en cours";

        }

        if($sortie->getDateLimiteInscription() <= $now && $sortie->getDateHeureDebut() > $now){

            return "clôturée";

        }

        if($sortie->getDateHeureDebut() > $now && $sortie->getDateLimiteInscription() > $now){

            return "ouverte";

        }

        return "créée";

    }

}