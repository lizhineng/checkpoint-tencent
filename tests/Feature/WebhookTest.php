<?php

namespace Zhineng\Checkpoint\Tencent\Tests\Feature;

class WebhookTest extends FeatureTestCase
{
    public function test_gracefully_handle_webhook_without_token()
    {
        $this->get('/checkpoint/webhook')->assertOk();
    }
}