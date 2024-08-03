@php use Illuminate\Support\Str; @endphp
<div class="row">
    @if ($user->isGoogleAccountAuthenticated())
        <p class="text-muted">{!! trans('dashboard.choose_main_gmb_account') !!}</p>
        <div class="col-6 mb-3">
            <div class="row">
                <div class="col-auto">
                    <a class="btn btn-icon btn-outline-danger" title="Delete"
                       onclick="return confirm('{{ trans('dashboard.disconnect_gmb_question') }}');"
                       href="{{ route('dashboard.account.gmb.disconnect') }}" class="btn btn-outline-danger btn-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round"
                             class="icon icon-tabler icons-tabler-outline icon-tabler-plug-connected">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M7 12l5 5l-1.5 1.5a3.536 3.536 0 1 1 -5 -5l1.5 -1.5z"/>
                            <path d="M17 12l-5 -5l1.5 -1.5a3.536 3.536 0 1 1 5 5l-1.5 1.5z"/>
                            <path d="M3 21l2.5 -2.5"/>
                            <path d="M18.5 5.5l2.5 -2.5"/>
                            <path d="M10 11l-2 2"/>
                            <path d="M13 14l-2 2"/>
                        </svg>
                        &nbsp;{{ trans('dashboard.disconnect_from_gmb') }}
                    </a>
                </div>
            </div>
        </div>
    @else
        <p class="text-muted">{!! trans('dashboard.link_gmb_account') !!}</p>
        <div class="col-6 mb-3">
            <div class="row">
                <div class="col-auto">
                    <a href="{{ $callbackURL ?? '#' }}" type="submit" class="btn btn-white btn-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round"
                             class="icon icon-tabler icons-tabler-outline icon-tabler-plug-connected">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M7 12l5 5l-1.5 1.5a3.536 3.536 0 1 1 -5 -5l1.5 -1.5z"/>
                            <path d="M17 12l-5 -5l1.5 -1.5a3.536 3.536 0 1 1 5 5l-1.5 1.5z"/>
                            <path d="M3 21l2.5 -2.5"/>
                            <path d="M18.5 5.5l2.5 -2.5"/>
                            <path d="M10 11l-2 2"/>
                            <path d="M13 14l-2 2"/>
                        </svg>
                        &nbsp;Connect with Google My Business
                    </a>
                </div>
            </div>
        </div>
    @endif

    @if (sizeof($accountLocations) > 0)
        <div class="col-12 mt-2">
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-vcenter table-mobile-md card-table">
                        <thead>
                        <tr>
                            <th>Emplacement</th>
                            <th>Description</th>
                            <th class="w-1"></th>
                        </tr>
                        </thead>
                        <tbody id="locations-container">
                        @foreach ($accountLocations as $accountLocation)
                            @php($isMainLocation =  Str::endsWith($accountLocation['name'], $user->googleCredentials?->main_location_id))
                            <tr>
                                <td>{{ $accountLocation['title'] }}</td>
                                <td>{{ $accountLocation['profile']['description'] ?? '---' }}</td>
                                <td>
                                    <a href="{{ $isMainLocation ? '#' : route('dashboard.account.locations.main', ['name' => $accountLocation['name']]) }}"
                                       class="btn btn-green btn-sm {{ $isMainLocation ? 'disabled' : '' }}"
                                       title="Définir comme emplacement principal"
                                       @if($isMainLocation)onclick="return confirm('Voulez-vous vraiment définir comme emplacement principal ?')"@endif>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                             stroke-linecap="round" stroke-linejoin="round"
                                             class="icon icon-tabler icons-tabler-outline icon-tabler-checks">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M7 12l5 5l10 -10"/>
                                            <path d="M2 12l5 5m5 -5l5 -5"/>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @if(! empty($accountLocationsNextToken))
                    <input name="gmb_next" type="hidden" value="{{ $accountLocationsNextToken }}"/>
                    <div class="card-footer text-center">
                        <div class="card-actions">
                            <a id="load-more-btn" href="javascript:void(0);" onclick="loadMoreLocations()"
                               class="btn btn-icon btn-clnkgo">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                     fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round"
                                     class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M12 5l0 14"/>
                                    <path d="M5 12l14 0"/>
                                </svg>
                                &nbsp;Charger plus
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

@section('script')
    function loadMoreLocations() {
    $.ajax({
    type: "GET",
    url: new URL("{{ route('dashboard.account') }}"),
    data: { tab: 'gmb', next: jQuery('input[name=gmb_next]').val() },
    beforeSend: function(xhr) {
    $('#load-more-btn').addClass('disabled');
    }
    }).done(function(data, textStatus, jqXHR) {
    let next = jqXHR.getResponseHeader('Gmb-Next');

    jQuery('input[name=gmb_next]').val(next);
    $('#locations-container').parent().append(data);

    if (next.length === 0) {
    $('#load-more-btn').parents('.card-footer').remove();
    }else {
    $('#load-more-btn').removeClass('disabled');
    }
    });
    }
@endsection
