<?php

namespace Zhineng\Checkpoint\Tencent\Result;

trait ManagesFramesCount
{
    protected ?int $framesCount = null;

    public function framesCount(int $count): self
    {
        $this->framesCount = $count;

        return $this;
    }
}
