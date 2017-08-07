<?php

namespace APM\VenteBundle\Entity;

use APM\TransportBundle\Entity\Livraison;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Boutique;
use APM\VenteBundle\Factory\TradeFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Transaction
 *
 * @ORM\Table(name="Transaction")
 * @ORM\Entity(repositoryClass="APM\VenteBundle\Repository\TransactionRepository")
 * @UniqueEntity("code")
 */
class Transaction extends TradeFactory
{
    /**
     * @var \DateTime
     * @Assert\DateTime
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, nullable=false)
     */
    private $code;

    /**
     * @var string
     * @Assert\Length(min=2, max=254)
     * @ORM\Column(name="destinataireNonAVM", type="string", length=255, nullable=true)
     */
    private $destinataireNonAvm;

    /**
     * @var string
     *
     * @ORM\Column(name="montant", type="decimal", nullable=true)
     */
    private $montant;

    /**
     * @var string
     * @Assert\Choice({0,1,2,3,4})
     * @ORM\Column(name="nature", type="string", length=255, nullable=true)
     */
    private $nature;

    /**
     * @var boolean
     * @ORM\Column(name="is_shipped", type="boolean", nullable=true)
     *
     */
    private $shipped;

    /**
     * @var integer
     * @Assert\Choice({0,1,2,3,4,5})
     * @ORM\Column(name="statut", type="integer", nullable=true)
     */
    private $statut;

    /**
     * Id
     *
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Utilisateur_avm
     * @ORM\ManyToOne(targetEntity="APM\UserBundle\Entity\Utilisateur_avm", inversedBy="transactionsEffectues")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="auteur_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $auteur;

    /**
     * @var Utilisateur_avm
     * @ORM\ManyToOne(targetEntity="APM\UserBundle\Entity\Utilisateur_avm", inversedBy="transactionsRecues")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="beneficiaire_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $beneficiaire;

    /**
     * @var Boutique
     * @ORM\ManyToOne(targetEntity="APM\VenteBundle\Entity\Boutique", inversedBy="transactionsRecues")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="boutiqueBeneficiaire_id", referencedColumnName="id")
     * })
     */
    private $boutiqueBeneficiaire;

    /**
     * @var Livraison
     * @ORM\ManyToOne(targetEntity="APM\TransportBundle\Entity\Livraison", inversedBy="operations")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="livraison_id", referencedColumnName="id")
     * })
     */
    private $livraison;


    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="APM\VenteBundle\Entity\Transaction_produit", mappedBy="transaction")
     *
     */
    private $transactionProduits;

    /**
     * @var Boutique
     * @ORM\ManyToOne(targetEntity="APM\VenteBundle\Entity\Boutique", inversedBy="transactions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="boutique_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $boutique;

    /**
     * Transaction constructor.
     * @param string $var
     */
    public function __construct($var)
    {
        $this->transactionProduits = new ArrayCollection();
        $this->date = new \DateTime();
        $this->code = "TX" . $var;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Transaction
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get destinataireNonAvm
     *
     * @return string
     */
    public function getDestinataireNonAvm()
    {
        return $this->destinataireNonAvm;
    }

    /**
     * Set destinataireNonAvm
     *
     * @param string $destinataireNonAvm
     *
     * @return Transaction
     */
    public function setDestinataireNonAvm($destinataireNonAvm)
    {
        $this->destinataireNonAvm = $destinataireNonAvm;

        return $this;
    }

    /**
     * Get montant
     *
     * @return string
     */
    public function getMontant()
    {
        return $this->montant;
    }

    /**
     * Set montant
     *
     * @param string $montant
     *
     * @return Transaction
     */
    public function setMontant($montant)
    {
        $this->montant = $montant;

        return $this;
    }

    /**
     * Get nature
     *
     * @return string
     */
    public function getNature()
    {
        return $this->nature;
    }

    /**
     * Set nature
     *
     * @param string $nature
     *
     * @return Transaction
     */
    public function setNature($nature)
    {
        $this->nature = $nature;

        return $this;
    }

    /**
     * Get statut
     *
     * @return string
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * Set statut
     *
     * @param string $statut
     *
     * @return Transaction
     */
    public function setStatut($statut)
    {
        $this->statut = $statut;

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
     * Get auteur
     *
     * @return Utilisateur_avm
     */
    public function getAuteur()
    {
        return $this->auteur;
    }

    /**
     * Set auteur
     *
     * @param Utilisateur_avm $auteur
     *
     * @return Transaction
     */
    public function setAuteur(Utilisateur_avm $auteur)
    {
        $this->auteur = $auteur;

        return $this;
    }

    /**
     * Get livraison
     *
     * @return Livraison
     */
    public function getLivraison()
    {
        return $this->livraison;
    }

    /**
     * Set livraison
     *
     * @param Livraison $livraison
     *
     * @return Transaction
     */
    public function setLivraison(Livraison $livraison)
    {
        $this->livraison = $livraison;

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
     * @return Transaction
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Add transactionProduit
     *
     * @param Transaction_produit $transactionProduit
     *
     * @return Transaction
     */
    public function addTransactionProduit(Transaction_produit $transactionProduit)
    {
        $this->transactionProduits[] = $transactionProduit;

        return $this;
    }

    /**
     * Remove transactionProduit
     *
     * @param Transaction_produit $transactionProduit
     */
    public function removeTransactionProduit(Transaction_produit $transactionProduit)
    {
        $this->transactionProduits->removeElement($transactionProduit);
    }

    /**
     * Get transactionProduits
     *
     * @return Collection
     */
    public function getTransactionProduits()
    {
        return $this->transactionProduits;
    }

    /**
     * Get boutique
     *
     * @return Boutique
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
     * @return Transaction
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
     * Get beneficiaire
     *
     * @return Utilisateur_avm
     */
    public function getBeneficiaire()
    {
        return $this->beneficiaire;
    }

    /**
     * Set beneficiaire
     *
     * @param Utilisateur_avm $beneficiaire
     *
     * @return Transaction
     */
    public function setBeneficiaire(Utilisateur_avm $beneficiaire)
    {
        $this->beneficiaire = $beneficiaire;

        return $this;
    }

    /**
     * Get boutiqueBeneficiaire
     *
     * @return Boutique
     */
    public function getBoutiqueBeneficiaire()
    {
        return $this->boutiqueBeneficiaire;
    }

    /**
     * Set boutiqueBeneficiaire
     *
     * @param Boutique $boutiqueBeneficiaire
     *
     * @return Transaction
     */
    public function setBoutiqueBeneficiaire(Boutique $boutiqueBeneficiaire = null)
    {
        $this->boutiqueBeneficiaire = $boutiqueBeneficiaire;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isShipped()
    {
        return $this->shipped;
    }

    /**
     * @param boolean $shipped
     */
    public function setShipped(bool $shipped)
    {
        $this->shipped = $shipped;
    }

    /**
     * Get shipped
     *
     * @return boolean
     */
    public function getShipped()
    {
        return $this->shipped;
    }
}
