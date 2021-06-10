<?php

namespace App\Controller;

use App\Entity\Room;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        $randomString = HomeController::generateRandomString();

        return $this->render('home/index.html.twig', [
            "randomString" => $randomString
        ]);
    }

    /**
     * @Route("/ajax/startGame", name="ajax_start_game")
     */
    public function ajaxStartGame(Request $request)
    {
        if ($request->isXmlHttpRequest() || $request->query->get('showJson') == 1) {  
            //Recovery the token
            $token = $request->request->get('token');
            
            //Insert the token into the table Room
            $room = new Room();
            $room->setToken($token);
            $room->setIp($request->getClientIp());
            $room->setIpBis('');
            $room->setCreatedAt(new \DateTime("now"));
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($room);
            $entityManager->flush();

            return new JsonResponse($token);
        } else {
            return $this->redirectToRoute('home');
        }
    }


    /**
     * Function to generate an aleatoire String
     */
    public static function generateRandomString($longueur = 25)
    {
        $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $longueurMax = strlen($caracteres);
        $chaineAleatoire = '';
        for ($i = 0; $i < $longueur; $i++) {
            $chaineAleatoire .= $caracteres[rand(0, $longueurMax - 1)];
        }
        return $chaineAleatoire;
    }
}
