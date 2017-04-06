<?php

namespace APM\VenteBundle\Entity;

use APM\VenteBundle\Factory\TradeFactory;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Transaction_produit
 *
 * @ORM\Table(name="Transaction_produit")
 * @ORM\Entity(repositoryClass="APM\VenteBundle\Repository\Transaction_produitRepository")
 *
 */
class Transaction_produit extends TradeFactory
{

    /**
     * @var integer
     *
     * @ORM\Column(name="quantite", type="integer", nullable=true)
     */
    private $quantite;


    /**
     * @var string
     * @ORM\Column(name="reference", type="string", length=255, nullable=true)
     */
    private $reference;
    /**
     * Id
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Offre
     * @ORM\ManyToOne(targetEntity="APM\VenteBundle\Entity\Offre", inversedBy="produitTransactions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="produit_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $produit;

    /**
     * @var Transaction
     * @ORM\ManyToOne(targetEntity="APM\VenteBundle\Entity\Transaction" , inversedBy="transactionProduits", cascade={"persist","remove"})
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
}
