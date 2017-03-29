<?php
namespace APM\VenteBundle\Form\Type;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use APM\VenteBundle\Entity\Categorie;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 10/01/2017
 * Time: 23:42
 */
class ObjectToStringTransformer implements DataTransformerInterface
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }


    /**
     * cette fonction transforme un objet en String
     *
     * @param Categorie|null $categorie
     * @return string
     */
    public function transform($categorie)
    {
        if (null === $categorie) {
            return '';
        }
        return $categorie->getDesignation();
    }

    /**
     * Cette fonction transforme un string en object
     * @param mixed $value The value in the transformed representation
     *
     * @return mixed The value in the original representation
     * //     *
     * //     * @throws TransformationFailedException When the transformation fails.
     * //     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return null;
        }

        $object = $this->manager
            ->getRepository('APMVenteBundle:Categorie')->findOneBy(array('designation' => $value));
        if (null === $object) {
            //Chance!!Ce message ne sera pas afficher au utilisateur
            throw new TransformationFailedException(
                printf('Lobjet id:"%s" nexiste pas', $value));
        }
        return $object;
    }
}