<?php

namespace Zhineng\Checkpoint\Tencent\Result;

trait ManagesSelection
{
    protected array $selection = [];

    public function select(...$selection): self
    {
        $this->selection = collect($selection)
            ->flatten()
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        return $this;
    }
}
