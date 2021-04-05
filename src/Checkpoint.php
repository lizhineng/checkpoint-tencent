<?php

namespace Zhineng\Checkpoint\Tencent;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

class Checkpoint
{
    public static bool $runsMigrations = true;

    public static bool $registersRoutes = true;

    public static string $identityVerificationModel = IdentityVerification::class;

    public static function endpointUrl(): string
    {
        return 'https://faceid.tencentcloudapi.com';
    }

    public static function post($uri, array $payload = [], array $options = []): Response
    {
        return static::makeApiCall('post', static::endpointUrl().$uri, $payload, $options);
    }

    protected static function makeApiCall($method, $uri, array $payload = [], array $options = []): Response
    {
        if (! isset($options['action'])) {
            throw new InvalidArgumentException('The request is missing action options.');
        }

        return Http::asJson()
            ->withMiddleware(SignRequest::handle(config('checkpoint.key'), config('checkpoint.secret'), $options['action']))
            ->{$method}($uri, $payload);
    }

    public static function ignoreRoutes(): static
    {
        static::$registersRoutes = false;

        return new static;
    }
}
