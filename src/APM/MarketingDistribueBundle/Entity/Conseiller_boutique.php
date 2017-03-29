<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 29/01/2017
 * Time: 08:18
 */
namespace APM\MarketingDistribueBundle\Entity;

use APM\VenteBundle\Entity\Boutique;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Conseiller
 *
 * @ORM\Table(name="conseiller_boutique")
 * @ORM\Entity(repositoryClass="APM\MarketingDistribueBundle\Repository\Conseiller_boutiqueRepository")
 * @UniqueEntity("code", message="Ce code est déjà pris.")
 */
class Conseiller_boutique
{
    /**
     * @var string
     * @ORM\Column(name="code", type="string", length=255, nullable=false)
     */
    private $code;

    /**
     * Id
     * @var integer
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     * @Assert\Range(min=0)
     * @ORM\Column(name="gainValeur", type="integer", nullable=true)
     */
    private $gainValeur;

    /**
     * @var Conseiller
     * @ORM\ManyToOne(targetEntity="APM\MarketingDistribueBundle\Entity\Conseiller", inversedBy="conseillerBoutiques")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="conseiller_id", referencedColumnName="id", nullable=false)
     *     })
     */
    private $conseiller;

    /**
     * @var Boutique
     * @ORM\ManyToOne(targetEntity="APM\VenteBundle\Entity\Boutique", inversedBy="boutiqueConseillers")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="boutique_id", referencedColumnName="id", nullable=false)
     *     })
     */
    private $boutique;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="APM\MarketingDistribueBundle\Entity\Commissionnement", mappedBy="conseillerBoutique", cascade={"remove"})
     *
     */
    private $commissionnements;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->commissionnements = new ArrayCollection();
        $this->commissions = new ArrayCollection();
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
     * @return Conseiller_boutique
     */
    public function setCode($code)
    {
        $this->code = $code;

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
     * Get conseiller
     *
     * @return Conseiller
     */
    public function getConseiller()
    {
        return $this->conseiller;
    }

    /**
     * Set conseiller
     *
     * @param Conseiller $conseiller
     *
     * @return Conseiller_boutique
     */
    public function setConseiller(Conseiller $conseiller)
    {
        $this->conseiller = $conseiller;

        return $this;
    }

    /**
     * Get boutique
     *
     * @return \APM\VenteBundle\Entity\Boutique
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
     * @return Conseiller_boutique
     */
    public function setBoutique(Boutique $boutique)
    {
        $this->boutique = $boutique;

        return $this;
    }

    /**
     * Add commissionnement
     *
     * @param Commissionnement $commissionnement
     *
     * @return Conseiller_boutique
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
     * Add commission
     *
     * @param Quota $commission
     *
     * @return Conseiller_boutique
     */
    public function addCommission(Quota $commission)
    {
        $this->commissions[] = $commission;

        return $this;
    }

    /**
     * Remove commission
     *
     * @param Quota $commission
     */
    public function removeCommission(Quota $commission)
    {
        $this->commissions->removeElement($commission);
    }

    /**
     * Get commissions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCommissions()
    {
        return $this->commissions;
    }

    /**
     * Get gainValeur
     *
     * @return integer
     */
    public function getGainValeur()
    {
        return $this->gainValeur;
    }

    /**
     * Set gainValeur
     *
     * @param integer $gainValeur
     *
     * @return Conseiller_boutique
     */
    public function setGainValeur($gainValeur)
    {
        $this->gainValeur = $gainValeur;

        return $this;
    }
}
