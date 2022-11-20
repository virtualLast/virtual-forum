<?php

namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;

#[ORM\HasLifecycleCallbacks]
trait StatusTrait
{

    #[ORM\Column(type: 'string', length: 255, options: ["default" => "submitted"])]
    private ?string $status = 'submitted';

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
