<?php
/*les admins representent le staff de l'entreprise (+super admin)
*   il ne doit avoir qu'un seul admin avec le role super-admin
*/
namespace APM\UserBundle\Entity;

use APM\CoreBundle\Trade\CodeGenerator;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Exclude;

/**
 * @ORM\Entity(repositoryClass="APM\UserBundle\Repository\AdminRepository")
 * @ORM\Table(name="Admin")
 * @ExclusionPolicy("all")
 */
class Admin extends Utilisateur
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct() {
        parent::__construct();
        $this->dateEnregistrement = new \DateTime();
        $this->lastLogin = new \DateTime;
        $this->enabled = false;
        $this->code = "XX" . CodeGenerator::getGenerator(4);
        $this->roles = array('ROLE_STAFF');
    }

    public function __toString()
    {
        return parent::__toString();
    }

}
