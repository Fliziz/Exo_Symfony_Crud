<?php

namespace App\Entity;

use App\Repository\ArticlesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticlesRepository::class)]
class Articles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    private ?string $contenu = null;

    #[ORM\ManyToOne(inversedBy: 'id_categorie')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $id_categorie = null;

    /**
     * @var Collection<int, Commentaire>
     */
    #[ORM\OneToMany(targetEntity: Commentaire::class, mappedBy: 'id_article')]
    private Collection $id_commentaires;

    public function __construct()
    {
        $this->id_commentaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getIdCategorie(): ?categorie
    {
        return $this->id_categorie;
    }

    public function setIdCategorie(?categorie $id_categorie): static
    {
        $this->id_categorie = $id_categorie;

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getIdCommentaires(): Collection
    {
        return $this->id_commentaires;
    }

    public function addIdCommentaire(Commentaire $idCommentaire): static
    {
        if (!$this->id_commentaires->contains($idCommentaire)) {
            $this->id_commentaires->add($idCommentaire);
            $idCommentaire->setIdArticle($this);
        }

        return $this;
    }

    public function removeIdCommentaire(Commentaire $idCommentaire): static
    {
        if ($this->id_commentaires->removeElement($idCommentaire)) {
            // set the owning side to null (unless already changed)
            if ($idCommentaire->getIdArticle() === $this) {
                $idCommentaire->setIdArticle(null);
            }
        }

        return $this;
    }
}
