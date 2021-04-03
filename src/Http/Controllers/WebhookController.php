<?php

namespace Zhineng\Checkpoint\Tencent\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Zhineng\Checkpoint\Tencent\Exceptions\InvalidPassthroughPayload;

class WebhookController
{
    public function __invoke(Request $request)
    {
        $payload = $request->all();

        if (! isset($payload['BizToken'])) {
            return new Response();
        }

        $this->handleIdentityVerifiedViaWeChat($payload);

        return new Response();
    }

    protected function handleIdentityVerifiedViaWeChat(array $payload)
    {
        $passthrough = json_decode($payload['Extra']);

        if (! is_array($passthrough)) {
            throw new InvalidPassthroughPayload;
        }
    }
}