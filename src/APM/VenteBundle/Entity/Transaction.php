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
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;

/**
 * Transaction
 *
 * @ORM\Table(name="Transaction")
 * @ORM\Entity(repositoryClass="APM\VenteBundle\Repository\TransactionRepository")
 * @UniqueEntity("code")
 * @ExclusionPolicy("all")
 */
class Transaction extends TradeFactory
{
    /**
     * @var \DateTime
     * @Expose
     * @Groups({"owner_transaction_details", "others_transaction_details"})
     * @Assert\DateTime
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;

    /**
     * @var string
     * @Expose
     * @Groups({"owner_list", "owner_transaction_details", "others_transaction_details"})
     * @ORM\Column(name="code", type="string", length=255, nullable=false)
     */
    private $code;

    /**
     * @var string
     * @Expose
     * @Groups({"owner_transaction_details", "others_transaction_details"})
     * @Assert\Length(min=2, max=254)
     * @ORM\Column(name="destinataireNonAVM", type="string", length=255, nullable=true)
     */
    private $destinataireNonAvm;

    /**
     * @var string
     * @Groups({"owner_transaction_details", "others_transaction_details"})
     * @ORM\Column(name="montant", type="decimal", nullable=true)
     */
    private $montant;

    /**
     * @var integer
     * @Expose
     * @Groups({"owner_transaction_details", "others_transaction_details"})
     * @ORM\Column(name="nature", type="integer", length=255, nullable=true)
     */
    private $nature;

    /**
     * @var string
     * @Expose
     * @Groups({"owner_list", "others_list", "owner_transaction_details", "others_transaction_details"})
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var boolean
     * @Expose
     * @Groups({"owner_transaction_details", "others_transaction_details"})
     * @ORM\Column(name="is_shipped", type="boolean", nullable=true)
     *
     */
    private $shipped;

    /**
     * @var integer
     * @Expose
     * @Groups({"owner_transaction_details", "others_transaction_details"})
     * @ORM\Column(name="statut", type="integer", nullable=true)
     */
    private $statut;

    /**
     * Id
     * @var integer
     * @Expose
     * @Groups({"owner_transaction_details", "others_transaction_details", "owner_list", "others_list"})
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Utilisateur_avm
     * @Expose
     * @Groups({"owner_transaction_details", "others_transaction_details"})
     * @ORM\ManyToOne(targetEntity="APM\UserBundle\Entity\Utilisateur_avm", inversedBy="transactionsEffectues")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="auteur_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $auteur;

    /**
     * @var Utilisateur_avm
     * @Expose
     * @Groups({"owner_transaction_details", "others_transaction_details"})
     * @ORM\ManyToOne(targetEntity="APM\UserBundle\Entity\Utilisateur_avm", inversedBy="transactionsRecues")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="beneficiaire_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $beneficiaire;

    /**
     * @var Boutique
     * @Expose
     * @Groups({"owner_transaction_details", "others_transaction_details"})
     * @ORM\ManyToOne(targetEntity="APM\VenteBundle\Entity\Boutique", inversedBy="transactionsRecues")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="boutiqueBeneficiaire_id", referencedColumnName="id")
     * })
     */
    private $boutiqueBeneficiaire;

    /**
     * @var Livraison
     * @Expose
     * @Groups({"owner_transaction_details"})
     * @ORM\ManyToOne(targetEntity="APM\TransportBundle\Entity\Livraison", inversedBy="operations")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="livraison_id", referencedColumnName="id")
     * })
     */
    private $livraison;


    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="APM\VenteBundle\Entity\Transaction_produit", mappedBy="transaction", cascade={"persist", "remove"})
     *
     */
    private $transactionProduits;

    /**
     * @var Boutique
     * @Expose
     * @Groups({"owner_transaction_details", "others_transaction_details"})
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
        $this->date = new \DateTime('now');
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
     * @return int
     */
    public function getNature()
    {
        return $this->nature;
    }

    /**
     * Set nature
     *
     * @param integer $nature
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
     * @return integer
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
     * Get shipped
     *
     * @return boolean
     */
    public function getShipped()
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
     * @return Transaction
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }
}
