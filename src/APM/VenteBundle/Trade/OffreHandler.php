<?php
namespace APM\VenteBundle\Trade;

use APM\AchatBundle\Entity\Specification_achat;
use APM\TransportBundle\Entity\Livraison;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\TradeAbstraction\TradeOperationInterface;
use APM\VenteBundle\Entity\Offre;
use Doctrine\Common\Persistence\ObjectManager;


/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 31/01/2017
 * Time: 17:46
 */
class OffreHandler implements TradeOperationInterface
{

    private $handler;

    function __construct(OperationHandler $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @param Offre $offre
     * @param Utilisateur_avm $user
     * Publier une offre revient à la rendre accessible à l'utilisateur designé
     */
    public function publier($offre, $user)
    {
        $user->addUtilisateurOffre($offre);
    }

    /**
     * @param Offre $offre
     * @param Utilisateur_avm $user
     * cette fonction rend l'offre inaccessible à l'utilisateur
     */
    public function depublier($offre, $user)
    {
        $user->removeUtilisateurOffre($offre);
    }

    public function inserer($var1, $var2)
    {
        // TODO: Implement inserer() method.
    }

    public function desinserer($var1, $var2)
    {
        // TODO: Implement desinserer() method.
    }

    /**
     * @param Offre $offre
     * @param Utilisateur_avm $source
     * @param Utilisateur_avm $destinataire
     * @param string $destinataireNonAVM
     * @param string $motif
     * @param $quantite
     * @param decimal $montant
     * Transferer une offre et enregistrer la transaction
     * @param Specification_achat $ordre
     * @param Livraison $livraison
     * @param ObjectManager $manager
     */
    public function transferer($offre, $source, $destinataire, $destinataireNonAVM, $motif, $quantite, $montant, $ordre, $livraison, $manager)
    {
        $offre->setBoutique(null);
        $offre->setCategorie(null);
        $offre->setVendeur($destinataire);

        $this->handler->enregistrerTransaction($offre, $source, $destinataireNonAVM, $motif, $montant, $quantite, $ordre, $livraison, $manager);
    }

}