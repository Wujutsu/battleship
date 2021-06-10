<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GameRepository::class)
 */
class Game
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $player;

    /**
     * @ORM\Column(type="integer")
     */
    private $boat;

    /**
     * @ORM\Column(type="integer")
     */
    private $length;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private $position;

    /**
     * @ORM\Column(type="boolean")
     */
    private $rotation;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $touch;

    /**
     * @ORM\ManyToOne(targetEntity=Room::class, inversedBy="games")
     * @ORM\JoinColumn(nullable=false)
     */
    private $room;

    public function __construct($player, $boat, $length, $position, $rotation, $touch, $room)
    {
        $this->player = $player;
        $this->boat = $boat;
        $this->length = $length;
        $this->position = $position;
        $this->rotation = $rotation;
        $this->touch = $touch;
        $this->room = $room;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlayer(): ?bool
    {
        return $this->player;
    }

    public function setPlayer(bool $player): self
    {
        $this->player = $player;

        return $this;
    }

    public function getBoat(): ?int
    {
        return $this->boat;
    }

    public function setBoat(int $boat): self
    {
        $this->boat = $boat;

        return $this;
    }

    public function getLength(): ?int
    {
        return $this->length;
    }

    public function setLength(int $length): self
    {
        $this->length = $length;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(string $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getRotation(): ?bool
    {
        return $this->rotation;
    }

    public function setRotation(bool $rotation): self
    {
        $this->rotation = $rotation;

        return $this;
    }

    public function getTouch(): ?string
    {
        return $this->touch;
    }

    public function setTouch(string $touch): self
    {
        $this->touch = $touch;

        return $this;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): self
    {
        $this->room = $room;

        return $this;
    }
}
