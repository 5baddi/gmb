<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Controllers\Dashboard\Media;

use BADDIServices\ClnkGO\Http\Controllers\DashboardController;

class NewMediaController extends DashboardController
{
    public function __invoke()
    {
        return $this->render(
            'dashboard.media.create',
            [
                'title' => 'Télécharger de nouveaux médias',
            ]
        );
    }
}