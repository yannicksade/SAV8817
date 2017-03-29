<?php

namespace APM\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Groupe_Relationnel
 *
 * @ORM\Table(name="groupe_relationnel")
 * @ORM\Entity(repositoryClass="APM\UserBundle\Repository\Groupe_relationnelRepository")
 * @UniqueEntity("code")
 */
class Groupe_relationnel
{
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, nullable=false)
     */
    private $code;

    /**
     * @var string
     * @Assert\Length(min=2, max=254)
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Length(min=2, max=155)
     * @ORM\Column(name="designation", type="string", length=255, nullable=true)
     */
    private $designation;

    /**
     * @var integer
     * @Assert\Choice({0,1,2,3,4,5,6,7,8})
     * @ORM\Column(name="type", type="integer", length=255, nullable=true)
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Utilisateur_avm
     *
     * @ORM\ManyToOne(targetEntity="APM\UserBundle\Entity\Utilisateur_avm", inversedBy="groupesProprietaire")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="proprietaire_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $proprietaire;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="APM\UserBundle\Entity\Individu_to_groupe", mappedBy="groupeRelationnel", cascade={"persist","remove"})
     *
     */
    private $groupeIndividus;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->groupeIndividus = new ArrayCollection();
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
     * @return Groupe_relationnel
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
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
     * @return Groupe_relationnel
     */
    public function setDesignation($designation)
    {
        $this->designation = $designation;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Groupe_relationnel
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get Id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get proprietaire
     *
     * @return Utilisateur_avm
     */
    public function getProprietaire()
    {
        return $this->proprietaire;
    }

    /**
     * Set proprietaire
     *
     * @param Utilisateur_avm $proprietaire
     *
     * @return Groupe_relationnel
     */
    public function setProprietaire(Utilisateur_avm $proprietaire)
    {
        $this->proprietaire = $proprietaire;

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
     * @return Groupe_relationnel
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Add groupeIndividus
     *
     * @param Individu_to_groupe $groupeIndividus
     *
     * @return Groupe_relationnel
     */
    public function addGroupeIndividus(Individu_to_groupe $groupeIndividus)
    {
        $this->groupeIndividus[] = $groupeIndividus;

        return $this;
    }

    /**
     * Remove groupeIndividus
     *
     * @param Individu_to_groupe $groupeIndividus
     */
    public function removeGroupeIndividus(Individu_to_groupe $groupeIndividus)
    {
        $this->groupeIndividus->removeElement($groupeIndividus);
    }

    /**
     * Get groupeIndividus
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGroupeIndividus()
    {
        return $this->groupeIndividus;
    }
}
