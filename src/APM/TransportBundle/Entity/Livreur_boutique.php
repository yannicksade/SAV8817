<?php

namespace APM\TransportBundle\Entity;


use APM\TransportBundle\Factory\TradeFactory;
use APM\VenteBundle\Entity\Boutique;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Livreur_boutique
 * @ORM\Table(name="livreur_boutique")
 * @ORM\Entity(repositoryClass="APM\TransportBundle\Repository\Livreur_boutiqueRepository")
 */
class Livreur_boutique extends TradeFactory
{


    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    private $id;

    /**
     * @var \DateTime
     * @Assert\DateTime
     * @ORM\Column(name="dateEnregistrement", type="datetime", nullable=false)
     */
    private $dateEnregistrement;

    /**
     * @var string
     * @Assert\Length(min=2, max=254)
     * @ORM\Column(name="reference", type="string", length=255, nullable=false)
     */
    private $reference;

    /**
     * @var Profile_transporteur
     *
     * @ORM\OneToOne(targetEntity="APM\TransportBundle\Entity\Profile_transporteur", inversedBy="livreurBoutique", cascade={"persist","remove"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="transporteur_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $transporteur;

    /**
     * @var Boutique
     *
     * @ORM\ManyToOne(targetEntity="APM\VenteBundle\Entity\Boutique", inversedBy="livreurBoutiques")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="boutique_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $boutiqueProprietaire;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="APM\VenteBundle\Entity\Boutique", inversedBy="livreurs")
     *
     */
    private $boutiques;

    public function __construct()
    {
        $this->dateEnregistrement = new \DateTime('now');
        $this->boutiques = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return Profile_transporteur
     */
    public function getId()
    {
        return $this->id;
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
     * @return Livreur_boutique
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Add boutique
     *
     * @param Boutique $boutique
     *
     * @return Livreur_boutique
     */
    public function addBoutique(Boutique $boutique)
    {
        $this->boutiques[] = $boutique;

        return $this;
    }

    /**
     * Remove boutique
     *
     * @param Boutique $boutique
     */
    public function removeBoutique(Boutique $boutique)
    {
        $this->boutiques->removeElement($boutique);
    }

    /**
     * Get boutiques
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBoutiques()
    {
        return $this->boutiques;
    }

    public function __toString()
    {
        return $this->transporteur->getMatricule();
    }

    /**
     * Get boutiqueProprietaire
     *
     * @return Boutique
     */
    public function getBoutiqueProprietaire()
    {
        return $this->boutiqueProprietaire;
    }

    /**
     * Set boutiqueProprietaire
     *
     * @param Boutique $boutiqueProprietaire
     *
     * @return Livreur_boutique
     */
    public function setBoutiqueProprietaire(Boutique $boutiqueProprietaire = null)
    {
        $this->boutiqueProprietaire = $boutiqueProprietaire;

        return $this;
    }

    /**
     * Set transporteur
     *
     * @param \APM\TransportBundle\Entity\Profile_transporteur $transporteur
     *
     * @return Livreur_boutique
     */
    public function setTransporteur(\APM\TransportBundle\Entity\Profile_transporteur $transporteur = null)
    {
        $this->transporteur = $transporteur;

        return $this;
    }

    /**
     * Get transporteur
     *
     * @return \APM\TransportBundle\Entity\Profile_transporteur
     */
    public function getTransporteur()
    {
        return $this->transporteur;
    }

    /**
     * Set dateEnregistrement
     *
     * @param \DateTime $dateEnregistrement
     *
     * @return Livreur_boutique
     */
    public function setDateEnregistrement($dateEnregistrement)
    {
        $this->dateEnregistrement = $dateEnregistrement;

        return $this;
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
}
