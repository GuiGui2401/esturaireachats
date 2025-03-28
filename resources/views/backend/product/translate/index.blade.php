@extends('backend.layouts.app')

@section('content')
    <div class="page-content">
        <div class="aiz-titlebar text-left mt-2 pb-2 px-3 px-md-2rem border-bottom border-gray">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="h3">{{ translate('Translate Product') }}</h1>
                </div>
                <div class="col-lg">
                    <a class="btn btn-info" href="{{ route('product_translate_copier.index') }}">
                        {{ translate('Copy Product If Not Exists In frensh') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="d-sm-flex">
            <!-- tab content -->
            <div class="flex-grow-1 p-sm-3 p-lg-2rem mb-2rem mb-md-0">
                <!-- Error Meassages -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="" method="POST" enctype="multipart/form-data" id="product_translate">
                    @csrf
                    <input name="_method" type="hidden" value="POST">
                    <input type="hidden" name="lang" value="{{ $lang }}">
                    <input type="hidden" name="tab" id="tab">
                    <input type="hidden" name="page" value="{{ $page }}">


                    <ul class="nav nav-tabs nav-fill border-light language-bar">
                        @foreach (get_all_active_language() as $key => $language)
                            <li class="nav-item">
                                <a class="nav-link text-reset @if ($language->code == $lang) active @else bg-soft-dark border-light border-left-0 @endif py-3"
                                    href="{{ route('product_translate.index', ['lang' => $language->code]) }}">
                                    <img loading="lazy" src="{{ static_asset('assets/img/flags/' . $language->code . '.png') }}"
                                        height="11" class="mr-1">
                                    <span>{{ $language->name }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </form>

                <form action="" class="row mt-2" method="GET">
                    <div class="col-lg-10">
                        <div class="form-group">
                            <input type="text" class="form-control" name="search"
                                value="{{ request()->input('search') }}">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">{{ translate('Search') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="container">
            @foreach ($products as $key => $product)
                <form action="{{ route('product_translate.update', $product->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="card">
                        <div class="row container">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-label">{{ translate('Name') }}</label>
                                    <input type="text" class="form-control" name="name" value="{{ $product->name }}">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-label">{{ translate('Description') }}</label>
                                    <textarea class="form-control aiz-text-editor" name="description" value="{!! $product->description !!}"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer d-flex justify-content-between align-items-center">
                            <img loading="lazy" src="{{ static_asset('assets/img/flags/' . $product->lang . '.png') }}" height="11"
                                class="mr-1">
                            <button type="submit" class="btn btn-info">{{ translate('Save') }}</button>
                        </div>
                    </div>
                </form>
            @endforeach

            <div class="aiz-pagination">
                {{ $products->appends(request()->input())->links() }}
            </div>

        </div>
    </div>
@endsection
