<?php

namespace Zhineng\Checkpoint\Tencent\Verify;

use Illuminate\Http\Client\Response;
use Zhineng\Checkpoint\Tencent\Checkpoint;
use Zhineng\Checkpoint\Tencent\Contracts\Verifiable;

class VerifyViaEid implements Verifiable
{
    use ManagesIdentity, ManagesMetadata;

    protected array $config = [
        'InputType' => EidConfig::INPUT_TYPE_OCR,
    ];

    public function __construct(
        protected string $merchantId
    ) {
        //
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

    public function usingIdentityFromUser(): self
    {
        $this->config['InputType'] = EidConfig::INPUT_TYPE_USING_IDENTITY_FROM_USER;

        return $this;
    }

    public function usingIdentityFromCreation(string $name, string $idNumber): self
    {
        $this->config['InputType'] = EidConfig::INPUT_TYPE_USING_IDENTITY_FROM_CREATION;

        $this->checkFor($name, $idNumber);

        return $this;
    }

    public function request(): Response
    {
        $payload = [
            'MerchantId' => $this->merchantId,
            'Config' => $this->config,
        ];

        if ($this->name && $this->idNumber) {
            $payload['Name'] = $this->name;
            $payload['IdCard'] = $this->idNumber;
        }

        if (! empty($this->metadata)) {
            $payload['Extra'] = json_encode($this->metadata);
        }

        return Checkpoint::post('/', $payload, ['action' => 'GetEidToken']);
    }
}
