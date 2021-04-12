<?php

namespace Zhineng\Checkpoint\Tencent\Channels;

use Illuminate\Database\Eloquent\Model;
use Zhineng\Checkpoint\Tencent\Checkpoint;

class IdentityVerificationViaEid
{
    protected ?string $name = null;

    protected ?string $idNumber = null;

    protected array $metadata = [];

    protected array $config = [
        'InputType' => EidConfig::INPUT_TYPE_OCR,
    ];

    public function __construct(
        protected Model $identifiable,
        protected string $merchantId
    ) {
        //
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

    public function ocr(): self
    {
        $this->config['InputType'] = EidConfig::INPUT_TYPE_OCR;

        return $this;
    }

    public function ocrIdCardFrontSideOnly(): self
    {
        $this->config['InputType'] = EidConfig::INPUT_TYPE_OCR_ID_CARD_FRONT_SIDE_ONLY;

        return $this;
    }

    public function acceptsIdentityFromUser(): self
    {
        $this->config['InputType'] = EidConfig::INPUT_TYPE_ACCEPTS_IDENTITY_FROM_USER;

        return $this;
    }

    public function usingIdentityFromCreation(): self
    {
        $this->config['InputType'] = EidConfig::INPUT_TYPE_USING_IDENTITY_FROM_CREATION;

        return $this;
    }

    public function request()
    {
        $payload = [
            'MerchantId' => $this->merchantId,
            'Config' => json_encode($this->config),
        ];

        if ($this->name && $this->idNumber) {
            $payload['Name'] = $this->name;
            $payload['IdCard'] = $this->idNumber;
        }

        if (! empty($this->metadata)) {
            $payload['Extra'] = json_encode($this->metadata);
        }

        return Checkpoint::post('/', $payload, ['action' => 'GetEidToken'])['Response']['EidToken'];
    }
}