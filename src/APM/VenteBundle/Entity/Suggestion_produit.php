<?php

namespace APM\VenteBundle\Entity;


use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\TradeAbstraction\Trade;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Suggestion_produit
 *
 * @ORM\Table(name="suggestion_produit")
 * @ORM\Entity(repositoryClass="APM\VenteBundle\Repository\Suggestion_produitRepository")
 * @UniqueEntity("code")
 */
class Suggestion_produit extends Trade
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, nullable=false)
     */
    private $code;

    /**
     * @var Utilisateur_avm
     * @ORM\ManyToOne(targetEntity="APM\UserBundle\Entity\Utilisateur_avm", inversedBy="suggerantSuggestions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="suggerant_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $suggerant;

    /**
     * @var Utilisateur_avm
     * @ORM\ManyToOne(targetEntity="APM\UserBundle\Entity\Utilisateur_avm", inversedBy="destinataireSuggestions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="destinataire_id", referencedColumnName="id")
     * })
     */
    private $destinataire;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="APM\VenteBundle\Entity\Offre", mappedBy="suggestion")
     */
    private $produits;


    /**
     * Constructor
     * @param string $var
     */
    public function __construct($var)
    {
        $this->produits = new ArrayCollection();
        $this->code = "SG" . $var;
    }

    /**
     * Get Suggestion_produitid
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Get suggerant
     *
     * @return Utilisateur_avm
     */
    public function getSuggerant()
    {
        return $this->suggerant;
    }

    /**
     * Set Suggerant
     *
     * @param Utilisateur_avm $suggerant
     *
     * @return Suggestion_produit
     */
    public function setSuggerant(Utilisateur_avm $suggerant)
    {
        $this->suggerant = $suggerant;

        return $this;
    }

    /**
     * Add produit
     *
     * @param Offre $produit
     *
     * @return Suggestion_produit
     */
    public function addProduit(Offre $produit)
    {
        $this->produits[] = $produit;

        return $this;
    }

    /**
     * Remove produit
     *
     * @param Offre $produit
     */
    public function removeProduit(Offre $produit)
    {
        $this->produits->removeElement($produit);
    }

    /**
     * Get produits
     *
     * @return Collection
     */
    public function getProduits()
    {
        return $this->produits;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Suggestion_produit
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get destinataire
     *
     * @return Utilisateur_avm
     */
    public function getDestinataire()
    {
        return $this->destinataire;
    }

    /**
     * Set destinataire
     *
     * @param Utilisateur_avm $destinataire
     *
     * @return Suggestion_produit
     */
    public function setDestinataire(Utilisateur_avm $destinataire = null)
    {
        $this->destinataire = $destinataire;

        return $this;
    }
}
