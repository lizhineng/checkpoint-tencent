<?php

namespace Zhineng\Checkpoint\Tencent\Concerns;

use Zhineng\Checkpoint\Tencent\Verify\VerifyViaEid;
use Zhineng\Checkpoint\Tencent\Verify\VerifyViaWeChatWeb;

trait CreateVerification
{
    public static function verifyViaWeChatWeb(int $ruleId): VerifyViaWeChatWeb
    {
        return new VerifyViaWeChatWeb($ruleId);
    }

    public static function verifyViaEid(string $merchantId): VerifyViaEid
    {
        return new VerifyViaEid($merchantId);
    }
}
