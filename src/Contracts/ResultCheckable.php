<?php

namespace Zhineng\Checkpoint\Tencent\Contracts;

use Illuminate\Http\Client\Response;

interface ResultCheckable
{
    public function get(string $token): Response;
}
