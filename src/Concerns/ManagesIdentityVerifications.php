<?php

namespace Zhineng\Checkpoint\Tencent\Concerns;

use Zhineng\Checkpoint\Tencent\IdentityVerificationBuilder;

trait ManagesIdentityVerifications
{
    public function newIdentityVerification()
    {
        return new IdentityVerificationBuilder($this);
    }
}
