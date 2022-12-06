<?php

namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;

#[ORM\HasLifecycleCallbacks]
trait VotingTrait
{
    #[ORM\Column(nullable: true)]
    private ?int $voteUp = null;

    #[ORM\Column(nullable: true)]
    private ?int $voteDown = null;

    public function getVoteUp(): ?int
    {
        return $this->voteUp;
    }

    public function setVoteUp(?int $voteUp): self
    {
        $this->voteUp = $voteUp ?? 0;

        return $this;
    }

    #[ORM\PrePersist]
    public function setVoteUpValue(): self
    {
        $this->voteUp = 0;

        return $this;
    }

    public function getVoteDown(): ?int
    {
        return $this->voteDown;
    }

    public function setVoteDown(?int $voteDown): self
    {
        $this->voteDown = $voteDown ?? 0;

        return $this;
    }

    #[ORM\PrePersist]
    public function setVoteDownValue(): self
    {
        $this->voteDown = 0;

        return $this;
    }
}
