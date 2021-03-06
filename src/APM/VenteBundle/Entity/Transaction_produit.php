<?php

namespace APM\VenteBundle\Entity;

use APM\VenteBundle\Factory\TradeFactory;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;

/**
 * Transaction_produit
 *
 * @ORM\Table(name="Transaction_produit")
 * @ORM\Entity(repositoryClass="APM\VenteBundle\Repository\Transaction_produitRepository")
 * @ExclusionPolicy("all")
 */
class Transaction_produit extends TradeFactory
{

    /**
     * @var integer
     * @Expose
     * @Groups({"owner_transactionP_details", "others_transactionP_details"})
     * @ORM\Column(name="quantite", type="integer", nullable=true)
     */
    private $quantite;

    /**
     * @var \DateTime
     * @Expose
     * @Groups({"owner_transactionP_details", "others_transactionP_details"})
     * @Assert\DateTime
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $dateInsertion;

    /**
     * @var string
     * @Expose
     * @Groups({"owner_transactionP_details", "others_transactionP_details"})
     * @ORM\Column(name="reference", type="string", length=255, nullable=true)
     */
    private $reference;
    /**
     * Id
     * @var integer
     * @Expose
     * @Groups({"owner_list", "others_list", "owner_transactionP_details", "others_transactionP_details"})
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Offre
     * @Expose
     * @Groups({"owner_list", "others_list", "owner_transactionP_details", "others_transactionP_details"})
     * @ORM\ManyToOne(targetEntity="APM\VenteBundle\Entity\Offre", inversedBy="produitTransactions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="produit_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $produit;

    /**
     * @var Transaction
     * @Expose
     * @Groups({"owner_transactionP_details", "others_transactionP_details"})
     * @ORM\ManyToOne(targetEntity="APM\VenteBundle\Entity\Transaction" , inversedBy="transactionProduits")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="transaction_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $transaction;

    /**
     * Transaction_produit constructor.
     *
     */
    function __construct()
    {
        $this->dateInsertion = new \DateTime('now');
    }

    /**
     * Get quantite
     *
     * @return integer
     */
    public function getQuantite()
    {
        return $this->quantite;
    }

    /**
     * Set quantite
     *
     * @param integer $quantite
     *
     * @return Transaction_produit
     */
    public function setQuantite($quantite)
    {
        $this->quantite = $quantite;

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
     * Get produit
     *
     * @return Offre
     */
    public function getProduit()
    {
        return $this->produit;
    }

    /**
     * Set produit
     *
     * @param Offre $produit
     *
     * @return Transaction_produit
     */
    public function setProduit(Offre $produit)
    {
        $this->produit = $produit;

        return $this;
    }

    /**
     * Get transaction
     *
     * @return Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * Set transaction
     *
     * @param Transaction $transaction
     *
     * @return Transaction_produit
     */
    public function setTransaction(Transaction $transaction = null)
    {
        $this->transaction = $transaction;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set reference
     *
     * @param string $reference
     *
     * @return Transaction_produit
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get dateInsertion
     *
     * @return \DateTime
     */
    public function getDateInsertion()
    {
        return $this->dateInsertion;
    }

    /**
     * Set dateInsertion
     *
     * @param \DateTime $dateInsertion
     *
     * @return Transaction_produit
     */
    public function setDateInsertion($dateInsertion)
    {
        $this->dateInsertion = $dateInsertion;

        return $this;
    }
}
