<?php

namespace Zhineng\Checkpoint\Tencent\Tests;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Zhineng\Checkpoint\Tencent\Checkpoint;
use Zhineng\Checkpoint\Tencent\Verify\EidConfig;

class VerifyTest extends FeatureTestCase
{
    /**
     * @dataProvider provides_requests_via_wechat_web
     */
    public function test_request_an_identity_verification_via_wechat_web($request, $assertion)
    {
        Http::fake();

        $request();

        Http::assertSent($assertion);
    }

    /**
     * @dataProvider provides_requests_via_eid
     */
    public function test_request_an_identity_verification_via_eid($request, $assertion)
    {
        Http::fake();

        $request();

        Http::assertSent($assertion);
    }

    public function provides_requests_via_wechat_web(): array
    {
        return [
            'minimal request payload' => [
                fn () => Checkpoint::verifyViaWeChatWeb(ruleId: 0)->request(),
                fn (Request $request) => $request['RuleId'] === '0',
            ],

            'return url' => [
                fn () => Checkpoint::verifyViaWeChatWeb(ruleId: 0)->returnTo('http://your-callback-url')->request(),
                fn (Request $request) => $request['RedirectUrl'] === 'http://your-callback-url',
            ],

            'preset identity' => [
                fn () => Checkpoint::verifyViaWeChatWeb(ruleId: 0)
                    ->checkFor('Zhineng', '000')
                    ->request(),

                fn (Request $request) => $request['Name'] === 'Zhineng' && $request['IdCard'] === '000',
            ],

            'custom metadata' => [
                fn () => Checkpoint::verifyViaWeChatWeb(ruleId: 0)
                    ->withMetadata(['foo' => 'bar'])
                    ->request(),

                fn (Request $request) => $request['Extra'] === json_encode(['foo' => 'bar']),
            ],
        ];
    }

    public function provides_requests_via_eid(): array
    {
        return [
            'minimal request payload' => [
                fn () => Checkpoint::verifyViaEid(merchantId: 'foo')->request(),

                fn (Request $request) => $request['MerchantId'] === 'foo'
                    && $request['Config'] === ['InputType' => EidConfig::INPUT_TYPE_OCR],
            ],

            'ocr id card front side only' => [
                fn () => Checkpoint::verifyViaEid(merchantId: 'foo')->ocrIdCardFrontSideOnly()->request(),

                fn (Request $request) => $request['Config'] === ['InputType' => EidConfig::INPUT_TYPE_OCR_ID_CARD_FRONT_SIDE_ONLY],
            ],

            'using identity from user' => [
                fn () => Checkpoint::verifyViaEid(merchantId: 'foo')->usingIdentityFromUser()->request(),

                fn (Request $request) => $request['Config'] === ['InputType' => EidConfig::INPUT_TYPE_USING_IDENTITY_FROM_USER],
            ],

            'using identity from creation' => [
                fn () => Checkpoint::verifyViaEid(merchantId: 'foo')
                    ->usingIdentityFromCreation('Zhineng', '000')
                    ->request(),

                fn (Request $request) => $request['Config'] === ['InputType' => EidConfig::INPUT_TYPE_USING_IDENTITY_FROM_CREATION],
            ],
        ];
    }
}
