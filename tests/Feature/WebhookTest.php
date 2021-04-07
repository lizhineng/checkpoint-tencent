<?php

namespace Zhineng\Checkpoint\Tencent\Tests\Feature;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Zhineng\Checkpoint\Tencent\Channel;
use Zhineng\Checkpoint\Tencent\Gender;
use Zhineng\Checkpoint\Tencent\IdentityVerification;

class WebhookTest extends FeatureTestCase
{
    use FakeResponse;

    public function test_gracefully_handle_webhook_without_token()
    {
        $this->get('/checkpoint/webhook')->assertOk();
    }

    /**
     * @dataProvider provides_identity_verification_responses_via_wechat_web
     */
    public function test_it_can_handle_an_identity_verified_event(array $response, array $assertion, bool $missing = false)
    {
        Storage::fake();

        Http::fake([
            '*' => Http::response($response),
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

        $assertionMethod = $missing ? 'assertDatabaseMissing' : 'assertDatabaseHas';

        $this->{$assertionMethod}('identity_verifications', array_merge([
            'identifiable_id' => $user->getKey(),
            'identifiable_type' => $user->getMorphClass(),
        ], $assertion));
    }

    public function provides_identity_verification_responses_via_wechat_web(): array
    {
        return [
            'passed verification data returned' => [
                $this->successResponse(),
                [
                    'name' => 'Zhineng',
                    'id_number' => '000',
                    'status' => IdentityVerification::STATUS_PASSED,
                    'token' => 'foo',
                    'channel' => Channel::WECHAT_WEB,
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
                        'id' => 'foo',
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
                ],
            ],
            'passed verification data returned, ocr excluded' => [
                $this->successResponseWithoutOcr(),
                [
                    'name' => 'Zhineng',
                    'id_number' => '000',
                    'status' => IdentityVerification::STATUS_PASSED,
                    'token' => 'foo',
                    'channel' => Channel::WECHAT_WEB,
                    'ocr' => json_encode([
                        'name' => '',
                        'id_number' => '',
                        'gender' => null,
                        'ethnic_group' => null,
                        'address' => null,
                        'issued_by' => null,
                        'issued_on' => null,
                        'expired_on' => null,
                        'date_of_birth' => null,
                    ]),
                    'evaluations' => json_encode([[
                        'timestamp' => '0',
                        'id' => 'foo',
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
                ],
            ],
            'failed verification data returned' => [
                $this->failedResponse(),
                [
                    'name' => 'Zhineng',
                    'id_number' => '000',
                    'status' => IdentityVerification::STATUS_FAILED,
                    'token' => 'foo',
                    'channel' => Channel::WECHAT_WEB,
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
                        'id' => 'foo',
                        'name' => 'Zhineng',
                        'id_number' => '000',
                        'similarity' => null,
                        'is_charged' => null,
                        'error_code' => 1301,
                        'error_message' => '活体检测没通过，请重试',
                        'evaluation_status' => 1301,
                        'evaluation_message' => '活体检测没通过，请重试',
                        'comparison_status' => null,
                        'comparison_message' => null,
                        'comparison_library' => '',
                    ]]),
                ]
            ],
            'awaiting verification data' => [
                $this->awaitingVerificationResponse(),
                [],
                $assertMissing = true,
            ],
            'error response' => [
                $this->expiredTokenResponse(),
                [],
                $assertMissing = true,
            ],
        ];
    }
}