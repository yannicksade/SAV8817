<?php

namespace APM\VenteBundle\Entity;

use APM\VenteBundle\TradeAbstraction\Trade;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Categorie
 *
 * @ORM\Table(name="categorie")
 * @ORM\Entity(repositoryClass="APM\VenteBundle\Repository\CategorieRepository")
 * @UniqueEntity("code")
 */
class Categorie extends Trade
{

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, nullable=false)
     */
    private $code;

    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Length(min=2, max=155)
     * @ORM\Column(name="designation", type="string", length=255, nullable=true)
     */
    private $designation;

    /**
     * @var string
     * @Assert\Length(min=2, max=254)
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;


    /**
     * @var boolean
     * @Assert\Choice({0,1})
     * @ORM\Column(name="estLivrable", type="boolean", nullable=true)
     */
    private $livrable;

    /**
     * @var boolean
     * @Assert\Choice({0,1})
     * @ORM\Column(name="publiable", type="boolean", nullable=true)
     */
    private $publiable;

    /**
     * @var integer
     * @Assert\Choice({0,1,2,3})
     * @ORM\Column(name="etat", type="integer", nullable=true)
     */
    private $etat;

    /**
     * @var string
     * @Assert\Url
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     */
    private $image;

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
     * @var Categorie
     *
     * @ORM\ManyToOne(targetEntity="APM\VenteBundle\Entity\Categorie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="categorieCourante_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $categorieCourante;


    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="APM\VenteBundle\Entity\Offre", mappedBy="categorie")
     *
     */
    private $offres;

    /**
     * @var Boutique
     * @ORM\ManyToOne(targetEntity="APM\VenteBundle\Entity\Boutique", inversedBy="categories")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="boutique_id", referencedColumnName="id", nullable=true)
     *     })
     */
    private $boutique;

    /**
     * Categorie constructor.
     * @param string $var
     */
    public function __construct($var)
    {
        $this->offres = new ArrayCollection();
        $this->code = "CA" . $var;
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
     * @return Categorie
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
     * @return Categorie
     */
    public function setDesignation($designation)
    {
        $this->designation = $designation;

        return $this;
    }

    /**
     * Get estLivrable
     *
     * @return boolean
     */
    public function isLivrable()
    {
        return $this->livrable;
    }

    /**
     * Get livrable
     *
     * @return boolean
     */
    public function getLivrable()
    {
        return $this->livrable;
    }

    /**
     * Set estLivrable
     *
     * @param boolean $estLivrable
     *
     * @return Categorie
     */
    public function setLivrable($estLivrable)
    {
        $this->livrable = $estLivrable;

        return $this;
    }

    /**
     * Get etat
     *
     * @return string
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * Set etat
     *
     * @param string $etat
     *
     * @return Categorie
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Categorie
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Id
     * Get Id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add offre
     *
     * @param Offre $offre
     *
     * @return Categorie
     */
    public function addOffre(Offre $offre)
    {
        $this->offres[] = $offre;

        return $this;
    }

    /**
     * Remove offre
     *
     * @param Offre $offre
     */
    public function removeOffre(Offre $offre)
    {
        $this->offres->removeElement($offre);
    }

    /**
     * Get offres
     *
     * @return Collection
     */
    public function getOffres()
    {
        return $this->offres;
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
     * @return Categorie
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get categorieCourante
     *
     * @return Categorie
     */
    public function getCategorieCourante()
    {
        return $this->categorieCourante;
    }

    /**
     * Set categorieCourante
     *
     * @param Categorie $categorieCourante
     *
     * @return Categorie
     */
    public function setCategorieCourante(Categorie $categorieCourante = null)
    {
        $this->categorieCourante = $categorieCourante;

        return $this;
    }

    /**
     * Get publiable
     *
     * @return boolean
     */
    public function getPubliable()
    {
        return $this->publiable;
    }

    /**
     * Set publiable
     *
     * @param boolean $publiable
     *
     * @return Categorie
     */
    public function setPubliable($publiable)
    {
        $this->publiable = $publiable;

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
     * @return Categorie
     */
    public function setBoutique(Boutique $boutique = null)
    {
        $this->boutique = $boutique;

        return $this;
    }
}
