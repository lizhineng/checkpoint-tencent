<?php

namespace Zhineng\Checkpoint\Tencent\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Zhineng\Checkpoint\Tencent\Checkpoint;
use Zhineng\Checkpoint\Tencent\Result\Select;

class ResultTest extends FeatureTestCase
{
    use RefreshDatabase;

    /**
     * @dataProvider provides_result_querying_via_wechat_web
     */
    public function test_get_identity_verification_result_via_wechat_web($querying, $assertion)
    {
        Http::fake();

        $querying();

        Http::assertSent($assertion);
    }

    public function test_get_identity_verification_result_via_eid()
    {
        Http::fake();

        Checkpoint::resultViaEid()->get(token: 'foo');

        Http::assertSent(function (Request $request) {
            return $request['EidToken'] === 'foo';
        });
    }

    public function provides_result_querying_via_wechat_web(): array
    {
        return [
            'minimal request payload' => [
                fn () => Checkpoint::resultViaWeChatWeb(ruleId: 0)->get(token: 'foo'),
                fn (Request $request) => $request['RuleId'] === '0' && $request['BizToken'] === 'foo',
            ],

            'custom frames count' => [
                fn () => Checkpoint::resultViaWeChatWeb(ruleId: 0)->framesCount(3)->get(token: 'foo'),
                fn (Request $request) => $request['BestFramesCount'] === 3,
            ],

            'clipping id card request' => [
                fn () => Checkpoint::resultViaWeChatWeb(ruleId: 0)->clipIdCard()->get(token: 'foo'),
                fn (Request $request) => $request['IsCutIdCardImage'] === true,
            ],

            'clipping portrait request' => [
                fn () => Checkpoint::resultViaWeChatWeb(ruleId: 0)->clipPortrait()->get(token: 'foo'),
                fn (Request $request) => $request['IsNeedIdCardAvatar'] === true,
            ],

            'custom selection' => [
                fn () => Checkpoint::resultViaWeChatWeb(ruleId: 0)
                    ->select([Select::TEXT, Select::FRAMES])
                    ->get(token: 'foo'),

                fn (Request $request) => $request['InfoType'] === Select::TEXT.Select::FRAMES,
            ],

            'custom selection, expects sorting' => [
                fn () => Checkpoint::resultViaWeChatWeb(ruleId: 0)
                    ->select([Select::FRAMES, Select::TEXT])
                    ->get(token: 'foo'),

                fn (Request $request) => $request['InfoType'] === Select::TEXT.Select::FRAMES,
            ],

            'custom selection, expects unique filtering' => [
                fn () => Checkpoint::resultViaWeChatWeb(ruleId: 0)
                    ->select([Select::TEXT, Select::TEXT])
                    ->get(token: 'foo'),

                fn (Request $request) => $request['InfoType'] === (string) Select::TEXT,
            ],
        ];
    }
}
