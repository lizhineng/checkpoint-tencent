<?php

namespace Zhineng\Checkpoint\Tencent;

use Stringable;

class File implements Stringable
{
    public function __construct(
        protected string $path
    ) {
        //
    }

    public static function create()
    {
        return new static(tempnam(sys_get_temp_dir(), 'checkpoint-tencent'));
    }

    public function put($content): self
    {
        $handle = fopen($this->path, 'w');
        fwrite($handle, $content);
        fclose($handle);

        return $this;
    }

    public function delete(): bool
    {
        return unlink($this->path);
    }

    public function __toString()
    {
        return $this->path;
    }
}
