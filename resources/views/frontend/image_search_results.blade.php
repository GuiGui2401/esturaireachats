@extends('frontend.layouts.app')

@php
    $meta_title = translate('Search by Image Results');
    $meta_description = translate('Products similar to your uploaded image');
@endphp

@section('meta_title'){{ $meta_title }}@stop
@section('meta_description'){{ $meta_description }}@stop

@section('meta')
    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="{{ $meta_title }}">
    <meta itemprop="description" content="{{ $meta_description }}">

    <!-- Twitter Card data -->
    <meta name="twitter:title" content="{{ $meta_title }}">
    <meta name="twitter:description" content="{{ $meta_description }}">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $meta_title }}" />
    <meta property="og:description" content="{{ $meta_description }}" />
@endsection

@section('content')

    <section class="mb-4 pt-4">
        <div class="container sm-px-0 pt-2">
            <form class="was-validated" id="search-form" action="" method="GET">
                <div class="row">

                    <!-- Sidebar Filters -->
                    <div class="col-xl-3">
                        <div class="aiz-filter-sidebar collapse-sidebar-wrap sidebar-xl sidebar-right z-1035">
                            <div class="overlay overlay-fixed dark c-pointer" data-toggle="class-toggle"
                                data-target=".aiz-filter-sidebar" data-same=".filter-sidebar-thumb"></div>
                            <div class="collapse-sidebar c-scrollbar-light text-left">
                                <div class="d-flex d-xl-none justify-content-between align-items-center pl-3 border-bottom">
                                    <h3 class="h6 mb-0 fw-600">{{ translate('Filters') }}</h3>
                                    <button type="button" class="btn btn-sm p-2 filter-sidebar-thumb"
                                        data-toggle="class-toggle" data-target=".aiz-filter-sidebar">
                                        <i class="las la-times la-2x"></i>
                                    </button>
                                </div>

                                <!-- Categories -->
                                <div class="bg-white border mb-3">
                                    <div class="fs-16 fw-700 p-3">
                                        <a href="#collapse_1"
                                            class="dropdown-toggle filter-section text-dark d-flex align-items-center justify-content-between"
                                            data-toggle="collapse">
                                            {{ translate('Categories') }}
                                        </a>
                                    </div>
                                    <div class="collapse show" id="collapse_1">
                                        <ul class="p-3 mb-0 list-unstyled">
                                            @foreach ($categories as $category)
                                                <li class="mb-3 text-dark">
                                                    <a class="text-reset fs-14 hov-text-primary"
                                                        href="{{ route('products.category', $category->slug) }}">
                                                        {{ $category->getTranslation('name') }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>

                                <!-- Price range -->
                                <div class="bg-white border mb-3">
                                    <div class="fs-16 fw-700 p-3">
                                        {{ translate('Price range') }}
                                    </div>
                                    <div class="p-3 mr-3">
                                        @php
                                            $product_count = get_products_count();
                                        @endphp
                                        <div class="aiz-range-slider">
                                            <div id="input-slider-range"
                                                data-range-value-min="@if ($product_count < 1) 0 @else {{ get_product_min_unit_price() }} @endif"
                                                data-range-value-max="@if ($product_count < 1) 0 @else {{ get_product_max_unit_price() }} @endif">
                                            </div>

                                            <div class="row mt-2">
                                                <div class="col-6">
                                                    <span class="range-slider-value value-low fs-14 fw-600 opacity-70"
                                                        @if (isset($min_price)) data-range-value-low="{{ $min_price }}"
                                                        @elseif($products->min('unit_price') > 0)
                                                            data-range-value-low="{{ $products->min('unit_price') }}"
                                                        @else
                                                            data-range-value-low="0" @endif
                                                        id="input-slider-range-value-low"></span>
                                                </div>
                                                <div class="col-6 text-right">
                                                    <span class="range-slider-value value-high fs-14 fw-600 opacity-70"
                                                        @if (isset($max_price)) data-range-value-high="{{ $max_price }}"
                                                        @elseif($products->max('unit_price') > 0)
                                                            data-range-value-high="{{ $products->max('unit_price') }}"
                                                        @else
                                                            data-range-value-high="0" @endif
                                                        id="input-slider-range-value-high"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Hidden Items -->
                                    <input type="hidden" name="min_price" value="">
                                    <input type="hidden" name="max_price" value="">
                                </div>

                                <!-- Attributes -->
                                @foreach ($attributes as $attribute)
                                    <div class="bg-white border mb-3">
                                        <div class="fs-16 fw-700 p-3">
                                            <a href="#"
                                                class="dropdown-toggle text-dark filter-section collapsed d-flex align-items-center justify-content-between"
                                                data-toggle="collapse"
                                                data-target="#collapse_{{ str_replace(' ', '_', $attribute->name) }}"
                                                style="white-space: normal;">
                                                {{ $attribute->getTranslation('name') }}
                                            </a>
                                        </div>
                                        @php
                                            $show = '';
                                            foreach ($attribute->attribute_values as $attribute_value) {
                                                if (in_array($attribute_value->value, $selected_attribute_values ?? [])) {
                                                    $show = 'show';
                                                }
                                            }
                                        @endphp
                                        <div class="collapse {{ $show }}"
                                            id="collapse_{{ str_replace(' ', '_', $attribute->name) }}">
                                            <div class="p-3 aiz-checkbox-list">
                                                @foreach ($attribute->attribute_values as $attribute_value)
                                                    <label class="aiz-checkbox mb-3">
                                                        <input type="checkbox" name="selected_attribute_values[]"
                                                            value="{{ $attribute_value->value }}"
                                                            @if (in_array($attribute_value->value, $selected_attribute_values ?? [])) checked @endif
                                                            onchange="filter()">
                                                        <span class="aiz-square-check"></span>
                                                        <span
                                                            class="fs-14 fw-400 text-dark">{{ $attribute_value->value }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <!-- Color -->
                                @if (get_setting('color_filter_activation'))
                                    <div class="bg-white border mb-3">
                                        <div class="fs-16 fw-700 p-3">
                                            <a href="#"
                                                class="dropdown-toggle text-dark filter-section collapsed d-flex align-items-center justify-content-between"
                                                data-toggle="collapse" data-target="#collapse_color">
                                                {{ translate('Filter by color') }}
                                            </a>
                                        </div>
                                        @php
                                            $show = '';
                                            foreach ($colors as $key => $color) {
                                                if (isset($selected_color) && $selected_color == $color->code) {
                                                    $show = 'show';
                                                }
                                            }
                                        @endphp
                                        <div class="collapse {{ $show }}" id="collapse_color">
                                            <div class="p-3 aiz-radio-inline">
                                                @foreach ($colors as $key => $color)
                                                    <label class="aiz-megabox pl-0 mr-2" data-toggle="tooltip"
                                                        data-title="{{ $color->name }}">
                                                        <input type="radio" name="color" value="{{ $color->code }}"
                                                            onchange="filter()"
                                                            @if (isset($selected_color) && $selected_color == $color->code) checked @endif>
                                                        <span
                                                            class="aiz-megabox-elem rounded d-flex align-items-center justify-content-center p-1 mb-2">
                                                            <span class="size-30px d-inline-block rounded"
                                                                style="background: {{ $color->code }};"></span>
                                                        </span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Contents -->
                    <div class="col-xl-9">

                        <!-- Breadcrumb -->
                        <ul class="breadcrumb bg-transparent py-0 px-1">
                            <li class="breadcrumb-item has-transition opacity-50 hov-opacity-100">
                                <a class="text-reset" href="{{ route('home') }}">{{ translate('Home') }}</a>
                            </li>
                            <li class="breadcrumb-item opacity-50 hov-opacity-100">
                                <a class="text-reset" href="{{ route('search') }}">{{ translate('All Products') }}</a>
                            </li>
                            <li class="text-dark fw-600 breadcrumb-item">
                                {{ translate('Image Search Results') }}
                            </li>
                        </ul>

                        <!-- Top Filters -->
                        <div class="text-left">
                            <div class="row gutters-5 flex-wrap align-items-center">
                                <div class="col-lg col-10">
                                    <h1 class="fs-20 fs-md-24 fw-700 text-dark mb-2">
                                        {{ translate('Image Search Results') }}
                                    </h1>
                                    
                                    <!-- Image Search Banner -->
                                    <div class="image-search-banner bg-white border mb-4 p-3">
                                        <div class="d-flex flex-wrap align-items-center">
                                            <!-- Image preview -->
                                            <div class="image-search-preview mr-4 mb-3 mb-md-0">
                                                @if (isset($searchImage))
                                                    <img src="{{ Storage::url($searchImage) }}"
                                                         alt="{{ translate('Search Img') }}"
                                                         class="img-fit shadow-sm"
                                                         style="width: 120px; height: 120px; object-fit: contain; border: 1px solid #eee;">
                                                @endif
                                            </div>
                                            <!-- Info -->
                                            <div class="image-search-info flex-grow-1">
                                                <h4 class="fs-16 fw-700 mb-1">{{ translate('Showing products similar to your image') }}</h4>
                                                <p class="mb-2 opacity-70">{{ translate('We found') }} {{ count($products) }} {{ translate('products') }}</p>
                                                <p class="mb-0 opacity-70 fs-12">{{ translate('Similarity score shows how closely each product matches your image') }}</p>
                                            </div>
                                            <!-- Search again -->
                                            <div class="image-search-actions ml-md-auto mt-3 mt-md-0">
                                                <a href="{{ route('search') }}" class="btn btn-outline-primary">
                                                    {{ translate('New Search') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-2 col-lg-auto d-xl-none mb-lg-3 text-right">
                                    <button type="button" class="btn btn-icon p-0" data-toggle="class-toggle"
                                        data-target=".aiz-filter-sidebar">
                                        <i class="la la-filter la-2x"></i>
                                    </button>
                                </div>
                                
                                <div class="col-6 col-lg-auto mb-3 w-lg-200px">
                                    <select class="form-control form-control-sm aiz-selectpicker rounded-0" name="sort_by"
                                        onchange="filter()">
                                        <option value="similarity" selected>{{ translate('Sort by Similarity') }}</option>
                                        <option value="newest">{{ translate('Newest') }}</option>
                                        <option value="oldest">{{ translate('Oldest') }}</option>
                                        <option value="price-asc">{{ translate('Price low to high') }}</option>
                                        <option value="price-desc">{{ translate('Price high to low') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Products -->
                        <div class="px-3">
                            <div
                                class="row gutters-16 row-cols-xxl-4 row-cols-xl-3 row-cols-lg-4 row-cols-md-3 row-cols-2 border-top border-left">
                                @foreach ($products as $key => $product)
                                    <div class="col border-right border-bottom has-transition hov-shadow-out z-1">
                                        <div class="aiz-card-box h-100 position-relative">
                                            <!-- Similarity badge -->
                                            @if(isset($similarities[$product->id]))
                                                <div class="absolute-top-left z-1 px-2 py-1 m-2 rounded" style="background: rgba(0, 0, 0, 0.5);">
                                                    <span class="text-white fs-12 fw-700">
                                                        {{ round($similarities[$product->id]) }}% {{ translate('Similar') }}
                                                    </span>
                                                </div>
                                            @endif
                                            
                                            <!-- Product box -->
                                            @include(
                                                'frontend.' .
                                                    get_setting('homepage_select') .
                                                    '.partials.product_box_1',
                                                ['product' => $product]
                                            )
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        @if(count($products) == 0)
                            <div class="text-center p-4 bg-white rounded shadow-sm">
                                <img src="{{ static_asset('assets/img/no-result.svg') }}" class="img-fluid mb-4" style="height: 150px;">
                                <h3 class="fw-600">{{ translate('No similar products found') }}</h3>
                                <p class="mb-4">{{ translate('We couldn\'t find products that closely match your image.') }}</p>
                                <a href="{{ route('search') }}" class="btn btn-primary">{{ translate('Try a different search') }}</a>
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </section>

@endsection

@section('script')
    <script type="text/javascript">
        function filter() {
            $('#search-form').submit();
        }

        function rangefilter(arg) {
            $('input[name=min_price]').val(arg[0]);
            $('input[name=max_price]').val(arg[1]);
            filter();
        }
    </script>
@endsection