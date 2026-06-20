<?php

namespace Dreitier\Alm\Inspecting\Log;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Psr\Log\LoggerInterface;

class StdoutLogger implements LoggerInterface
{

    public function emergency(\Stringable|string $message, array $context = []): void
    {
        echo $message . PHP_EOL;
    }

    public function alert(\Stringable|string $message, array $context = []): void
    {
        echo $message . PHP_EOL;
    }

    public function critical(\Stringable|string $message, array $context = []): void
    {
        echo $message . PHP_EOL;
    }

    public function error(\Stringable|string $message, array $context = []): void
    {
        echo $message . PHP_EOL;
    }

    public function warning(\Stringable|string $message, array $context = []): void
    {
        echo $message . PHP_EOL;
    }

    public function notice(\Stringable|string $message, array $context = []): void
    {
        echo $message . PHP_EOL;
    }

    public function info(\Stringable|string $message, array $context = []): void
    {
        echo $message . PHP_EOL;
    }

    public function debug(\Stringable|string $message, array $context = []): void
    {
        echo $message . PHP_EOL;
    }

    public function log($level, \Stringable|string $message, array $context = []): void
    {
        echo $level . ":" . $message . PHP_EOL;
    }

    public static function createHandlerstack(): HandlerStack {
        $loggerInstance = new static();
        $handlers = HandlerStack::create();

        $templates = [
            '{code} >> {req_headers}',
            '{code} >> {req_body}',
            '{code} << {res_headers}',
            '{code} << {res_body}'
        ];

        foreach ($templates as $template) {
            $handlers->unshift(Middleware::log($loggerInstance, new MessageFormatter($template)));
        }

        return $handlers;
    }
}