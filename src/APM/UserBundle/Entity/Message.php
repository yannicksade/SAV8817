<?php

namespace APM\UserBundle\Entity;

use APM\AnimationBundle\Entity\Base_documentaire;
use APM\VenteBundle\Entity\Offre;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Message
 *
 * @ORM\Table(name="message")
 * @ORM\Entity(repositoryClass="APM\UserBundle\Repository\MessageRepository")
 * @UniqueEntity("code")
 */
class Message
{
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, nullable=false)
     */
    protected $code;
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @var string
     * @Assert\Length(min=0)
     * @ORM\Column(name="contenu", type="text", nullable=true)
     */
    private $contenu;
    /**
     * @var \DateTime
     * @Assert\DateTime
     * @ORM\Column(name="dateEmission", type="datetime", nullable=false)
     */
    private $dateEmission;
    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="APM\VenteBundle\Entity\Offre")
     */
    private $offres;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="APM\UserBundle\Entity\Piece_jointe", mappedBy="message", cascade={"persist","remove"})
     */
    private $piecesJointes;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="APM\AnimationBundle\Entity\Base_documentaire")
     */
    private $documents;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->offres = new ArrayCollection();
        $this->piecesjointes = new ArrayCollection();
        $this->dateEmission = new \DateTime();
        $this->documents = new ArrayCollection();
    }

    /**
     * Get contenu
     *
     * @return string
     */
    public function getContenu()
    {
        return $this->contenu;
    }

    /**
     * Set contenu
     *
     * @param string $contenu
     *
     * @return Message
     */
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;

        return $this;
    }

    /**
     * Get dateEmission
     *
     * @return \DateTime
     */
    public function getDateEmission()
    {
        return $this->dateEmission;
    }

    /**
     * Set dateEmission
     *
     * @param \DateTime $dateEmission
     *
     * @return Message
     */
    public function setDateEmission($dateEmission)
    {
        $this->dateEmission = $dateEmission;

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
     * Add offre
     *
     * @param Offre $offre
     *
     * @return Message
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
     * Add Piece_jointe
     *
     * @param Piece_jointe $Piece_jointe
     *
     * @return Message
     */
    public function addPiecejJinte(Piece_jointe $Piece_jointe)
    {
        $this->piecesJointes[] = $Piece_jointe;

        return $this;
    }

    /**
     * Remove Piece_jointe
     *
     * @param Piece_jointe $Piece_jointe
     */
    public function removePiecesjointe(Piece_jointe $Piece_jointe)
    {
        $this->piecesjointes->removeElement($Piece_jointe);
    }

    /**
     * Get piecesJointes
     *
     * @return Collection
     */
    public function getPiecesJointes()
    {
        return $this->piecesJointes;
    }

    /**
     * Add piecesJointe
     *
     * @param Piece_jointe $piecesJointe
     *
     * @return Message
     */
    public function addPiecesJointe(Piece_jointe $piecesJointe)
    {
        $this->piecesJointes[] = $piecesJointe;

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
     * @return Message
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Add document
     *
     * @param Base_documentaire $document
     *
     * @return Message
     */
    public function addDocument(Base_documentaire $document)
    {
        $this->documents[] = $document;

        return $this;
    }

    /**
     * Remove document
     *
     * @param Base_documentaire $document
     */
    public function removeDocument(Base_documentaire $document)
    {
        $this->documents->removeElement($document);
    }

    /**
     * Get documents
     *
     * @return Collection
     */
    public function getDocuments()
    {
        return $this->documents;
    }
}
