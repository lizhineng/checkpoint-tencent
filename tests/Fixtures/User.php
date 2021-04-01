<?php

namespace Zhineng\Checkpoint\Tencent\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Zhineng\Checkpoint\Tencent\HasIdentity;

class User extends Model
{
    use HasIdentity;

    protected $guarded = [];
}
