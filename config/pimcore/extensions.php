<?php

return [
    "bundle" => [
        "Pimcore\\Bundle\\EcommerceFrameworkBundle\\PimcoreEcommerceFrameworkBundle" => TRUE,
        "Pimcore\\Bundle\\DataImporterBundle\\PimcoreDataImporterBundle" => TRUE,
        "Pimcore\\Bundle\\DataHubBundle\\PimcoreDataHubBundle" => [
            "enabled" => TRUE,
            "priority" => 11,
            "environments" => [

            ]
        ]
    ]
];
