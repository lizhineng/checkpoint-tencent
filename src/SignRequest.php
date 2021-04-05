<?php

namespace Zhineng\Checkpoint\Tencent;

use Illuminate\Support\Str;
use Psr\Http\Message\RequestInterface;

class SignRequest
{
    protected array $signedHeaders = [
        'content-type',
        'host',
    ];

    protected string $algorithm = 'TC3-HMAC-SHA256';

    public function __construct(
        protected string $accessKey,
        protected string $accessSecret,
        protected string $action
    ) {
        //
    }

    public static function handle(string $accessKey, string $accessSecret, string $action): callable
    {
        return (new static($accessKey, $accessSecret, $action))->process();
    }

    public function process(): callable
    {
        return function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                $request = $request->withHeader('X-TC-Action', $this->action);
                $request = $request->withHeader('X-TC-Timestamp', time());
                $request = $request->withHeader('X-TC-Version', '2018-03-01');
                $request = $request->withHeader('Authorization', $this->buildAuthorization($request));

                return $handler($request, $options);
            };
        };
    }

    protected function buildAuthorization(RequestInterface $request): string
    {
        $scope = implode('/', [
            gmdate('Y-m-d', $request->getHeaderLine('X-TC-Timestamp')),
            Str::of($request->getUri()->getHost())->explode('.')->first(),
            'tc3_request',
        ]);

        $signedHeaders = collect($this->signedHeaders)->sort()->join(';');

        return "{$this->algorithm} Credential={$this->accessKey}/{$scope}, SignedHeaders={$signedHeaders}, Signature={$this->buildSignature($request, $scope)}";
    }

    protected function buildSignature(RequestInterface $request, string $scope): string
    {
        $data = implode("\n", [
            $this->algorithm,
            $request->getHeaderLine('X-TC-Timestamp'),
            $scope,
            hash('sha256', $this->buildRequestMeta($request)),
        ]);

        $dateKey = hash_hmac('sha256', gmdate('Y-m-d', $request->getHeaderLine('X-TC-Timestamp')),
            'TC3'.$this->accessSecret, true);
        $serviceKey = hash_hmac('sha256', Str::of($request->getUri()->getHost())->explode('.')->first(), $dateKey, true);
        $key = hash_hmac('sha256', 'tc3_request', $serviceKey, true);

        return hash_hmac('sha256', $data, $key);
    }

    protected function buildRequestMeta(RequestInterface $request): string
    {
        return implode("\n", [
            $request->getMethod(),

            $request->getUri()->getPath(),

            $request->getUri()->getQuery(),

            collect($this->signedHeaders)
                ->map(fn($header) => $header.':'.$request->getHeaderLine($header))
                ->join("\n")."\n",

            collect($this->signedHeaders)->sort()->join(';'),

            hash('sha256', $request->getBody()->getContents()),
        ]);
    }
}