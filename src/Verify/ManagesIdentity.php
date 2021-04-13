<?php

namespace Zhineng\Checkpoint\Tencent\Verify;

trait ManagesIdentity
{
    protected ?string $name = null;

    protected ?string $idNumber = null;

    public function checkFor(string $name, string $idNumber): self
    {
        $this->name = $name;
        $this->idNumber = $idNumber;

        return $this;
    }
}
