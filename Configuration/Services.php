<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services()
        ->defaults()
        ->autowire()
        ->autoconfigure();

    $services->load('Bobosch\\OdsOsm\\', '../Classes/*')
        ->exclude('../Classes/Domain/Model/*');
};
