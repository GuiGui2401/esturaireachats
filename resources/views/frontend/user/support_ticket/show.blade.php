@extends('frontend.layouts.user_panel')

@section('panel_content')
    <div class="card rounded-0 shadow-none border">
        <!-- Ticket info -->
        <div class="card-header border-bottom-0">
            <div class="text-center text-md-left">
                <h5 class="mb-md-0 fs-20 fw-700 text-dark">{{ $ticket->subject }} #{{ $ticket->code }}</h5>
               <div class="mt-4 fs-14">
                   <span class="fw-700 text-dark"> {{ $ticket->user->name }} </span>
                   <span class="ml-2 text-secondary"> {{ $ticket->created_at }} </span>
                   <span class="badge badge-inline badge-gray ml-2 p-3 fs-12" style="border-radius: 25px; min-width: 80px !important;"> {{ translate(ucfirst($ticket->status)) }} </span>
               </div>
            </div>
        </div>
        <hr class="mx-4">
        
        <div class="card-body">
            <!-- Reply form -->
            <form action="{{route('support_ticket.seller_store')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="ticket_id" value="{{$ticket->id}}" required>
                <input type="hidden" name="user_id" value="{{$ticket->user_id}}">
                <div class="form-group">
                    <textarea class="aiz-text-editor rounded-0" name="reply" data-buttons='[["font", ["bold", "underline", "italic"]],["para", ["ul", "ol"]],["view", ["undo","redo"]]]' required></textarea>
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <div class="input-group" data-toggle="aizuploader" data-type="image" data-multiple="true">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium rounded-0">{{ translate('Browse')}}</div>
                            </div>
                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                            <input type="hidden" name="attachments" class="selected-files">
                        </div>
                        <div class="file-preview box sm">
                        </div>
                    </div>
                </div>
                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-sm btn-primary rounded-0 w-150px" onclick="submit_reply('pending')">{{ translate('Send Reply') }}</button>
                </div>
            </form>
            
            <div class="pad-top">
                <ul class="list-group list-group-flush mt-3">
                    <!-- Replies -->
                    @foreach($ticket->ticketreplies as $ticketreply)
                        <li class="list-group-item px-0 border-bottom-0">
                            <div class="media">
                                <a class="media-left" href="#">
                                    @if($ticketreply->user->avatar_original != null)
                                        <span class="avatar avatar-sm mr-3">
                                            <img loading="lazy" src="{{ uploaded_asset($ticketreply->user->avatar_original) }}" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';">
                                        </span>
                                    @else
                                        <span class="avatar avatar-sm mr-3">
                                            <img loading="lazy" src="{{ static_asset('assets/img/avatar-place.png') }}">
                                        </span>
                                    @endif
                                </a>
                                <div class="media-body">
                                    <div class="comment-header">
                                        <span class="fs-14 fw-700 text-dark">{{ $ticketreply->user->name }}</span>
                                        <p class="text-muted text-sm fs-12 mt-2">{{ date('d.m.Y h:i:m', strtotime($ticketreply->created_at)) }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="fs-14 fw-400">
                                {!! $ticketreply->reply !!}
                                <br>
                                <br>
                                @foreach ((explode(",",$ticketreply->files)) as $key => $file)
                                    @php $file_detail = get_single_uploaded_file($file) @endphp
                                    @if($file_detail != null)
                                        <a href="{{ uploaded_asset($file) }}" download="" class="badge badge-lg badge-inline badge-light mb-1">
                                            <i class="las la-download text-muted">{{ $file_detail->file_original_name.'.'.$file_detail->extension }}</i>
                                        </a>
                                        <br>
                                    @endif
                                @endforeach
                            </div>
                        </li>
                    @endforeach

                    <!-- Ticket Details -->
                    <li class="list-group-item px-0">
                        <div class="media">
                            <a class="media-left" href="#">
                                @if($ticket->user->avatar_original != null)
                                    <span class="avatar avatar-sm mr-3">
                                        <img loading="lazy" src="{{ uploaded_asset($ticket->user->avatar_original) }}" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';">
                                    </span>
                                @else
                                    <span class="avatar avatar-sm mr-3">
                                        <img loading="lazy" src="{{ static_asset('assets/img/avatar-place.png') }}">
                                    </span>
                                @endif
                            </a>
                            <div class="media-body">
                                <div class="comment-header">
                                    <span class="fs-14 fw-700 text-dark">{{ $ticket->user->name }}</span>
                                    <p class="text-muted text-sm fs-12 mt-2">{{ date('d.m.Y h:i:m', strtotime($ticket->created_at)) }}</p>
                                </div>
                            </div>
                        </div>
                        <div>
                            {!! $ticket->details !!}
                            <br>
                            <br>
                            @foreach ((explode(",",$ticket->files)) as $key => $file)
                                @php $file_detail = get_single_uploaded_file($file) @endphp
                                @if($file_detail != null)
                                    <a href="{{ uploaded_asset($file) }}" download="" class="badge badge-lg badge-inline badge-light mb-1">
                                        <i class="las la-download text-muted">{{ $file_detail->file_original_name.'.'.$file_detail->extension }}</i>
                                    </a>
                                    <br>
                                @endif
                            @endforeach
                        </div>
                    </li>
                    
                </ul>
            </div>

        </div>
    </div>
@endsection
