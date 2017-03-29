<?php

namespace APM\VenteBundle\Entity;

use APM\VenteBundle\TradeAbstraction\Trade;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Transaction_produit
 *
 * @ORM\Table(name="Transaction_produit")
 * @ORM\Entity(repositoryClass="APM\VenteBundle\Repository\Transaction_produitRepository")
 *
 */
class Transaction_produit extends Trade
{

    /**
     * @var integer
     *
     * @ORM\Column(name="quantite", type="integer", nullable=true)
     */
    private $quantite;


    /**
     * @var string
     * @Assert\NotBlank
     * @ORM\Column(name="reference", type="string", length=255, nullable=false)
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
     * @var Rabais_offre
     * @ORM\ManyToOne(targetEntity="APM\VenteBundle\Entity\Rabais_offre", inversedBy="transactions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="rabais_id", referencedColumnName="id")
     * })
     */
    private $rabais;

    /**
     * Transaction_produit constructor.
     * @param string $var
     */
    function __construct($var)
    {
        $this->reference = "TP" . $var;
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
     * Get rabais
     *
     * @return Rabais_offre
     */
    public function getRabais()
    {
        return $this->rabais;
    }

    /**
     * Set rabais
     *
     * @param Rabais_offre $rabais
     *
     * @return Transaction_produit
     */
    public function setRabais(Rabais_offre $rabais = null)
    {
        $this->rabais = $rabais;

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
