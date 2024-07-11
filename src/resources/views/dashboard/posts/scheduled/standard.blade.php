@extends('dashboard.posts.scheduled.partials.edit')

@section('title')
    {{ ucfirst($title) }}
@endsection

@section('form')
{{--<div class="mb-3">--}}
{{--    <div class="form-label">Media</div>--}}
{{--    <input name="media[]" type="file" class="form-control" multiple/>--}}
{{--</div>--}}

{{--<hr/>--}}
{{--<div class="row mb-3">--}}
{{--    <div class="col-6">--}}
{{--        <label class="form-label">Topic type</label>--}}
{{--        <select name="topic_type" class="form-select @if ($errors->has('topic_type')) is-invalid @endif">--}}
{{--            <option @if (old('topic_type') === 'STANDARD' || empty(old('topic_type'))) selected @endif value="STANDARD">Standard</option>--}}
{{--            <option @if (old('topic_type') === 'EVENT') selected @endif value="EVENT">Event</option>--}}
{{--            <option @if (old('topic_type') === 'OFFER') selected @endif value="OFFER">Offer</option>--}}
{{--            <option @if (old('topic_type') === 'ALERT') selected @endif value="ALERT">Alert</option>--}}
{{--        </select>--}}
{{--        @if ($errors->has('topic_type'))--}}
{{--            <div class="invalid-feedback">{{ $errors->first('topic_type') }}</div>--}}
{{--        @endif--}}
{{--    </div>--}}
{{--    <div class="col-6">--}}
{{--        <label class="form-label">Alert type&nbsp;<span class="text-sm text-secondary">(this field is only applicable for posts with topic type alert)</span></label>--}}
{{--        <select name="alert_type" class="form-select @if ($errors->has('alert_type')) is-invalid @endif">--}}
{{--            <option @if (old('alert_type') === 'ALERT_TYPE_UNSPECIFIED' || empty(old('alert_type'))) selected @endif value="ALERT_TYPE_UNSPECIFIED">Unspecified</option>--}}
{{--            <option @if (old('alert_type') === 'COVID_19') selected @endif value="COVID_19">Covid 2019</option>--}}
{{--        </select>--}}
{{--        @if ($errors->has('alert_type'))--}}
{{--            <div class="invalid-feedback">{{ $errors->first('alert_type') }}</div>--}}
{{--        @endif--}}
{{--    </div>--}}
{{--</div>--}}
{{--<hr/>--}}
{{--<div class="mb-3">--}}
{{--    <span class="text-sm text-secondary">This field is required for posts with topic type event or alert</span>--}}
{{--</div>--}}

{{--<hr/>--}}

@endsection