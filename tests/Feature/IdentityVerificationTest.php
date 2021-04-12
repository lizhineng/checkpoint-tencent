<?php

namespace Zhineng\Checkpoint\Tencent\Tests\Feature;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Zhineng\Checkpoint\Tencent\Channels\EidConfig;

class IdentityVerificationTest extends FeatureTestCase
{
    public function test_identifiable_models_can_request_an_identity_verification_via_wechat_web()
    {
        Http::fake([
            'tencentcloudapi.com/*' => Http::response(['Response' => ['Url' => '']]),
        ]);

        $this->createIdentifiable()
            ->newIdentityVerification()
            ->viaWeChatWeb(ruleId: 0)
            ->checkFor('Zhineng', '000')
            ->request();

        Http::assertSent(function (Request $request) {
            return $request['RuleId'] === '0' &&
                $request['Name'] === 'Zhineng' &&
                $request['IdCard'] === '000';
        });
    }

    public function test_identifiable_models_can_request_an_identity_verification_via_eid()
    {
        Http::fake([
            'tencentcloudapi.com/*' => Http::response(['Response' => ['EidToken' => '']]),
        ]);

        $this->createIdentifiable()
            ->newIdentityVerification()
            ->viaEid(merchantId: 'foo')
            ->checkFor('Zhineng', '000')
            ->request();

        Http::assertSent(function (Request $request) {
            return $request['MerchantId'] === 'foo' &&
                $request['Name'] === 'Zhineng' &&
                $request['IdCard'] === '000' &&
                $request['Config'] === json_encode(['InputType' => EidConfig::INPUT_TYPE_OCR]);
        });
    }
}
