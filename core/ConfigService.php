<?php

namespace core;

class ConfigService
{
    /** @var string $filePath */
    private $filePath;

    public function get($path)
    {
        $this->filePath = $path;
        return $this;
    }

    public function parse()
    {
        $content = file_get_contents($this->filePath);
        return json_decode($content, true);
    }
}