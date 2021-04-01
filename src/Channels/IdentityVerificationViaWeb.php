<?php

namespace Zhineng\Checkpoint\Tencent\Channels;

use Illuminate\Database\Eloquent\Model;

class IdentityVerificationViaWeb
{
    protected string $name;

    protected string $idNumber;

    protected array $payload = [];

    protected ?string $redirect = null;

    public function __construct(
        protected Model $identifiable,
        protected int $ruleId
    ) {
        //
    }

    public function checkFor(string $name, string $idNumber): self
    {
        $this->name = $name;
        $this->idNumber = $idNumber;

        return $this;
    }

    public function withPayload(array $payload): self
    {
        $this->payload = array_merge($this->payload, $payload);

        return $this;
    }

    public function returnTo(string $url): self
    {
        $this->redirect = $url;

        return $this;
    }

    public function request(): string
    {
        return 'https://';
    }
}