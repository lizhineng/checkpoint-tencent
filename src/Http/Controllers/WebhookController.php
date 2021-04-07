<?php

namespace Zhineng\Checkpoint\Tencent\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Zhineng\Checkpoint\Tencent\Channel;
use Zhineng\Checkpoint\Tencent\Checkpoint;
use Zhineng\Checkpoint\Tencent\Exceptions\InvalidPassthroughPayload;
use Zhineng\Checkpoint\Tencent\File;
use Zhineng\Checkpoint\Tencent\Gender;
use Zhineng\Checkpoint\Tencent\IdentityVerification;

class WebhookController
{
    public function __invoke(Request $request)
    {
        $payload = $request->all();

        if (! $this->hasToken($payload)) {
            return new Response();
        }

        $method = $this->resolveHandler($payload);

        if ($method && method_exists($this, $method)) {
            try {
                $this->{$method}($payload);
            } catch (InvalidPassthroughPayload $e) {
                return new Response('Webhook Skipped');
            }

            return new Response('Webhook Handled');
        }

        return new Response();
    }

    protected function hasToken(array $payload): bool
    {
        return count(array_intersect_key(array_flip(['BizToken']), $payload)) > 0;
    }

    protected function resolveHandler(array $payload): string|false
    {
        if (isset($payload['BizToken'])) {
            return 'handleIdentityVerifiedViaWeChatWeb';
        }

        return false;
    }

    protected function handleIdentityVerifiedViaWeChatWeb(array $payload): void
    {
        $passthrough = json_decode($payload['Extra'], true);

        if (! is_array($passthrough)) {
            throw new InvalidPassthroughPayload;
        }

        $verification = $this->newIdentityVerification($payload['Extra']);

        $result = $this->findVerificationResultViaWeChatWeb($payload['BizToken'], $passthrough['rule_id']);

        // skip the webhook if there's any errors occurred.
        if ($result->json('Response.Error')) {
            return;
        }

        // skip the webhook if `ErrCode` is null which indicating the status
        // of the request still awaiting verification.
        if ($result->json('Response.Text.ErrCode') === null) {
            return;
        }

        $verification->fill([
            'name' => $result->json('Response.Text.Name'),
            'id_number' => $result->json('Response.Text.IdCard'),
            'status' => $result->json('Response.Text.ErrCode') === 0
                ? IdentityVerification::STATUS_PASSED
                : IdentityVerification::STATUS_FAILED,
            'token' => $payload['BizToken'],
            'channel' => Channel::WECHAT_WEB,
            'ocr' => $this->buildOcrData($result->json('Response.Text')),
            'evaluations' => $this->buildEvaluationsData($result->json('Response.Text.LivenessDetail')),
            'id_card_images' => $this->buildIdCardImagesData($result->json('Response.IdCardData')),
            'frames' => $this->buildFramesData($result->json('Response.BestFrame')),
            'video_path' => $this->persistArtifact($result->json('Response.VideoData.LivenessVideo') ?? null),
        ])->save();
    }

    protected function newIdentityVerification(string $passthrough): IdentityVerification
    {
        $passthrough = json_decode($passthrough, true);

        if (! is_array($passthrough) || ! isset($passthrough['identifiable_id'], $passthrough['identifiable_type'])) {
            throw new InvalidPassthroughPayload;
        }

        return Checkpoint::$identityVerificationModel::newModelInstance([
            'identifiable_id' => $passthrough['identifiable_id'],
            'identifiable_type' => $passthrough['identifiable_type'],
        ]);
    }

    protected function findVerificationResultViaWeChatWeb(string $token, string $ruleId): \Illuminate\Http\Client\Response
    {
        return Checkpoint::post('/', [
            'RuleId' => $ruleId,
            'BizToken' => $token,
        ], ['action' => 'GetDetectInfoEnhanced']);
    }

    protected function buildOcrData(array $payload): array
    {
        return [
            'name' => $payload['OcrName'] ?? null,
            'id_number' => $payload['OcrIdCard'] ?? null,
            'gender' => Gender::make($payload['OcrGender']),
            'ethnic_group' => $payload['OcrNation'] ?? null,
            'address' => $payload['OcrAddress'] ?? null,
            'issued_by' => $payload['OcrAuthority'] ?? null,
            'issued_on' => ($date = $payload['OcrValidDate'] ?? null)
                ? Carbon::createFromFormat('Y.m.d', Str::of($date)->explode('-')->first())->toDateString()
                : null,
            'expired_on' => ($date = $payload['OcrValidDate'] ?? null)
                ? Carbon::createFromFormat('Y.m.d', Str::of($date)->explode('-')->last())->toDateString()
                : null,
            'date_of_birth' => ($date = $payload['OcrBirth'] ?? null)
                ? Carbon::parse($date)->toDateString()
                : null,
        ];
    }

    protected function buildEvaluationsData(array|null $payload): array
    {
        return collect($payload)
            ->map(function ($item) {
                return [
                    'timestamp' => $item['ReqTime'] ?? null,
                    'id' => $item['Seq'] ?? null,
                    'name' => $item['Name'] ?? null,
                    'id_number' => $item['Idcard'] ?? null,
                    'similarity' => $item['Sim'] ?? null,
                    'is_charged' => $item['IsNeedCharge'] ?? null,
                    'error_code' => $item['Errcode'] ?? null,
                    'error_message' => $item['Errmsg'] ?? null,
                    'evaluation_status' => $item['Livestatus'] ?? null,
                    'evaluation_message' => $item['Livemsg'] ?? null,
                    'comparison_status' => $item['Comparestatus'] ?? null,
                    'comparison_message' => $item['Comparemsg'] ?? null,
                    'comparison_library' => $item['CompareLibType'] ?? null,
                ];
            })
            ->toArray();
    }

    protected function buildIdCardImagesData(array|null $payload): array
    {
        return [
            'front_side_path' => $this->persistArtifact($payload['OcrFront'] ?? null),
            'back_side_path' => $this->persistArtifact($payload['OcrBack'] ?? null),
            'cropped_front_side_path' => $this->persistArtifact($payload['ProcessedFrontImage'] ?? null),
            'cropped_back_side_path' => $this->persistArtifact($payload['ProcessedBackImage'] ?? null),
            'cropped_portrait_path' => $this->persistArtifact($payload['Avatar'] ?? null),
        ];
    }

    protected function persistArtifact(string|null $base64): string|null
    {
        if (is_null($base64)) {
            return null;
        }

        $file = File::create()->put(base64_decode($base64));

        return tap(Storage::disk(config('checkpoint.disk'))->putFile('/', (string) $file), function () use ($file) {
            $file->delete();
        });
    }

    protected function buildFramesData(array|null $payload): array
    {
        return collect($payload['BestFrame'] ?? null)
            ->merge($payload['BestFrames'] ?? null)
            ->filter()
            ->map(fn ($base64) => $this->persistArtifact($base64))
            ->toArray();
    }
}