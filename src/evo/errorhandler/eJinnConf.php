<?php

return [
    "author"        => "ArtisticPhoenix",
    "description"   => "Error and Shutdown handler",
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
            "extends"       => "\\ErrorException",
            "severity"      => E_ERROR,
            "interfaces"    => [
                "ErrorHandlerExceptionInterface"
            ],
            'implements' =>[
                 'evo\\errorhandler\\Exception\\ErrorHandlerExceptionInterface'
            ],
            "exceptions" => [
                900 => 'ShutdownError',
                909 => 'RuntimeError'
            ]
        ]
    ]
];