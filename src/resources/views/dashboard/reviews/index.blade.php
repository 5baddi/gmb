@extends('layouts.dashboard')

@section('title')
    {{ ucfirst($title) }}
@endsection

@section('content')
    <div class="row row-cards">
        <div class="space-y" id="reviews-container">
            @include('dashboard.reviews.partials.list')
        </div>
    </div>
    @if(! empty($reviews['nextPageToken']))
        <div class="row mt-3">
            <div class="col">
                <a id="load-more-btn" href="javascript:void(0);" onclick="loadMoreReviews()"
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
@endsection

@section('script')
    function loadMoreLocations() {
    $.ajax({
    type: "GET",
    url: new URL("{{ route('dashboard.reviews') }}"),
    data: { tab: 'gmb', next: '{{ $media['nextPageToken'] ?? null }}'},
    beforeSend: function(xhr) {
    $('#load-more-btn').addClass('disabled');
    }
    }).done(function(data, textStatus, jqXHR) {
    let next = jqXHR.getResponseHeader('Gmb-Next');
    $('#reviews-container').append(data);

    if (next.length === 0) {
    $('#load-more-btn').parents('.row').remove();
    }else {
    $('#load-more-btn').removeClass('disabled');
    }
    });
    }
@endsection