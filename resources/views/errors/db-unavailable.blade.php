@extends('errors::minimal')

@section('title', 'Database Unavailable')
@section('code', '503')
@section('message', $hint ?? 'Database unavailable. Please try again shortly.')

{{-- Extra context in non-production when available, without leaking secrets --}}
@if(app()->isLocal() && isset($message))
    <div style="max-width:640px;margin:24px auto 0;text-align:left;font-size:13px;opacity:0.7;word-break:break-word;">
        <code>{{ Str::limit($message, 500) }}</code>
    </div>
@endif
