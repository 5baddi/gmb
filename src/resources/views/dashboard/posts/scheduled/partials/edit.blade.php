@extends('layouts.dashboard')

@section('title')
    {{ ucfirst($title) }}
@endsection

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/libs/dropzone/dist/dropzone.css" rel="stylesheet" />
@endsection

@section('content')
<div class="row row-cards">
    <div class="col-12">
        <form class="card" method="POST" action="{{ route('dashboard.scheduled-posts.save', ['type' => $type]) }}" enctype="multipart/form-data">
            @csrf
            <input name="id" value="{{ $id }}" hidden/>
            <div class="card-header">
                <h3 class="card-title">{!! $cardTitle !!}</h3>
            </div>
            <div class="card-body">
                <div class="mb-3 dropzone" id="upload-scheduled-post-media">
                    <div class="dz-default dz-message">
                        <button class="dz-button" type="button">{{ trans('global.drop_post_media_here') }}</button>
                    </div>
                </div>
                <div class="mb-3">
                    <small class="text-secondary">
                        {{ trans('global.media_allowed_mimetypes') }}
                    </small>
                    <br/>
                    <small class="text-secondary">
                        {{ trans('global.media_allowed_file_size') }}
                    </small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Résumé&nbsp;<span class="form-label-description" id="summary-length">0/5000</span></label>
                    <textarea name="summary" maxlength="5000" onkeyup="calculateTextLength(event, '#summary-length', '/5000')" class="form-control @if ($errors->has('summary')) is-invalid @endif" rows="5" placeholder="Summary">{{ $scheduledPost?->summary ?? old('summary') }}</textarea>
                    @if ($errors->has('summary'))
                        <div class="invalid-feedback">{{ $errors->first('summary') }}</div>
                    @endif
                </div>
                <div class="row mb-3">
                    <div class="col-4">
                        <label class="form-label">Type d’appel à l’action</label>
                        <select name="action_type" class="form-select @if ($errors->has('action_type')) is-invalid @endif">
                            <option @if (($scheduledPost?->action_type ?? old('action_type')) === 'book') selected @endif value="book">Réserver un rendez-vous, une table, etc</option>
                            <option @if (($scheduledPost?->action_type ?? old('action_type')) === 'order') selected @endif value="order">Commander quelque chose</option>
                            <option @if (($scheduledPost?->action_type ?? old('action_type')) === 'shop') selected @endif value="shop">Parcourir un catalogue de produits</option>
                            <option @if (($scheduledPost?->action_type ?? old('action_type')) === 'learn_more') selected @endif value="learn_more">En savoir plus</option>
                            <option @if (($scheduledPost?->action_type ?? old('action_type')) === 'sign_up') selected @endif value="sign_up">S'inscrire/s'inscrire/rejoindre quelque chose</option>
                            @if($type === 'offer')
                            <option @if (($scheduledPost?->action_type ?? old('action_type')) === 'get_offer') selected @endif value="get_offer">Offre</option>
                            @endif
                            <option @if (($scheduledPost?->action_type ?? old('action_type')) === 'call' || empty(($scheduledPost?->action_type ?? old('action_type')))) selected @endif value="call">Appeler l'établissement</option>
                        </select>
                        @if ($errors->has('action_type'))
                            <div class="invalid-feedback">{{ $errors->first('action_type') }}</div>
                        @endif
                    </div>
                    <div class="col-8">
                        <label class="form-label">L’appel à l’action</label>
                        <input type="text" name="action_url" class="form-control @if ($errors->has('action_url')) is-invalid @endif" value="{{ $scheduledPost?->action_url ?? old('action_url') }}" placeholder="L’appel à l’action"/>
                        @if ($errors->has('action_url'))
                            <div class="invalid-feedback">{{ $errors->first('action_url') }}</div>
                        @endif
                    </div>
                </div>
                @yield('form')
                <div class="accordion">
                    <div class="accordion-item" id="accordion-auto-posting">
                        <h2 class="accordion-header" id="heading-1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-1" aria-expanded="true">
                                {{ trans('global.auto_posting') }}
                            </button>
                        </h2>
                        <div id="collapse-1" class="accordion-collapse collapse" data-bs-parent="#accordion-auto-posting" style="">
                            <div class="accordion-body pt-3">
                                <div class="row">
                                    <div class="col-8">
                                        <label class="form-label">{{ trans('global.scheduled_date') }}</label>
                                        <input type="date" name="scheduled_date" class="form-control @if ($errors->has('scheduled_date')) is-invalid @endif" value="{{ old('scheduled_date') }}"/>
                                        @if ($errors->has('scheduled_date'))
                                            <div class="invalid-feedback">{{ $errors->first('scheduled_date') }}</div>
                                        @endif
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label">{{ trans('global.scheduled_time') }}</label>
                                        <input type="time" name="scheduled_time" class="form-control @if ($errors->has('scheduled_time')) is-invalid @endif" value="{{ old('scheduled_time') }}"/>
                                        @if ($errors->has('scheduled_time'))
                                            <div class="invalid-feedback">{{ $errors->first('scheduled_time') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <button type="submit" class="btn btn-clnkgo">
                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-device-floppy"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" /><path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M14 4l0 4l-6 0l0 -4" /></svg>
                    &nbsp;{{ trans('dashboard.save_post') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/libs/dropzone/dist/dropzone-min.js"></script>
@endsection

@section('script')
    document.addEventListener("DOMContentLoaded", function() {
        let dropzoneInstance = new Dropzone("#upload-scheduled-post-media", {
            url: '{{ route('dashboard.scheduled-posts.upload.media', ['id' => $id]) }}',
            dictRemoveFile: '{{ trans('global.remove_file') }}',
            dictCancelUpload: 'Annuler le téléchargement',
            dictCancelUploadConfirmation: 'Êtes-vous sûr de vouloir annuler ce téléchargement ?',
            addRemoveLinks: true,
            uploadMultiple: true,
            acceptedFiles: 'image/jpeg, image/png, image/gif, image/bmp, image/tiff, image/webp, video/mp4, video/quicktime, video/x-msvideo, video/mpeg, video/x-ms-wmv',
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            },
            error: function (file) {
                if (file.accepted === false) {
                    alert("{{ trans('global.unsupported_file_format') }}");
                }
            }
        });
    })
@endsection