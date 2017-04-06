<?php

namespace APM\MarketingDistribueBundle\Entity;

use APM\MarketingDistribueBundle\Factory\TradeFactory;
use APM\VenteBundle\Entity\Boutique;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Quota
 *
 * @ORM\Table(name="quota")
 * @ORM\Entity(repositoryClass="APM\MarketingDistribueBundle\Repository\QuotaRepository")
 * @UniqueEntity("code")
 */
class Quota extends TradeFactory
{
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, nullable=false)
     */
    private $code;

    /**
     * @var \DateTime
     * @ORM\column(type="datetime")
     */
    private $date;

    /**
     * @var string
     * @Assert\Length(min=2, max=254)
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Length(min=2, max=55)
     * @ORM\Column(name="libelleQuota", type="string", length=255, nullable=true)
     */
    private $libelleQuota;

    /**
     * @var string
     *
     * @ORM\Column(name="valeurQuota", type="string", length=255, nullable=true)
     */
    private $valeurQuota;

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
     * @var Boutique
     * @ORM\ManyToOne(targetEntity="APM\VenteBundle\Entity\Boutique", inversedBy="commissionnements")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="boutiqueProprietaire_id", referencedColumnName="id", nullable=false)
     *     })
     */
    private $boutiqueProprietaire;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="APM\MarketingDistribueBundle\Entity\Commissionnement", mappedBy="commission")
     *
     */
    private $commissionnements;

    /**
     * Constructor
     * @param string $var
     */
    public function __construct($var)
    {
        $this->commissionnements = new ArrayCollection();
        $this->code = "QA" . $var;
        $this->date = new \DateTime();
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
     * @return Quota
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get libelleQuota
     *
     * @return string
     */
    public function getLibelleQuota()
    {
        return $this->libelleQuota;
    }

    /**
     * Set libelleQuota
     *
     * @param string $libelleQuota
     *
     * @return Quota
     */
    public function setLibelleQuota($libelleQuota)
    {
        $this->libelleQuota = $libelleQuota;

        return $this;
    }

    /**
     * Get valeurQuota
     *
     * @return string
     */
    public function getValeurQuota()
    {
        return $this->valeurQuota;
    }

    /**
     * Set valeurQuota
     *
     * @param string $valeurQuota
     *
     * @return Quota
     */
    public function setValeurQuota($valeurQuota)
    {
        $this->valeurQuota = $valeurQuota;

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
     * @return Quota
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
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
     * @return Quota
     */
    public function setBoutiqueProprietaire(Boutique $boutiqueProprietaire)
    {
        $this->boutiqueProprietaire = $boutiqueProprietaire;

        return $this;
    }

    /**
     * Add commissionnement
     *
     * @param Commissionnement $commissionnement
     *
     * @return Quota
     */
    public function addCommissionnement(Commissionnement $commissionnement)
    {
        $this->commissionnements[] = $commissionnement;

        return $this;
    }

    /**
     * Remove commissionnement
     *
     * @param Commissionnement $commissionnement
     */
    public function removeCommissionnement(Commissionnement $commissionnement)
    {
        $this->commissionnements->removeElement($commissionnement);
    }

    /**
     * Get commissionnements
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCommissionnements()
    {
        return $this->commissionnements;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;
    }
}
