<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Controllers;

use App\Models\User;
use App\Jobs\PullAccountLocations;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\Factory;
use BADDIServices\ClnkGO\Models\Pack;
use Illuminate\Foundation\Bus\DispatchesJobs;
use BADDIServices\ClnkGO\Services\UserService;
use BADDIServices\ClnkGO\Domains\GoogleService;
use BADDIServices\ClnkGO\Models\AccountLocation;
use Illuminate\Routing\Controller as BaseController;
use BADDIServices\ClnkGO\Models\UserGoogleCredentials;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use BADDIServices\ClnkGO\Domains\GoogleMyBusinessService;
use BADDIServices\ClnkGO\Models\ObjectValues\GoogleCredentialsObjectValue;

class DashboardController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    protected UserService $userService;

    protected GoogleService $googleService;

    protected GoogleMyBusinessService $googleMyBusinessService;

    protected User $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->userService = app(UserService::class);

            $this->user = Auth::id() !== null ? $this->userService->findById(Auth::id()) : null;

            $this->googleService = app(GoogleService::class);
            $this->googleService->refreshAccessToken($this->user->googleCredentials);
            $this->user->load(['googleCredentials']); // reload user google credentials

            $this->googleMyBusinessService = new GoogleMyBusinessService(
                $this->user->googleCredentials?->getAccessToken(),
                $this->user->googleCredentials?->getAccountId(),
                $this->user->googleCredentials?->getMainLocationId()
            );

            return $next($request);
        });
    }

    public function render(string $name, array $data = []): View|Factory
    {
        return view($name, array_merge($this->defaultData(), $data));
    }

    private function defaultData(): array
    {
        $accountLocations = AccountLocation::query()
            ->where(AccountLocation::USER_ID_COLUMN, $this->user->getId())
            ->get()
            ->toArray();

        // Fixme: remove this auto pull
        if (sizeof($accountLocations) === 0 && $this->user->googleCredentials instanceof UserGoogleCredentials) {
            PullAccountLocations::dispatch(
                $this->user->getId(),
                GoogleCredentialsObjectValue::fromArray($this->user->googleCredentials->toArray())
            );
        }

        return [
            'user'                  => $this->user,
            'userAccountLocations'  => $accountLocations,
        ];
    }
}