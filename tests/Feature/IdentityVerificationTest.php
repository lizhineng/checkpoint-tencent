<?php

namespace Zhineng\Checkpoint\Tencent\Tests\Feature;

class IdentityVerificationTest extends FeatureTestCase
{
    public function test_identifiable_models_can_request_a_identity_verification()
    {
        $user = $this->createIdentifiable();

        $link = $user->newIdentityVerification()
            ->viaWeb(ruleId: 1)
            ->withPayload(['foo' => 'bar'])
            ->returnTo('https://foo.com/bar')
            ->request();

        $this->assertStringStartsWith($link, "https://open.weixin.qq.com/connect/oauth2/authorize");
    }
}
