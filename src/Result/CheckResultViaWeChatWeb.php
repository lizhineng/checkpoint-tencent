<?php

namespace Zhineng\Checkpoint\Tencent\Result;

use Illuminate\Http\Client\Response;
use Zhineng\Checkpoint\Tencent\Checkpoint;
use Zhineng\Checkpoint\Tencent\Contracts\ResultCheckable;

class CheckResultViaWeChatWeb implements ResultCheckable
{
    use ManagesFramesCount, ManagesSelection;

    protected bool $clipIdCard = false;

    protected bool $clipPortrait = false;

    public function __construct(
        protected string $ruleId
    ) {
        //
    }

    public function clipIdCard(): self
    {
        $this->clipIdCard = true;

        return $this;
    }

    public function clipPortrait(): self
    {
        $this->clipPortrait = true;

        return $this;
    }

    public function get(string $token): Response
    {
        $payload = [
            'BizToken' => $token,
            'RuleId' => $this->ruleId,
            'IsCutIdCardImage' => $this->clipIdCard,
            'IsNeedIdCardAvatar' => $this->clipPortrait,
        ];

        if (! is_null($this->framesCount)) {
            $payload['BestFramesCount'] = $this->framesCount;
        }

        if (! empty($this->selection)) {
            $payload['InfoType'] = join('', $this->selection);
        }

        return Checkpoint::post('/', $payload, ['action' => 'GetDetectInfoEnhanced']);
    }
}