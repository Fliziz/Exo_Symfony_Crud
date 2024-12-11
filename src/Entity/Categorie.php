<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    /**
     * @var Collection<int, Articles>
     */
    #[ORM\OneToMany(targetEntity: Articles::class, mappedBy: 'id_categorie')]
    private Collection $id_categorie;

    public function __construct()
    {
        $this->id_categorie = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, Articles>
     */
    public function getIdCategorie(): Collection
    {
        return $this->id_categorie;
    }

    public function addIdCategorie(Articles $idCategorie): static
    {
        if (!$this->id_categorie->contains($idCategorie)) {
            $this->id_categorie->add($idCategorie);
            $idCategorie->setIdCategorie($this);
        }

        return $this;
    }

    public function removeIdCategorie(Articles $idCategorie): static
    {
        if ($this->id_categorie->removeElement($idCategorie)) {
            // set the owning side to null (unless already changed)
            if ($idCategorie->getIdCategorie() === $this) {
                $idCategorie->setIdCategorie(null);
            }
        }

        return $this;
    }
}
