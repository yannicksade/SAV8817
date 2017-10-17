<?php

namespace APM\TransportBundle\Entity;

use APM\TransportBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Boutique;
use APM\VenteBundle\Entity\Transaction;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * Livraison
 *
 * @ORM\Table(name="livraison")
 * @ORM\Entity(repositoryClass="APM\TransportBundle\Repository\LivraisonRepository")
 * @UniqueEntity("code")
 */
class Livraison extends TradeFactory
{
    /**
     * @var string
     * @ORM\Column(name="code", type="string", length=255, nullable=false )
     */
    private $code;

    /**
     * @var \DateTime
     * @Assert\DateTime
     * @ORM\Column(name="dateEnregistrement", type="datetime", nullable=false)
     */
    private $dateEnregistrement;

    /**
     * @var \DateTime
     * @Assert\DateTime
     * @ORM\Column(name="dateHeureLivraison", type="datetime", nullable=true)
     */
    private $dateEtHeureLivraison;

    /**
     * @var string
     * @Assert\Length(min=2, max=254)
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var integer
     * @ORM\Column(name="etatLivraison", type="integer",nullable=true)
     */
    private $etatLivraison;

    /**
     * @var string
     * @ORM\Column(name="priorite", type="string", length=255, nullable=true)
     */
    private $priorite;

    /**
     * @var boolean
     * @ORM\Column(name="valide", type="boolean", nullable=true)
     */
    private $valide;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Profile_transporteur
     *
     * @ORM\ManyToOne(targetEntity="APM\TransportBundle\Entity\Profile_transporteur", inversedBy="livraisons")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="livreur_boutique_id", referencedColumnName="id")
     * })
     */
    private $livreur;

    /**
     * @var Utilisateur_avm
     *
     * @ORM\ManyToOne(targetEntity="APM\UserBundle\Entity\Utilisateur_avm", inversedBy="livraisons")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $utilisateur;

    /**
     * @var Boutique
     *
     * @ORM\ManyToOne(targetEntity="APM\VenteBundle\Entity\Boutique", inversedBy="livraisons")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="boutique_id", referencedColumnName="id")
     * })
     */
    private $boutique;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="APM\VenteBundle\Entity\Transaction", mappedBy="livraison")
     */
    private $operations;

    /**
     * Constructor
     * @param string $var
     */
    public function __construct($var)
    {
        $this->operations = new ArrayCollection();
        $this->code = "LV" . $var;
        $this->dateEnregistrement = new \DateTime('now');
    }

    /**
     * Get dateEtHeureLivraison
     *
     * @return \DateTime
     */
    public function getDateEtHeureLivraison()
    {
        return $this->dateEtHeureLivraison;
    }

    /**
     * Set dateEtHeureLivraison
     *
     * @param \DateTime $dateEtHeureLivraison
     *
     * @return Livraison
     */
    public function setDateEtHeureLivraison($dateEtHeureLivraison)
    {
        $this->dateEtHeureLivraison = $dateEtHeureLivraison;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Livraison
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get etatLivraison
     *
     * @return integer
     */
    public function getEtatLivraison()
    {
        return $this->etatLivraison;
    }

    /**
     * Set etatLivraison
     *
     * @param integer $etatLivraison
     *
     * @return Livraison
     */
    public function setEtatLivraison($etatLivraison)
    {
        $this->etatLivraison = $etatLivraison;

        return $this;
    }

    /**
     * Get priorite
     *
     * @return integer
     */
    public function getPriorite()
    {
        return $this->priorite;
    }

    /**
     * Set priorite
     *
     * @param integer $priorite
     *
     * @return Livraison
     */
    public function setPriorite($priorite)
    {
        $this->priorite = $priorite;

        return $this;
    }

    /**
     * Get estValide
     *
     * @return boolean
     */
    public function isValide()
    {
        return $this->valide;
    }

    /**
     * Get valide
     *
     * @return boolean
     */
    public function getValide()
    {
        return $this->valide;
    }

    /**
     * Set estValide
     *
     * @param boolean $estValide
     *
     * @return Livraison
     */
    public function setValide($estValide)
    {
        $this->valide = $estValide;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add operation
     *
     * @param Transaction $operation
     *
     * @return Livraison
     */
    public function addOperation(Transaction $operation)
    {
        $this->operations[] = $operation;

        return $this;
    }

    /**
     * Remove operation
     *
     * @param Transaction $operation
     */
    public function removeOperation(Transaction $operation)
    {
        $this->operations->removeElement($operation);
    }

    /**
     * Get operations
     *
     * @return Collection
     */
    public function getOperations()
    {
        return $this->operations;
    }

    /**
     * Get livreur
     *
     * @return Profile_transporteur
     */
    public function getLivreur()
    {
        return $this->livreur;
    }

    /**
     * Set livreur
     *
     * @param Profile_transporteur $livreur
     *
     * @return Livraison
     */
    public function setLivreur(Profile_transporteur $livreur)
    {
        $this->livreur = $livreur;

        return $this;
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
     * @return Livraison
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get utilisateur
     *
     * @return Utilisateur_avm
     */
    public function getUtilisateur()
    {
        return $this->utilisateur;
    }

    /**
     * Set utilisateur
     *
     * @param Utilisateur_avm $utilisateur
     *
     * @return Livraison
     */
    public function setUtilisateur(Utilisateur_avm $utilisateur)
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * Get boutique
     *
     * @return \APM\VenteBundle\Entity\Boutique
     */
    public function getBoutique()
    {
        return $this->boutique;
    }

    /**
     * Set boutique
     *
     * @param Boutique $boutique
     *
     * @return Livraison
     */
    public function setBoutique(Boutique $boutique = null)
    {
        $this->boutique = $boutique;

        return $this;
    }

    public function __toString()
    {
        return $this->code;
    }

    /**
     * Get dateEnregistrement
     *
     * @return \DateTime
     */
    public function getDateEnregistrement()
    {
        return $this->dateEnregistrement;
    }

    /**
     * Set dateEnregistrement
     *
     * @param \DateTime $dateEnregistrement
     *
     * @return Livraison
     */
    public function setDateEnregistrement($dateEnregistrement)
    {
        $this->dateEnregistrement = $dateEnregistrement;

        return $this;
    }
}
