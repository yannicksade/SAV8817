<?php

namespace APM\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="APM\UserBundle\Repository\UtilisateurRepository")
 * @ORM\Table(name="utilisateur")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"admin" = "Admin", "utilisateur_avm" = "Utilisateur_avm"})
 *
 */
abstract class Utilisateur extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Length(min=2, max=100)
     * @ORM\Column(name="nom", type="string", length=255, nullable=true)
     */
    protected $nom;
    /**
     * @var string
     * @Assert\Length(min=2, max=100)
     * @ORM\Column(name="prenom", type="string", length=255, nullable=true)
     */
    protected $prenom;
    /**
     * @var Date
     * @Assert\Date
     * @ORM\Column(name="dateNaissance", type="date", nullable=true)
     */
    protected $dateNaissance;
    /**
     * @Assert\Country()
     * @ORM\Column(name="pays", type="string", length=255, nullable=true)
     */
    protected $pays;
    /**
     * @var integer
     * @Assert\Choice({0,1})
     * @ORM\Column(name="genre", type="integer", nullable=true)
     */
    protected $genre;
    /**
     * @var integer
     * @Assert\Range(min=100,)
     * @ORM\Column(name="telephone", type="integer", nullable=true)
     */
    protected $telephone;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, nullable=true)
     */
    protected $code;

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Utilisateur
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     *
     * @return Utilisateur
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get dateNaissance
     *
     * @return Date
     */
    public function getDateNaissance()
    {
        return $this->dateNaissance;
    }

    /**
     * Set dateNaissance
     *
     * @param \DateTime $dateNaissance
     *
     * @return Utilisateur
     */
    public function setDateNaissance($dateNaissance)
    {
        $this->dateNaissance = $dateNaissance;

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
     * @return Utilisateur
     */
    public function setPays($pays)
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * Get genre
     *
     * @return string
     */
    public function getGenre()
    {
        return $this->genre;
    }

    /**
     * Set genre
     *
     * @param string $genre
     *
     * @return Utilisateur
     */
    public function setGenre($genre)
    {
        $this->genre = $genre;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     *
     * @return Utilisateur
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

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
     * @return Utilisateur
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }
}
