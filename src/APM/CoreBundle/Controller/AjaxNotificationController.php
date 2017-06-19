<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 18/05/2017
 * Time: 09:37
 */

namespace APM\CoreBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class AjaxNotificationController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        /** @var Session $session */
        $session = $this->get('session');
        $type_messages = $session->getFlashBag()->all();
        return $this->json($type_messages, 200);

        /*return new JsonResponse(
            ["items" =>
                [
                    ["id" => "1",
                        "designation" => "SAMSUNG GALAXY 6",
                        "description" => "blar blar blar blar blar blarblar blar blar  blar blar blar blar blar blar",
                        "url_image" => $this->get('apm_core.packages_maker')->getPackages()->getUrl("/team2.jpg", "resolve_img"),
                    ],
                    ["id" => "2",
                        "designation" => "TECHNO",
                        "description" => "blar blar blar blar blar blarblar blar blar  blar blar blar blar blar blar",
                        "url_image" => $this->get('apm_core.packages_maker')->getPackages()->getUrl("/team2.jpg", "resolve_img"),
                    ],
                    ["id" => "3",
                        "designation" => "ALCATEL",
                        "description" => "blar blar blar blar blar blarblar blar blar  blar blar blar blar blar blar",
                        'url_image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/team2.jpg', 'resolve_img'),
                    ],
                    ["id" => "4",
                        "designation" => "HTC 7",
                        "description" => "blar blar blar blar blar blarblar blar blar  blar blar blar blar blar blar",
                        'url_image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/team2.jpg', 'resolve_img'),
                    ]
                ]
            ]
        );*/
    }
}
