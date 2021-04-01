<?php

namespace Zhineng\Checkpoint\Tencent;

use Illuminate\Database\Eloquent\Model;
use Zhineng\Checkpoint\Tencent\Channels\IdentityVerificationViaWeb;

class IdentityVerificationBuilder
{
    public function __construct(
        protected Model $identifiable,
    ) {
        //
    }

    public function viaWeb(int $ruleId): IdentityVerificationViaWeb
    {
        return new IdentityVerificationViaWeb($this->identifiable, $ruleId);
    }
}