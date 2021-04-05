<?php

namespace Zhineng\Checkpoint\Tencent\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Zhineng\Checkpoint\Tencent\Checkpoint;
use Zhineng\Checkpoint\Tencent\Exceptions\InvalidPassthroughPayload;
use Zhineng\Checkpoint\Tencent\IdentityVerification;

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
        $passthrough = json_decode($payload['Extra'], true);

        if (! is_array($passthrough)) {
            throw new InvalidPassthroughPayload;
        }

        $verification = $this->newIdentityVerification($payload['Extra']);

        $result = $this->findVerificationResultViaWeChat($payload['BizToken'], $passthrough['rule_id']);

        $verification->fill([
            'name' => $result->json('Response.Text.Name'),
            'id_number' => $result->json('Response.Text.IdCard'),
            'status' => IdentityVerification::STATUS_PASSED,
        ])->save();
    }

    protected function newIdentityVerification(string $passthrough): IdentityVerification
    {
        $passthrough = json_decode($passthrough, true);

        if (! is_array($passthrough) || ! isset($passthrough['identifiable_id'], $passthrough['identifiable_type'])) {
            throw new InvalidPassthroughPayload;
        }

        return Checkpoint::$identityVerificationModel::newModelInstance([
            'identifiable_id' => $passthrough['identifiable_id'],
            'identifiable_type' => $passthrough['identifiable_type'],
        ]);
    }

    protected function findVerificationResultViaWeChat(string $token, string $ruleId)
    {
        return Checkpoint::post('/', [
            'RuleId' => $ruleId,
            'BizToken' => $token,
        ], ['action' => 'GetDetectInfoEnhanced']);
    }
}