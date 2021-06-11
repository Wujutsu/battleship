<?php

namespace App\Controller;

use App\Entity\Game;
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
            $room->setCellulePlayerOne('');
            $room->setCellulePlayerTwo('');
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($room);
            $entityManager->flush();

            //Insert boat for playerOne and playerTwo
            for ($player = 0; $player < 2; $player++) {
                $tabObjectBoat = HomeController::generateBoat([1, 1, 1, 1, 2, 2, 2, 3, 3, 4], $player, $room);
                foreach ($tabObjectBoat as $game) {
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($game);
                    $entityManager->flush();
                }
            }

            return new JsonResponse($token);
        } else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * Generate the boats of the game in random position
     * @return Game[]  
     */
    public static function generateBoat(array $tabBoatLength, $player, Room $room)
    {
        $tabObjectBoat = array();
        $tabBoxAffected = array();

        foreach ($tabBoatLength as $key => $length) {

            if ($length <= 10) {
                $posX = 1;
                $posY = 1;
                $rotation = 0;

                //Check if boat don't touch over boats or override the game
                $retour = false;
                while ($retour == false) {
                    $retour = true;
                    $posX = rand(1, 10);
                    $posY = rand(1, 10);
                    $rotation = rand(0, 1);

                    for ($i = 0; $i < $length; $i++) {
                        if ($rotation == 0)
                            if (isset($tabBoxAffected[($posX + $i).';'.$posY]) || ($posX + ($length - 1) > 10))
                                $retour = false;
                        if ($rotation == 1)
                            if (isset($tabBoxAffected[$posX.';'.($posY + $i)]) || ($posY + ($length - 1) > 10))
                                $retour = false;
                    }
                }

                //If all conditions are checked
                for ($i = 0; $i < $length; $i++) {

                    if ($rotation == 0) {
                        //Management of contact boxes of the boat
                        if ($i == 0) {
                            $tabBoxAffected[($posX - 1).';'.($posY - 1)] = true;
                            $tabBoxAffected[($posX - 1).';'.($posY + 1)] = true;
                            $tabBoxAffected[($posX - 1).';'.$posY] = true;
                            $tabBoxAffected[($posX + $length).';'.($posY - 1)] = true;
                            $tabBoxAffected[($posX + $length).';'.($posY + 1)] = true;
                            $tabBoxAffected[($posX + $length).';'.$posY] = true;
                        }
                        $tabBoxAffected[($posX + $i).';'.($posY - 1)] = true;
                        $tabBoxAffected[($posX + $i).';'.($posY + 1)] = true;

                        //Management boat boxes
                        $tabBoxAffected[($posX + $i).';'.$posY] = true;

                    } else {
                        //Management of contact boxes of the boat
                        if ($i == 0) {
                            $tabBoxAffected[($posX - 1).';'.($posY - 1)] = true;
                            $tabBoxAffected[($posX + 1).';'.($posY - 1)] = true;
                            $tabBoxAffected[$posX.';'.($posY - 1)] = true;
                            $tabBoxAffected[($posX - 1).';'.($posY + $length)] = true;
                            $tabBoxAffected[($posX + 1).';'.($posY + $length)] = true;
                            $tabBoxAffected[$posX.';'.($posY + $length)] = true;
                        }
                        $tabBoxAffected[($posX - 1).';'.($posY + $i)] = true;
                        $tabBoxAffected[($posX + 1).';'.($posY + $i)] = true;

                        //Management boat boxes
                        $tabBoxAffected[$posX.';'.($posY + $i)] = true;
                    }
                }
                $tabObjectBoat[] = new Game($player, $key, $length, $posX.';'.$posY, $rotation, '', $room);     
            }
        }

        return $tabObjectBoat;
    }

    /**
     * Generate an aleatoire String
     * @return string
     */
    public static function generateRandomString(int $longueur = 25)
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
