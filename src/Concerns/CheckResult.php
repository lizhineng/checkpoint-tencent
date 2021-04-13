<?php

namespace Zhineng\Checkpoint\Tencent\Concerns;

use Zhineng\Checkpoint\Tencent\Result\CheckResultViaEid;
use Zhineng\Checkpoint\Tencent\Result\CheckResultViaWeChatWeb;

trait CheckResult
{
    public static function resultViaWeChatWeb(int $ruleId): CheckResultViaWeChatWeb
    {
        return new CheckResultViaWeChatWeb($ruleId);
    }

    public static function resultViaEid(): CheckResultViaEid
    {
        return new CheckResultViaEid;
    }
}
