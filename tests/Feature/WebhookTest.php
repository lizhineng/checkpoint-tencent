<?php

namespace Zhineng\Checkpoint\Tencent\Tests\Feature;

use Illuminate\Support\Facades\Http;
use Zhineng\Checkpoint\Tencent\IdentityVerification;

class WebhookTest extends FeatureTestCase
{
    public function test_gracefully_handle_webhook_without_token()
    {
        $this->get('/checkpoint/webhook')->assertOk();
    }

    public function test_it_can_handle_an_identity_verified_event()
    {
        Http::fake([
            '*' => Http::response([
                'Response' => ['Text' => [
                    'ErrCode' => 0,
                    'IdCard' => '000',
                    'Name' => 'Zhineng',
                ]],
            ]),
        ]);

        $this->withoutExceptionHandling();

        $user = $this->createIdentifiable();

        $this->call('GET', '/checkpoint/webhook', [
            'BizToken' => 'foo',
            'Extra' => json_encode([
                'identifiable_id' => $user->getKey(),
                'identifiable_type' => $user->getMorphClass(),
                'rule_id' => 0,
            ]),
        ]);

        $this->assertDatabaseHas('identity_verifications', [
            'identifiable_id' => $user->getKey(),
            'identifiable_type' => $user->getMorphClass(),
            'name' => 'Zhineng',
            'id_number' => '000',
            'status' => IdentityVerification::STATUS_PASSED,
        ]);
    }
}