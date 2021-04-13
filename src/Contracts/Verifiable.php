<?php

namespace Zhineng\Checkpoint\Tencent\Contracts;

use Illuminate\Http\Client\Response;

interface Verifiable
{
    public function request(): Response;
}