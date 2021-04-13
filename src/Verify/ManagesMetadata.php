<?php

namespace Zhineng\Checkpoint\Tencent\Verify;

trait ManagesMetadata
{
    protected array $metadata = [];

    public function withMetadata(array $metadata): self
    {
        $this->metadata = array_merge($this->metadata, $metadata);

        return $this;
    }
}
