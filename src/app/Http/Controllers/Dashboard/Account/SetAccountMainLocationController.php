<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Controllers\Dashboard\Account;

use Throwable;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use BADDIServices\ClnkGO\Entities\Alert;
use BADDIServices\ClnkGO\Models\UserGoogleCredentials;
use BADDIServices\ClnkGO\Http\Controllers\DashboardController;
use BADDIServices\ClnkGO\Models\ObjectValues\GoogleCredentialsObjectValue;

class SetAccountMainLocationController extends DashboardController
{
    public function __invoke(Request $request): RedirectResponse
    {
        try {
            $this->userService->saveGoogleCredentials(
                $this->user,
                GoogleCredentialsObjectValue::fromArray(
                    array_merge(
                        $this->user->googleCredentials->toArray(),
                        [
                            UserGoogleCredentials::MAIN_LOCATION_ID_COLUMN
                            => Str::replace('locations/', '', $request->get('name'))
                        ]
                    )
                )
            );

            return redirect()->route('dashboard.account', ['tab' => 'gmb'])
                ->with(
                    'alert',
                    new Alert('Main location successfully updated.', 'success')
                );
        } catch (Throwable){
            return redirect()->route('dashboard.account', ['tab' => 'emails'])
                ->with(
                    'alert',
                    new Alert('An occurred error while setting main location!')
                );
        }
    }
}