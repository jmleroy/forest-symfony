<?php

namespace ForestAdmin\ForestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;

/**
 * Class ForestController
 * @package ForestAdmin\ForestBundle\Controller
 */
class ForestController extends Controller
{
    /**
     * This route is called by ForestAdmin to test if ForestBundle is properly installed
     * @Route("/forest")
     */
    public function indexAction()
    {
        $response = new Response;
        $response->setStatusCode(204);

        return $response;
    }

    /**
     * @Route("/forest/sessions")
     * @Method("POST")
     *
     * @param Request $request
     * @return Response
     */
    public function sessionsAction(Request $request)
    {
        $data = json_decode($request->getContent());
        $forest = $this->get('forestadmin.forest');

        $allowedUsers = $forest->getAllowedUsers($data);

        $currentUser = null;
        foreach($allowedUsers as $user) {
            if($user->email == $data->email && password_verify($data->password, $user->password)) {
                $currentUser = $user;
                break;
            }
        }

        if($currentUser) {
            $token = $forest->generateAuthToken($currentUser);
            return new JsonResponse(array('token' => $token));
        }

        return new Response('Unauthorized', 401);
    }

    /**
     * TODO to delete or use in dev only
     * @Route("/forest/post")
     */
    public function postAction()
    {
        $forest = $this->get('forestadmin.forest');
        $forest->postApimap();
        
        return new Response('posted');
    }

    /**
     * TODO to delete or use in dev only
     * @Route("/forest/trace")
     * @return JsonResponse
     */
    public function traceAction()
    {
        $forest = $this->get('forestadmin.forest');
        $apimap = $forest->getApimap();

        return new Response($apimap);
    }

    /**
     * TODO to delete or use in dev only
     * @Route("/forest/dump")
     * @return Response
     */
    public function dumpAction()
    {
        $forest = $this->get('forestadmin.forest');
        $collections = $forest->getCollections();
        $apimap = json_decode($forest->getApimap());
        $metadata = $forest->getMetadata();
        foreach($metadata as $old_key => $cm) {
            $new_key = str_replace('\\', '_', $cm->getName());
            $metadata[$new_key] = $cm;
            unset($metadata[$old_key]);
        }

        return new Response($this->render('ForestBundle:Default:index.html.twig', array(
            'collections' => $collections,
            'apimap' => $apimap,
            'metadata' => $metadata,
        )));
    }
}
