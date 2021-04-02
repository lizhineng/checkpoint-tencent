<?php

namespace Zhineng\Checkpoint\Tencent\Channels;

use Illuminate\Database\Eloquent\Model;
use Zhineng\Checkpoint\Tencent\Checkpoint;

class IdentityVerificationViaWeChat
{
    protected ?string $name = null;

    protected ?string $idNumber = null;

    protected array $metadata = [];

    protected ?string $redirect = null;

    public function __construct(
        protected Model $identifiable,
        protected int|string $ruleId
    ) {
        $this->ruleId = (string) $this->ruleId;
    }

    public function checkFor(string $name, string $idNumber): self
    {
        $this->name = $name;
        $this->idNumber = $idNumber;

        return $this;
    }

    public function withMetadata(array $metadata): self
    {
        $this->metadata = array_merge($this->metadata, $metadata);

        return $this;
    }

    public function returnTo(string $url): self
    {
        $this->redirect = $url;

        return $this;
    }

    public function request(): string
    {
        $payload = [
            'RuleId' => $this->ruleId,
        ];

        if ($this->name && $this->idNumber) {
            $payload['Name'] = $this->name;
            $payload['IdCard'] = $this->idNumber;
        }

        if ($this->redirect) {
            $payload['RedirectUrl'] = $this->redirect;
        }

        if (! empty($this->metadata)) {
            $payload['Extra'] = json_encode($this->metadata);
        }

        return Checkpoint::post('/', $payload, ['action' => 'DetectAuth'])['Response']['Url'];
    }
}