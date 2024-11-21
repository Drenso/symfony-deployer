<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routing): void {
  $routing->add('_drenso_update_page_preview', '/_drenso/update/preview/{name}')
    ->controller('Drenso\DeployerBundle\Controller\UpdatePreviewController::preview');
};
