<?php

namespace Zhineng\Checkpoint\Tencent\Result;

use Illuminate\Http\Client\Response;
use Zhineng\Checkpoint\Tencent\Checkpoint;
use Zhineng\Checkpoint\Tencent\Contracts\ResultCheckable;

class CheckResultViaEid implements ResultCheckable
{
    use ManagesFramesCount, ManagesSelection;

    public function get(string $token): Response
    {
        $payload = [
            'EidToken' => $token,
        ];

        if (! is_null($this->framesCount)) {
            $payload['BestFramesCount'] = $this->framesCount;
        }

        if (! empty($this->selection)) {
            $payload['InfoType'] = join('', $this->selection);
        }

        return Checkpoint::post('/', $payload, ['action' => 'GetEidResult']);
    }
}