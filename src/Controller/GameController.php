<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Room;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    /**
     * @Route("/game", name="game_redirect")
     */
    public function gameRedirect()
    {
        return $this->redirectToRoute('home');
    }

    /**
     * PlayerOne
     * @Route("/game/{token}", name="game_playerOne")
     */
    public function indexPlayerOne(Request $request): Response
    {
        $room = $this->getDoctrine()->getRepository(Room::class)->findOneBy(['Token' => $request->attributes->get('token')]);
        if (!$room)
            return $this->redirectToRoute('home');

        $game = $this->getDoctrine()->getRepository(Game::class)->findBy(['room' => $room]);
        $boatsPlayer = GameController::creatingViewCelluleOfMapPlayer($game, 0);
        $boatsPlayerEnemy = GameController::creatingViewCelluleOfMapPlayer($game, 1);
        $celluleTouch = GameController::showCelluleTouch($room, 0);

        return $this->render('game/index.html.twig', [
            "player" => 0,
            "room" => $room,
            "boatsPlayer" => $boatsPlayer,
            "boatsPlayerEnemy" => $boatsPlayerEnemy,
            "celluleTouch" => $celluleTouch
        ]);
    }

    /**
     * PlayerTwo
     * @Route("/game/guest/{token}", name="game_playerTwo")
     */
    public function indexPlayerTwo(Request $request): Response
    {
        $room = $this->getDoctrine()->getRepository(Room::class)->findOneBy(['Token' => $request->attributes->get('token'), 'ip_bis' => ['', $request->getClientIp()]]);
        if (!$room) {
            return $this->redirectToRoute('home');

        } else if ($room->getIpBis() == '') {
            $entityManager = $this->getDoctrine()->getManager();
            $roomUpdate = $entityManager->getRepository(Room::class)->find($room->getId());
            
            if ($roomUpdate) {
                $roomUpdate->setIpBis($request->getClientIp());
                $entityManager->flush();
            }
        }

        $game = $this->getDoctrine()->getRepository(Game::class)->findBy(['room' => $room]);
        $boatsPlayer = GameController::creatingViewCelluleOfMapPlayer($game, 1);
        $boatsPlayerEnemy = GameController::creatingViewCelluleOfMapPlayer($game, 0);
        $celluleTouch = GameController::showCelluleTouch($room, 1);

        return $this->render('game/index.html.twig', [
            "player" => 1,
            "room" => $room,
            "boatsPlayer" => $boatsPlayer,
            "boatsPlayerEnemy" => $boatsPlayerEnemy,
            "celluleTouch" => $celluleTouch
        ]);
    }

    /**
     * @Route("/ajax/touchCellule", name="ajax_touch_cellule")
     */
    public function ajaxAddCellule(Request $request)
    {
        if ($request->isXmlHttpRequest() || $request->query->get('showJson') == 1) {  
            //Recovery variables
            $token = $request->request->get('token');
            $player = $request->request->get('player');
            $cellule = $request->request->get('cellule');

            $room = $this->getDoctrine()->getRepository(Room::class)->findOneBy(['Token' => $token]);
            if ($room) {

                //Show all cellule boat enemy
                $game = $this->getDoctrine()->getRepository(Game::class)->findBy(['room' => $room]);
                $player == 0 ? $boatsCelluleEnemy = GameController::creatingViewCelluleOfMapPlayer($game, 1) : $boatsCelluleEnemy = GameController::creatingViewCelluleOfMapPlayer($game, 0);
                //If cellule touch are a boat
                //if (isset($boatsCelluleEnemy[$cellule]))
                    

                //Update cellule touch
                $entityManager = $this->getDoctrine()->getManager();
                $roomUpdate = $entityManager->getRepository(Room::class)->find($room->getId());
                
                if ($roomUpdate) {
                    if ($player == 0) {
                        $roomUpdate->addCellulePlayerOne($cellule);
                    } else {
                        $roomUpdate->addCellulePlayerTwo($cellule);
                    }
                    $entityManager->flush();
                }
            }

            return new JsonResponse("");
        } else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * Creating the view of map player (show cellule boat) (10 x 10)
     */
    public static function creatingViewCelluleOfMapPlayer($game, bool $player)
    {
        $celluleBoat = array();
        foreach ($game as $game) {
            if ($player == $game->getPlayer()) {
                $position = explode(';', $game->getPosition());
                $posX = $position[0];
                $posY = $position[1];
    
                for ($i = 0; $i < $game->getLength(); $i++) {
                    $game->getRotation() == 0 ? $celluleBoat[($posX + $i) + (($posY - 1) * 10)] = true : $celluleBoat[$posX + ((($posY + $i) - 1) * 10)] = true;
                }
            }
        }

        return $celluleBoat;
    }

    /**
     * Show all cellules played on enemy map 
     * @return array
     */
    public static function showCelluleTouch(Room $room, bool $player)
    {
        $celluleTouch = array();

        $player == 0 ? $tabTemp = explode(';', $room->getCellulePlayerOne()) : $tabTemp = explode(';', $room->getCellulePlayerTwo());
        foreach ($tabTemp as $var) {
            if ($var != "")
                $celluleTouch[$var] = true;
        }

        return $celluleTouch;
    }
}
