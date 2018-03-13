<?php
require_once __DIR__.'/../../../vendor/autoload.php';
return [
    "author"        => "ErrorHandler",
    "description"   => "eJinn The Exception Genie",
    "package"       => "Evo",
    "subpackage"    => "ErrorHandler",
    "_buildpath"     => ["psr"=>4],
    "support"       => "https://github.com/ArtisticPhoenix/ErrorHandler/issues",
    "version"       => "1.0.0",
    "reserved"       => [1,2,[8,20]],
    "namespaces"     => [
        "evo\\errorhandler\\Exception"  => [
            "subpackage"    => "Exception",
            "buildpath"     =>  __DIR__.'/Exception/',
            "interfaces"    => [
                "ErrorHandlerExceptionInterface"
            ],
            'implements' =>[
                'evo\\errorhandler\\Exception\\ErrorHandlerExceptionInterface'
            ],
            "exceptions" => [
                "0"     => "UnknownError",
                "2000"  => "ShutdownError",
            ]
        ]
    ]
];
