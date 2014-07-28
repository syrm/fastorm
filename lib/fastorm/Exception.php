<?php
namespace fastorm;

class Exception extends \Exception
{
    public function __toString()
    {
        return get_class($this) . " {$this->code} '{$this->message}' in {$this->file} ({$this->line})\n"
        . "{$this->getTraceAsString()}";
    }
}
