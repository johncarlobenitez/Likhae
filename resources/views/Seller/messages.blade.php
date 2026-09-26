@extends('layouts.seller')
@section('title','Messages')
@section('active','messages')
@section('subtitle','Contact buyers, riders, logistics, and platform staff.')
@section('content')<div class="sl-page"><div class="sl-page-toolbar"><div><span class="sl-eyebrow">Communication</span><h2>Messages</h2><p>Conversations are stored in the LIKHAE database.</p></div></div>@include('partials.conversation-center',['sendRoute'=>route('seller.messages.send')])</div>@endsection
