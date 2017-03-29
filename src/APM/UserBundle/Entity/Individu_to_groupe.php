<?php

namespace APM\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Individu_to_groupe
 *
 * @ORM\Table(name="individu_to_groupe")
 * @ORM\Entity(repositoryClass="APM\UserBundle\Repository\Individu_to_groupeRepository")
 * @UniqueEntity("code")
 */
class Individu_to_groupe
{
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, nullable=false)
     */
    private $code;

    /**
     * @var integer
     * @Assert\Choice({0,1,2})
     * @ORM\Column(name="propriete", type="integer", length=255, nullable=true)
     */
    private $propriete;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Groupe_relationnel
     *
     * @ORM\ManyToOne(targetEntity="APM\UserBundle\Entity\Groupe_relationnel", inversedBy="groupeIndividus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="groupeRelationnel_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $groupeRelationnel;

    /**
     * @var Utilisateur_avm
     *
     * @ORM\ManyToOne(targetEntity="APM\UserBundle\Entity\Utilisateur_avm", inversedBy="individuGroupes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="individu_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $individu;

    /**
     * Get propriete
     *
     * @return string
     */
    public function getPropriete()
    {
        return $this->propriete;
    }

    /**
     * Set propriete
     *
     * @param string $propriete
     *
     * @return Individu_to_groupe
     */
    public function setPropriete($propriete)
    {
        $this->propriete = $propriete;

        return $this;
    }


    /**
     * Get  Id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Get groupeRelationnel
     *
     * @return Groupe_relationnel
     */
    public function getGroupeRelationnel()
    {
        return $this->groupeRelationnel;
    }

    /**
     * Set groupeRelationnel
     *
     * @param Groupe_relationnel $groupeRelationnel
     *
     * @return Individu_to_groupe
     */
    public function setGroupeRelationnel(Groupe_relationnel $groupeRelationnel = null)
    {
        $this->groupeRelationnel = $groupeRelationnel;

        return $this;
    }

    /**
     * Get individu
     *
     * @return Utilisateur_avm
     */
    public function getIndividu()
    {
        return $this->individu;
    }

    /**
     * Set individu
     *
     * @param Utilisateur_avm $individu
     *
     * @return Individu_to_groupe
     */
    public function setIndividu(Utilisateur_avm $individu = null)
    {
        $this->individu = $individu;

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
     * @return Individu_to_groupe
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }
}
