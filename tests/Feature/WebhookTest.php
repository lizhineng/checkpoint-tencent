<?php

namespace Zhineng\Checkpoint\Tencent\Tests\Feature;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Zhineng\Checkpoint\Tencent\Gender;
use Zhineng\Checkpoint\Tencent\IdentityVerification;

class WebhookTest extends FeatureTestCase
{
    use FakeResponse;

    public function test_gracefully_handle_webhook_without_token()
    {
        $this->get('/checkpoint/webhook')->assertOk();
    }

    public function test_it_can_handle_an_identity_verified_event()
    {
        Storage::fake();

        Http::fake([
            '*' => Http::response($this->successResponse()),
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
            'ocr' => json_encode([
                'name' => 'Zhineng',
                'id_number' => '000',
                'gender' => GENDER::MALE,
                'ethnic_group' => 'foo',
                'address' => 'bar',
                'issued_by' => 'baz',
                'issued_on' => '2021-01-01',
                'expired_on' => '2031-01-01',
                'date_of_birth' => '2021-01-01',
            ]),
            'evaluations' => json_encode([[
                'timestamp' => '0',
                'request_id' => 'foo',
                'name' => 'Zhineng',
                'id_number' => '000',
                'similarity' => '100.00',
                'is_charged' => true,
                'error_code' => 0,
                'error_message' => '成功',
                'evaluation_status' => 0,
                'evaluation_message' => '成功',
                'comparison_status' => 0,
                'comparison_message' => '成功',
                'comparison_library' => 'bar',
            ]]),
        ]);
    }
}