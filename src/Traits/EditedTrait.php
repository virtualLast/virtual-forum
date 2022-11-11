<?php

namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;

#[ORM\HasLifecycleCallbacks]
trait EditedTrait
{
    #[ORM\Column(nullable: true)]
    private ?bool $edited = null;

    public function isEdited(): ?bool
    {
        return $this->edited;
    }

    #[ORM\PrePersist]
    public function setEdited(?bool $edited): self
    {
        $this->edited = $edited ?? false;

        return $this;
    }
}
