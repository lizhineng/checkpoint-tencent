<?php

namespace Zhineng\Checkpoint\Tencent\Tests\Feature;

trait FakeResponse
{
    protected function successResponse(): array
    {
        return [
            'Response' => [
                'Text' => [
                    'ErrCode' => 0,
                    'ErrMsg' => '成功',
                    'IdCard' => '000',
                    'Name' => 'Zhineng',
                    'OcrNation' => 'foo',
                    'OcrAddress' => 'bar',
                    'OcrBirth' => '2021/1/1',
                    'OcrAuthority' => 'baz',
                    'OcrValidDate' => '2021.01.01-2031.01.01',
                    'OcrName' => 'Zhineng',
                    'OcrIdCard' => '000',
                    'OcrGender' => '男',
                    'LiveStatus' => 0,
                    'LiveMsg' => '成功',
                    'Comparestatus' => 0,
                    'Comparemsg' => '成功',
                    'CompareLibType' => 'qux',
                    'Sim' => '100.00',
                    'Location' => null,
                    'Mobile' => '',
                    'Extra' => '',
                    'LivenessDetail' => [
                        [
                            'ReqTime' => '0',
                            'Seq' => 'foo',
                            'Idcard' => '000',
                            'Name' => 'Zhineng',
                            'Sim' => '100.00',
                            'IsNeedCharge' => true,
                            'Errcode' => 0,
                            'Errmsg' => '成功',
                            'Livestatus' => 0,
                            'Livemsg' => '成功',
                            'Comparestatus' => 0,
                            'Comparemsg' => '成功',
                            'CompareLibType' => 'bar',
                        ],
                    ],
                ],
                'IdCardData' => [
                    'OcrFront' => null,
                    'OcrBack' => null,
                    'ProcessedFrontImage' => null,
                    'ProcessedBackImage' => null,
                    'Avatar' => null,
                ],
                'BestFrame' => [
                    'BestFrame' => null,
                    'BestFrames' => null,
                ],
                'VideoData' => [
                    'LivenessVideo' => null,
                ],
                'RequestId' => 'foo',
                'Encryption' => [
                    'CiphertextBlob' => '',
                    'EncryptList' => [],
                    'Iv' => '',
                ],
            ],
        ];
    }

    protected function expiredTokenResponse(): array
    {
        return [
            'Response' => [
                'Error' => [
                    'Code' => 'InvalidParameterValue.BizTokenExpired',
                    'Message' => 'BizToken过期。',
                ],
                'RequestId' => 'foo',
                'Encryption' => [
                    'CiphertextBlob' => '',
                    'EncryptList' => [],
                    'Iv' => '',
                ],
            ],
        ];
    }
}