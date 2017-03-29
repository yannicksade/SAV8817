<?php

namespace APM\TransportBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Zone_intervention
 *
 * @ORM\Table(name="Zone_intervention")
 * @ORM\Entity(repositoryClass="APM\TransportBundle\Repository\Zone_interventionRepository")
 * @UniqueEntity("code")
 */
class Zone_intervention
{
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, nullable=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="zonetime", type="string", length=255, nullable=true)
     */
    private $zoneTime;

    /**
     * @var string
     * @Assert\Locale
     * @ORM\Column(name="language", type="string", length=255, nullable=true)
     */
    private $language;

    /**
     * @var string
     * @Assert\Length(min=2, max=254)
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string
     * @Assert\Length(min=2, max=100)
     * @ORM\Column(name="designation", type="string", length=255, nullable=true)
     */
    private $designation;

    /**
     * @var string
     * @Assert\Length(min=2, max=254)
     * @ORM\Column(name="adresse", type="string", length=255, nullable=true)
     */
    private $adresse;

    /**
     * @var string
     * @Assert\Country
     * @ORM\Column(name="pays", type="string", length=255, nullable=true)
     */
    private $pays;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="APM\TransportBundle\Entity\Profile_transporteur", inversedBy="transporteurZones")
     * @ORM\JoinTable(name="transporteur_Zone_intervention",
     *   joinColumns={
     *     @ORM\JoinColumn(name="zoneIntervention_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="transporteur_id", referencedColumnName="id", nullable=true)
     *   }
     * )
     */
    private $zoneTransporteurs;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->zoneTransporteurs = new ArrayCollection();
    }

    /**
     * Get language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set language
     *
     * @param string $language
     *
     * @return Zone_intervention
     */
    public function setLanguage($language)
    {
        $this->language = $language;

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
     * @return Zone_intervention
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }


    /**
     * Get pays
     *
     * @return string
     */
    public function getPays()
    {
        return $this->pays;
    }

    /**
     * Set pays
     *
     * @param string $pays
     *
     * @return Zone_intervention
     */
    public function setPays($pays)
    {
        $this->pays = $pays;

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
     * Add transporteur
     *
     * @param Profile_transporteur $transporteur
     *
     * @return Zone_intervention
     */
    public function addZoneTransporteur(Profile_transporteur $transporteur)
    {
        $this->zoneTransporteurs[] = $transporteur;

        return $this;
    }

    /**
     * Remove transporteur
     *
     * @param Profile_transporteur $transporteur
     */
    public function removeZoneTransporteur(Profile_transporteur $transporteur)
    {
        $this->zoneTransporteurs->removeElement($transporteur);
    }

    /**
     * Get transporteurs
     *
     * @return Collection
     */
    public function getZoneTransporteurs()
    {
        return $this->zoneTransporteurs;
    }

    /**
     * Get designation
     *
     * @return string
     */
    public function getDesignation()
    {
        return $this->designation;
    }

    /**
     * Set designation
     *
     * @param string $designation
     *
     * @return Zone_intervention
     */
    public function setDesignation($designation)
    {
        $this->designation = $designation;

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
     * @return Zone_intervention
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get zoneTime
     *
     * @return string
     */
    public function getZoneTime()
    {
        return $this->zoneTime;
    }

    /**
     * Set zoneTime
     *
     * @param string $zoneTime
     *
     * @return Zone_intervention
     */
    public function setZoneTime($zoneTime)
    {
        $this->zoneTime = $zoneTime;

        return $this;
    }

    /**
     * Get adresse
     *
     * @return string
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * Set adresse
     *
     * @param string $adresse
     *
     * @return Zone_intervention
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }
}
