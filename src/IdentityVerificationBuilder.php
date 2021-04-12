<?php

namespace Zhineng\Checkpoint\Tencent;

use Illuminate\Database\Eloquent\Model;
use Zhineng\Checkpoint\Tencent\Channels\IdentityVerificationViaEid;
use Zhineng\Checkpoint\Tencent\Channels\IdentityVerificationViaWeChat;

class IdentityVerificationBuilder
{
    public function __construct(
        protected Model $identifiable,
    ) {
        //
    }

    public function viaWeChatWeb(int $ruleId): IdentityVerificationViaWeChat
    {
        return new IdentityVerificationViaWeChat($this->identifiable, $ruleId);
    }

    public function viaEid(string $merchantId): IdentityVerificationViaEid
    {
        return new IdentityVerificationViaEid($this->identifiable, $merchantId);
    }
}
