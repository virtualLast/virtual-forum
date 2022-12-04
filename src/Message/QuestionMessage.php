<?php

namespace App\Message;

final class QuestionMessage extends AbstractMessage
{
    public function __construct(int $id, array $context = [])
    {
        parent::__construct($id, $context);
    }
}
