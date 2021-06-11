<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RoomRepository::class)
 */
class Room
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Token;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $ip;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $ip_bis;

    /**
     * @ORM\OneToMany(targetEntity=Game::class, mappedBy="room", orphanRemoval=true)
     */
    private $games;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="text")
     */
    private $cellule_player_one;

    /**
     * @ORM\Column(type="text")
     */
    private $cellule_player_two;

    public function __construct()
    {
        $this->games = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->Token;
    }

    public function setToken(string $Token): self
    {
        $this->Token = $Token;

        return $this;
    }
    
    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getIpBis(): ?string
    {
        return $this->ip_bis;
    }

    public function setIpBis(?string $ip_bis): self
    {
        $this->ip_bis = $ip_bis;

        return $this;
    }

    /**
     * @return Collection|Game[]
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): self
    {
        if (!$this->games->contains($game)) {
            $this->games[] = $game;
            $game->setRoom($this);
        }

        return $this;
    }

    public function removeGame(Game $game): self
    {
        if ($this->games->removeElement($game)) {
            // set the owning side to null (unless already changed)
            if ($game->getRoom() === $this) {
                $game->setRoom(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getCellulePlayerOne(): ?string
    {
        return $this->cellule_player_one;
    }

    public function setCellulePlayerOne(string $cellule_player_one): self
    {
        $this->cellule_player_one = $cellule_player_one;

        return $this;
    }

    public function addCellulePlayerOne(string $cellule_player_one): self
    {
        $this->cellule_player_one .= $cellule_player_one.';';

        return $this;
    }

    public function getCellulePlayerTwo(): ?string
    {
        return $this->cellule_player_two;
    }

    public function setCellulePlayerTwo(string $cellule_player_two): self
    {
        $this->cellule_player_two = $cellule_player_two;

        return $this;
    }

    public function addCellulePlayerTwo(string $cellule_player_two): self
    {
        $this->cellule_player_two .= $cellule_player_two.';';

        return $this;
    }
}
