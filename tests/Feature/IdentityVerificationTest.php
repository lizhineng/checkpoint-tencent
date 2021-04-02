<?php

namespace Zhineng\Checkpoint\Tencent\Tests\Feature;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

class IdentityVerificationTest extends FeatureTestCase
{
    public function test_identifiable_models_can_request_a_identity_verification()
    {
        Http::fake([
            'tencentcloudapi.com/*' => Http::response(['Response' => ['Url' => '']]),
        ]);

        $this->createIdentifiable()
            ->newIdentityVerification()
            ->viaWeChat(ruleId: 0)
            ->checkFor('Zhineng', '000')
            ->request();

        Http::assertSent(function (Request $request) {
            return $request['RuleId'] === '0' &&
                $request['Name'] === 'Zhineng' &&
                $request['IdCard'] === '000';
        });
    }
}
