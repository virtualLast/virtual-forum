<?php

namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\HasLifecycleCallbacks]
trait StatusTrait
{

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status ?? 'submitted';

        return $this;
    }

    #[ORM\PrePersist]
    public function setStatusValue()
    {
        $this->status = 'submitted';

        return $this;
    }
}
