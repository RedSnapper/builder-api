<?php

namespace RedSnapper\Builder\Tests;

class Fixtures
{
    public static function successfulResponse(): string
    {
        return json_encode([
            'result' => [
                'data' => [
                    [
                        'code'  => 'UK 12345',
                        'date'  => '2022-02-03',
                        'id'    => 19,
                        'link'  => '/home',
                        'title' => 'Home Page',
                    ],
                    [
                        'code'  => 'UK 12345',
                        'date'  => '2022-02-05',
                        'id'    => 20,
                        'link'  => '/about',
                        'title' => 'About Page',
                    ],
                ],
            ],
            'log'    => [
                'passed'   => true,
                'messages' => [
                    [
                        'channel' => 'build',
                        'purpose' => 'extra',
                        'message' => 'Builder v2022.012. Build: Jan 20 2022; 13:10:58',
                    ],
                ],
            ],
        ]);
    }

    public static function errorMsgResponse(string $msg = 'Builder error msg'): string
    {
        return json_encode([
            'log' => [
                'passed'   => false,
                'messages' => [
                    [
                        'channel' => 'build',
                        'purpose' => 'extra',
                        'message' => 'Builder v2022.012. Build: Jan 20 2022; 13:10:58',
                    ],
                    [
                        'channel' => 'fatal',
                        'purpose' => 'alert',
                        'message' => $msg,
                    ],
                ],
            ],
        ]);
    }

    public static function emptyErrorMsgResponse(): string
    {
        return json_encode([
            'log' => [
                'passed'   => false,
                'messages' => [
                    [
                        'channel' => 'build',
                        'purpose' => 'extra',
                        'message' => 'Builder v2022.012. Build: Jan 20 2022; 13:10:58',
                    ],
                ],
            ],
        ]);
    }
}