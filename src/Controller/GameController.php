<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    /**
     * @Route("/game/{token}", name="game")
     */
    public function index(Request $request): Response
    {

        //dd($request->attributes->get('token'));
        
        return $this->render('game/index.html.twig', [
        ]);
    }
}
