<?php
namespace Hellaz\Exceptions;

class AnalysisException extends \Exception {
    protected $context = [];

    public function __construct($message = "", $code = 0, $context = [], \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }

    public function getContext() {
        return $this->context;
    }
}
