<?php

namespace Zhineng\Checkpoint\Tencent;

use Illuminate\Database\Eloquent\Model;

class IdentityVerification extends Model
{
    public const STATUS_PASSED = 'passed';
    public const STATUS_FAILED = 'failed';

    protected $guarded = [];
}