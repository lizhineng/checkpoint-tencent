<?php

namespace Zhineng\Checkpoint\Tencent\Verify;

use Illuminate\Http\Client\Response;
use Zhineng\Checkpoint\Tencent\Checkpoint;

class VerifyViaWeChatWeb
{
    use ManagesIdentity, ManagesMetadata;

    protected ?string $returnTo = null;

    public function __construct(
        protected string $ruleId
    ) {
        //
    }

    public function returnTo(string $url): self
    {
        $this->returnTo = $url;

        return $this;
    }

    public function request(): Response
    {
        $payload = [
            'RuleId' => $this->ruleId,
        ];

        if ($this->name && $this->idNumber) {
            $payload['Name'] = $this->name;
            $payload['IdCard'] = $this->idNumber;
        }

        if ($this->returnTo) {
            $payload['RedirectUrl'] = $this->returnTo;
        }

        if (! empty($this->metadata)) {
            $payload['Extra'] = json_encode($this->metadata);
        }

        return Checkpoint::post('/', $payload, ['action' => 'DetectAuth']);
    }
}