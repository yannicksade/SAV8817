<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 18/08/2017
 * Time: 23:15
 */

namespace APM\UserBundle\Controller;

use APM\UserBundle\Entity\Utilisateur;
use APM\VenteBundle\Entity\Boutique;
use APM\VenteBundle\Entity\Categorie;
use APM\VenteBundle\Entity\Offre;
use APM\VenteBundle\Entity\Remise;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
//use FOS\RestBundle\View\View;
use JMS\Serializer\DeserializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Lock;
use Symfony\Component\Security\Core\Exception\LockedException;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;

use Lexik\Bundle\JWTAuthenticationBundle\Security\Guard\JWTTokenAuthenticator;

class SecurityController extends FOSRestController
{
    /**
     * @View
     */
    public function testAction(Request $request)
    {
        /*$csrfToken = $this->has('security.csrf.token_manager')
            ? $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue()
            : null;*/

        $em = $this->getDoctrine()->getManager();
        $offres = $em->getRepository(Offre::class)->findAll();
        /*$data = [
            "token" => $csrfToken,
            'name' => "John Doe",
        ];
        */

        //$response->headers->set('XSRF-TOKEN', $csrfToken);clear
        $serializerContext = SerializationContext::create()->enableMaxDepthChecks();
        //$deSerializerContext = DeserializationContext::create();
        $serializer = SerializerBuilder::create()->build();

        /** @var Offre $offre */
        $offre = $serializer->deserialize("{\"designation\":\"BAMBOU DE CHINE2\",\"description\":\"Blar blar\",\"mode_vente\":1, \"etat\":2, \"type_offre\":1}", Offre::class, 'json'); // , $deSerializerContext->setGroups(array("test"))
        $offre->setCode("YSADE023");
        $offre->setDateCreation(new \DateTime("now"));

        //$view = $this->view([$offre], 200);
        $data = $serializer->serialize($offres, 'json', $serializerContext->setGroups(array("offre_details")));
        //->setTemplate('FOSUserBundle:security:login.html.twig');
        // ->setTemplateVar('offres')
        //->setTemplateData($data);
        //->setFormat("html");
        // ->setRoute("api_user_login");
        /* ->setRoute('');*/

        /* $em->persist($offre);
         $em->flush();*/

        return $data;
        /*return $this->renderLogin($lastUsername, array(
            'last_username' => $lastUsername,
            'image' => $image,
            'email' => $email,
            'error' => $error,
            'csrf_token' => $csrfToken,
            'url_image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/', 'resolve_img'),
        ));*/

    }

    /**
     * Renders the login template with the given parameters. Overwrite this function in
     * an extended controller to provide additional data for the login template.
     *
     * @param $lastUsername
     * @param array $data
     * @return Response
     */
    /*protected function renderLogin($lastUsername, array $data)
    {
        $template = $lastUsername?'@FOSUser/Security/locked-screen.html.twig':'@FOSUser/Security/login.html.twig';

        $view = $this->view(null, 200)
            ->setTemplate($template)
            //->setTemplateVar('data')
            ->setTemplateData($data);
            //->setRoute('apm_core_user-dashboard_index');


        return $this->handleView($view);

       // return $this->render($template, $data);
    }*/

    /**
     * @Post("/login", name="login", options={"method_prefix"= false})
     */
    public function checkAction()
    {
        throw new \DomainException('You should never see this');
    }

    /**
     *
     * @Get("/logout", name="logout", options={"method_prefix"= false})
     */
    public function logoutAction()
    {
        throw new \DomainException('You should never see this');
    }

    /**
     * @Lock("/lock")
     */
    public function lockAction(Request $request)
    {
        $session = $request->getSession();
        /** @var Utilisateur $user */
        $user = $this->getUser();
        $session->set('email', $user->getEmail());
        $session->set('username', $user->getUsername());
        $session->set('image', $user->getImage());

        //$jwtManager = $this->container->get('lexik_jwt_authentication.jwt_manager');

        throw new LockedException();
    }

    public function reloginAction(Request $request)
    {
        $session = $request->getSession();
        $session->invalidate();
        //supprimer le token
        return $this->routeRedirectView('fos_user_security_login');
    }
}
