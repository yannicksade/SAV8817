<?php

namespace APM\MarketingReseauBundle\Entity;

use APM\MarketingDistribueBundle\Entity\Conseiller;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Reseau_conseillers
 *
 * @ORM\Table(name="reseau_conseillers")
 * @ORM\Entity(repositoryClass="APM\MarketingReseauBundle\Repository\Reseau_conseillersRepository")
 * @UniqueEntity("code")
 */
class Reseau_conseillers
{
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, nullable=false)
     */
    private $code;

    /**
     * @var string
     *
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
     * Id
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="APM\MarketingDistribueBundle\Entity\Conseiller", mappedBy="reseau")
     * @ORM\JoinColumn(name="advisors_id", referencedColumnName="id")
     */
    private $advisors;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->advisors = new ArrayCollection();
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
     * @return Reseau_conseillers
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
     * @return Reseau_conseillers
     */
    public function setDesignation($designation)
    {
        $this->designation = $designation;

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
     * Add advisor
     *
     * @param Conseiller $advisor
     *
     * @return Reseau_conseillers
     */
    public function addAdvisor(Conseiller $advisor)
    {
        $this->advisors[] = $advisor;

        return $this;
    }

    /**
     * Remove advisor
     *
     * @param Conseiller $advisor
     */
    public function removeAdvisor(Conseiller $advisor)
    {
        $this->advisors->removeElement($advisor);
    }

    /**
     * Get advisors
     *
     * @return Collection
     */
    public function getAdvisors()
    {
        return $this->advisors;
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
     * @return Reseau_conseillers
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }
}
