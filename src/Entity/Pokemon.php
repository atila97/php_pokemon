<?php

namespace App\Entity;


use App\Repository\PokemonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use JsonSerializable;
use Symfony\Component\Serializer\Annotation\Groups;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @ApiResource(
 *     paginationClientItemsPerPage=true,
 *     normalizationContext={"groups"={"pokemon:read"}},
 *     denormalizationContext={"groups"={"pokemon:write"}},
 *     collectionOperations={
 *          "get"={
 *              "pagination_items_per_page"=50
 *          }
 *      },
 *     itemOperations={
 *      "get",
 *      "put"={
 *          "method"="PUT",
 *          "controller"=ApiController::class,
 *      },
 *      "delete"={
 *          "method"="DELETE",
 *          "controller"=ApiController::class,
 *      }
 *     },
 * )
 * @ApiFilter(BooleanFilter::class, properties={"legendary"})
 * @ApiFilter(SearchFilter::class, properties={"type": "exact", "name": "partial", "generation"})
 * 
 * @ORM\Entity(repositoryClass=PokemonRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Pokemon implements JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id", type="integer")
     * @Groups({"pokemon:read", "pokemon:write"})
     */
    protected ?int $id = null;

    /**
     * @ORM\Column(type="string", length=100)
     * @Groups({"pokemon:read", "pokemon:write"})
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"pokemon:read", "pokemon:write"})
     */
    private $number;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"pokemon:read", "pokemon:write"})
     */
    private $hp;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"pokemon:read", "pokemon:write"})
     */
    private $attack;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"pokemon:read", "pokemon:write"})
     */
    private $defense;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"pokemon:read", "pokemon:write"})
     */
    private $spAtk;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"pokemon:read", "pokemon:write"})
     */
    private $spDef;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"pokemon:read", "pokemon:write"})
     */
    private $speed;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"pokemon:read", "pokemon:write"})
     */
    private $legendary;

    /**
     * @ORM\ManyToMany(targetEntity=PokemonType::class, inversedBy="pokemon")
     * @MaxDepth(1)
     * @Groups({"pokemon:read", "pokemon:write"})
     */
    private $type;

    /**
     * @ORM\Column(type="integer")
     *  @Groups({"pokemon:read", "pokemon:write"})
     */
    private $generation;

    public function __construct()
    {
        $this->type = new ArrayCollection();
    }


    public static function fromCsv(array $row) : self {
        return (new Self())
            ->setName($row["Name"])
            ->setNumber($row["#"])
            ->setHp($row["HP"])
            ->setAttack($row["Attack"])
            ->setDefense($row["Defense"])
            ->setSpAtk($row["Sp. Atk"])
            ->setSpDef($row["Sp. Def"])
            ->setSpeed($row["Speed"])
            ->setGeneration($row["Generation"])
            ->setLegendary($row["Legendary"] === "True")
        ;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        if (null === $name || empty($name)) return $this;
        $this->name = $name;

        return $this;
    }

    public function getTotal(): ?int
    {
        return $this->hp + $this->attack + $this->defense + $this->speed + $this->spAtk + $this->spDef;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getHp(): ?int
    {
        return $this->hp;
    }

    public function setHp(int $hp): self
    {
        $this->hp = $hp;

        return $this;
    }

    public function getDefense(): ?int
    {
        return $this->defense;
    }

    public function setDefense(int $defense): self
    {
        $this->defense = $defense;

        return $this;
    }

    public function getAttack(): ?int
    {
        return $this->attack;
    }

    public function setAttack(int $attack): self
    {
        $this->attack = $attack;

        return $this;
    }

    public function getSpAtk(): ?int
    {
        return $this->spAtk;
    }

    public function setSpAtk(int $spAtk): self
    {
        $this->spAtk = $spAtk;

        return $this;
    }

    public function getSpDef(): ?int
    {
        return $this->spDef;
    }

    public function setSpDef(int $spDef): self
    {
        $this->spDef = $spDef;

        return $this;
    }

    public function getSpeed(): ?int
    {
        return $this->speed;
    }

    public function setSpeed(int $speed): self
    {
        $this->speed = $speed;

        return $this;
    }

    public function getLegendary(): ?bool
    {
        return $this->legendary;
    }

    public function setLegendary(?bool $legendary): self
    {
        if (null === $legendary) return $this;
        $this->legendary = $legendary;

        return $this;
    }

    /**
     * @return Collection<int, PokemonType>
     */
    public function getType(): Collection
    {
        return $this->type;
    }

    public function setTypes($type1, $type2): self
    {
        $this->type = new ArrayCollection();
        $this->addType($type1);
        $this->addType($type2);
        return $this;
    }

    public function addType(?PokemonType $type): self
    {
        if ($type === null) return $this;
        if ($type->getId() === null) {
            return $this;
        }

        if (!$this->type->contains($type)) {
            $this->type[] = $type;
        }

        return $this;
    }

    public function removeType(PokemonType $type): self
    {
        $this->type->removeElement($type);

        return $this;
    }

    public function getGeneration(): ?int
    {
        return $this->generation;
    }

    public function setGeneration(?int $generation): self
    {
        if (null === $generation) return $this;
        $this->generation = $generation;

        return $this;
    }

    public function jsonSerialize() 
    {
        return [
            "id" => $this->id
        ];
    }
}
