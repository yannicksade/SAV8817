<?php

namespace APM\TransportBundle\Entity;


use APM\VenteBundle\Entity\Boutique;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Livreur_boutique
 * @ORM\Table(name="livreur_boutique")
 * @ORM\Entity(repositoryClass="APM\TransportBundle\Repository\Livreur_boutiqueRepository")
 */
class Livreur_boutique
{


    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    private $id;

    /**
     * @var string
     * @Assert\Length(min=2, max=254)
     * @ORM\Column(name="reference", type="string", length=255, nullable=false)
     */
    private $reference;

    /**
     * @var Profile_transporteur
     *
     * @ORM\OneToOne(targetEntity="APM\TransportBundle\Entity\Profile_transporteur", cascade={"persist","remove"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="transporteur_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $transporteur;

    /**
     * @var Boutique
     *
     * @ORM\ManyToOne(targetEntity="APM\VenteBundle\Entity\Boutique", inversedBy="livreurs")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="boutique_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $boutique;

    /**
     * Get Transporteur
     *
     * @return string
     */
    public function getTransporteur()
    {
        return $this->transporteur;
    }

    /**
     * Set transporteur
     *
     * @param string $transporteur
     *
     * @return Livreur_boutique
     */
    public function setTransporteur($transporteur)
    {
        $this->transporteur = $transporteur;

        return $this;
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
     * @return Livreur_boutique
     */
    public function setBoutique(Boutique $boutique)
    {
        $this->boutique = $boutique;

        return $this;
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
     * Set id
     *
     * @param Profile_transporteur $id
     *
     * @return Livreur_boutique
     */
    public function setId(Profile_transporteur $id)
    {
        $this->id = $id;

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
     * @return Livreur_boutique
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }
}
