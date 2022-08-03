@extends('layouts.app')
@php
use \mobiledetect\mobiledetectlib\Detection;
$detect = new Mobile_Detect;
@endphp
@section('vendor-style')
    <style>
        .content-wrapper {
            padding: 3px;
        }

        .app-content {
            padding: calc(2rem + 2.45rem) 0 0 0rem !important;
            overflow-x: hidden;
            font-family: BinancePlex,Arial,sans-serif!important;
        }

        @media (max-width: 767.98px) {
            html body.navbar-sticky .app-content {
                padding: calc(1rem - 0.8rem + 4.45rem) 0 0 0 !important;
            }
        }

        #app-conainer {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-content: space-between;
            align-items: flex-start;
            flex-wrap: nowrap;
        }

        /*.dark-layout body {
            background-color: #000000 !important;
        }*/

        .tabbable .nav-tabs {
            overflow-x: auto;
            overflow-y: hidden;
            flex-wrap: nowrap;
        }

        .tabbable .nav-tabs .nav-link {
            white-space: nowrap;
        }

        .tableFixHead {
            overflow: auto;
            height: 100px;
        }

        .tableFixHead thead {
            position: sticky;
            top: 0;
            z-index: 1;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        table {
            font-size: 11px;
            font-weight: 500;
            color: rgb(183, 189, 198);
            overflow: hidden;
            width: 100%;
        }

        td {
            height: 12px;
            line-height: 12px;
        }

        .price {
            width: 30%;
            padding-left: 5px;
        }

        .asks .price {
            color:rgb(234,84,85)
        }

        .bids .price {
            color:rgb(40,199,111)
        }

        .bids, .asks {
            font-family: BinancePlex,Arial,sans-serif!important;
            height: 12px;
            line-height: 20px;
        }
        .order-loader {
            position: relative;
            right: 0px;
            bottom: 0px;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background: #000000b5;
        }
        .se-pre-con2 {
            position: absolute;
            top: 50%;
            left: 50%;
        }
        .hidden{
            display: none;
        }
        </style>
@endsection
@section('content')
<div class="container-fluid">
    <div class="row match-height">
        <div class="col-12 col-lg-3 bordered darked px-0 tabbable">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link" id="fav-tab" data-bs-toggle="tab"
                        data-bs-target="#fav" type="button" role="tab" aria-controls="fav"
                        aria-selected="false">
                        <i class="bi bi-star"></i>
                    </a>
                </li>
                @foreach ($pairs as $pair)
                <li class="nav-item">
                    <a class="nav-link" id="{{ $pair }}-tab" data-bs-toggle="tab" data-bs-target="#{{ $pair }}-tabb"
                        aria-controls="{{ $pair }}" role="tab" aria-selected="true">{{ $pair }}</a>
                </li>
                @endforeach
            </ul>

            <div class="row">
                <div class="col-12 card-search custom-data-search">
                    <div class="input-group input-group-sm px-1 mb-1"><span
                            class="input-group-text text-dark border-0" id="basic-addon1"><i
                                class="bi bi-search"></i></span><input type="text" name="search_table"
                            class="form-control form-control-sm text-dark border-0" placeholder="Search...">
                    </div>
                </div>
            </div>

            <div class="tab-content">
                <div class="tab-pane" id="fav" role="tabpanel" aria-labelledby="fav-tab">
                    <script>
                        document.addEventListener ("DOMContentLoaded", function () {
                            var tr_elements = $('.custom-data-table-fav tbody tr');
                            $(document).on('input', 'input[name=search_table]', function () {
                                var search = $(this).val().toUpperCase();
                                var match = tr_elements.filter(function (idx, elem) {
                                    return $(elem).text().trim().toUpperCase().indexOf(search) >= 0 ? elem : null;
                                }).sort();
                                var table_content = $('.custom-data-table-fav tbody');
                                if (match.length == 0) {
                                    table_content.html('<tr><td colspan="100%" class="text-center">Data Not Found</td></tr>');
                                } else {
                                    table_content.html(match);
                                }
                            });
                        });
                    </script>
                    <div class="table-responsive" style="max-height:300px;min-height:300px;overflow-y:auto;">
                        <table class="table text-dark table-sm table-borderless tableFixHead custom-data-table-fav">
                            <thead class="text-muted">
                                <tr>
                                    <th scope="col">Pair</th>
                                    @if (getPlatform('trading')->pair_prices == 1)
                                    <th class="d-lg-none d-xl-block" scope="col">Change</th>
                                    <th scope="col">Price</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($favs as $fav)
                                <tr>
                                    <td>
                                        <div class="d-flex justify-content-start">
                                            <form action="{{ route('user.watchlist.delete') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $fav->id }}">
                                                <button type="submit" style="background: none;padding: 0px;border: none;">
                                                    <i class="me-1 text-warning bi bi-star-fill"></i>
                                                </button>
                                            </form>
                                            <a href="{{ route('user.trade.now',[$fav->currency,$fav->pair]) }}">
                                                <span class="text-dark fw-bold">{{ $fav->currency }}</span>/<span class="text-secondary fw-bold">{{ $fav->pair }}</span>
                                            </a>
                                        </div>
                                    </td>

                                    @if (getPlatform('trading')->pair_prices == 1)
                                    <td class="d-lg-none d-xl-block">
                                        <span class="change-{{ $fav->currency }}{{ $fav->pair }}"></span>
                                    </td>
                                    <td>
                                        <span class="tic-{{ $fav->currency }}{{ $fav->pair }}"></span><i class="tic-{{ $fav->currency }}{{ $fav->pair }}-icon bi"></i>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @foreach ($pairs as $pair)
                <div class="tab-pane @if ($pairs[0] == $pair) active @endif" id="{{ $pair }}-tabb" aria-labelledby="{{ $pair }}-tab" role="tabpanel">
                    <script>
                        document.addEventListener ("DOMContentLoaded", function () {
                            var tr_elements = $('.custom-data-table-{{ $pair }} tbody tr');
                            $(document).on('input', 'input[name=search_table]', function () {
                                var search = $(this).val().toUpperCase();
                                var match = tr_elements.filter(function (idx, elem) {
                                    return $(elem).text().trim().toUpperCase().indexOf(search) >= 0 ? elem : null;
                                }).sort();
                                var table_content = $('.custom-data-table-{{ $pair }} tbody');
                                if (match.length == 0) {
                                    table_content.html('<tr><td colspan="100%" class="text-center">Data Not Found</td></tr>');
                                } else {
                                    table_content.html(match);
                                }
                            });
                        });
                    </script>
                    <div class="table-responsive" style="max-height:300px;min-height:300px;overflow-y:auto;">
                        <table class="table text-dark table-sm table-borderless tableFixHead custom-data-table-{{ $pair }}">
                            <thead class="text-muted">
                                <tr>
                                    <th scope="col">Pair</th>
                                    @if (getPlatform('trading')->pair_prices == 1)
                                    <th class="d-lg-none d-xl-block" scope="col">Change</th>
                                    <th scope="col">Price</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($markets[$currency] as $market)
                                <tr>
                                    <td>
                                        <div class="d-flex justify-content-start">
                                            <form action="{{ route('user.watchlist.store') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="currency" value="{{ $market['currency'] }}">
                                                <input type="hidden" name="pair" value="{{ $market['pair'] }}">
                                                <button type="submit" style="background: none;padding: 0px;border: none;">
                                                    <i class="me-1 text-warning icon-unlock bi bi-star"></i>
                                                </button>
                                            </form>
                                            <a href="{{ route('user.trade.now',[$market['currency'],$market['pair']]) }}">
                                                <span class="text-dark fw-bold">{{ $market['currency'] }}</span>/<span class="text-secondary fw-bold">{{ $market['pair'] }}</span>
                                            </a>
                                        </div>
                                    </td>

                                    @if (getPlatform('trading')->pair_prices == 1)
                                    <td class="d-lg-none d-xl-block">
                                        <span class="change-{{ $market['currency'] }}{{ $market['pair'] }}"></span>
                                    </td>
                                    <td>
                                        <span class="tic-{{ $market['currency'] }}{{ $market['pair'] }}"></span><i class="tic-{{ $market['currency'] }}{{ $market['pair'] }}-icon bi"></i>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="bordered-t">
                <div class="d-flex justify-content-between align-items-center p-1">
                    <span class="text-warning fw-bold">Market Trades</span>
                    <i class="bi bi-chevron-down"></i>
                </div>

                <div class="table-responsive">
                    <table class="table text-dark table-sm table-borderless">
                        <thead class="text-muted">
                            <tr>
                                <th class="text-start" scope="col">Price</th>
                                <th class="text-start" scope="col">Amount</th>
                                <th class="text-end" scope="col">Time</th>
                            </tr>
                        </thead>
                    </table>
                    <table id="tradeTable" class="trade table text-dark table-sm table-borderless"></table>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6 bordered darked px-0">
            <div class="d-flex d-lg-none justify-content-between align-items-center text-2">
                <div class="d-flex flex-column">
                    <span class="text-muted">Last Price: <span class="last_price">------</span><i class="last_price_icon bi"></i></span>
                    <span class="text-muted">24h Change: <span class="day_change">------</span><i class="day_change_icon bi"></i></span>
                </div>
                @if ($provide != 'coinbasepro')
                <div class="d-flex flex-column">
                    <span class="text-muted d-none d-md-block">{{ $symbol }} Volume: <span class="text-dark day_volume_pair">------</span></span>
                    <span class="text-muted d-none d-md-block">{{ $currency }} Volume: <span class="text-dark day_volume_currency">------</span></span>
                </div>
                <div class="d-flex flex-column">
                    <span class="text-muted">24h High: <span class="text-dark day_high">------</span></span>
                    <span class="text-muted">24h Low: <span class="text-dark day_low">------</span></span>
                </div>
                @endif
            </div>
            <div class="d-none d-lg-flex align-items-center p-1 text-2">
                <div class="d-flex align-items-center me-3">
                    <img class="avatar-content" width="24px"
                        src="{{ getImage('assets/images/cryptoCurrency/'. strtolower($symbol).'.png') }}"
                        alt="{{ $symbol }}">
                        <i class="bi bi-chevron-right"></i>
                        <img class="avatar-content" width="24px"
                            src="{{ getImage('assets/images/cryptoCurrency/'. strtolower($currency).'.png') }}"
                            alt="{{ $currency }}">
                </div>
                @if ($provide != 'coinbasepro')
                <div class="d-flex flex-column me-3">
                    <span class="text-muted">24h change</span>
                    <span class="day_change">
                        -------
                    </span>
                </div>
                @endif
                <div class="d-flex flex-column me-3">
                    <span class="text-muted">24h Price Range</span>
                    <span class="text-muted">High: <span class="text-dark day_high">-------</span></span>
                    <span class="text-muted">Low: <span class="text-dark day_low">-------</span>
                </div>
                <div class="d-flex flex-column">
                    <span class="text-muted">24h Volume</span>
                    <span class="text-muted">{{ $symbol }}: <span class="text-dark day_volume_pair">-------</span></span>
                    @if ($provide != 'coinbasepro')
                    <span class="text-muted">{{ $currency }}: <span class="text-dark day_volume_currency">-------</span></span>
                    @endif
                </div>
            </div>
            <!-- TradingView Widget BEGIN -->
            <div id="tradingview_{{ grs() }}" class="bordered-t "></div>
            <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
            <script type="text/javascript">
                new TradingView.widget({
                    "width": "100%",
                    "height": "400",
                    "symbol": "{{ $provider }}:{{ strtoUpper($symbol) }}{{ strtoUpper($currency) }}",
                    "interval": "H",
                    "timezone": "Etc/UTC",
                    "theme": "dark",
                    "style": "1",
                    "locale": "en",
                    "toolbar_bg": "#f1f3f6",
                    "enable_publishing": false,
                    "hide_legend": true,
                    "save_image": false,
                    "container_id": "tradingview_{{ grs() }}"
                });
            </script>
            <!-- TradingView Widget END -->
            <div class="bordered-t darked px-0">
                <div class="order-loader">
                    <div class="se-pre-con2 spinner-border text-primary" role="status">
                        <span class="sr-only"></span>
                    </div>
                </div>
                <div class="order-loaded hidden">
                    <ul class="nav nav-tabs" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pills-market-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-market" type="button" role="tab" aria-controls="pills-market"
                                aria-selected="true">Market</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-limit-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-limit" type="button" role="tab" aria-controls="pills-limit"
                                aria-selected="false">Limit</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-funds-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-funds" type="button" role="tab" aria-controls="pills-funds"
                                aria-selected="false">Funds</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-market" role="tabpanel"
                            aria-labelledby="pills-market-tab">
                            <div class="row pb-1 px-1">
                                <div class="col-6">
                                    <form class="text-center mt-1" id="marketFormBuy">
                                    <label for="basic-url"
                                        class="form-label mb-1 d-flex justify-content-between text-1 text-dark">
                                        <span>Amount</span>
                                        <span>
                                            <a class="text-dark" onclick="getPercBuy('.MarketBuy','#totalbuymarket','0.25')">25%</a>
                                            <a class="text-dark" onclick="getPercBuy('.MarketBuy','#totalbuymarket','0.5')">50%</a>
                                            <a class="text-dark" onclick="getPercBuy('.MarketBuy','#totalbuymarket','0.75')">75%</a>
                                            <a class="text-dark" onclick="getPercBuy('.MarketBuy','#totalbuymarket','1')">100%</a>
                                        </span>
                                    </label>
                                    <div class="input-group input-group-sm mb-1">
                                        <input type="number" class="form-control text-dark border-0 MarketBuy"  min="{{ $limit->min_amount }}" max="{{ $limit->max_amount }}"  step="0.00000001" required="" id="amount"
                                        name="amountMarketBuy" id="MarketBuy" placeholder="Amount" onkeyup="getPriceBuy('.MarketBuy','#totalbuymarket')" aria-label="Amount (to the nearest dollar)">
                                        <span class="input-group-text text-dark border-0">{{ $symbol }}</span>
                                    </div>
                                    <label for="basic-url"
                                        class="form-label mb-1 d-flex justify-content-between text-1 text-dark">
                                        <span>Total</span>
                                        <span>Processing Fee: <span class="text-warning">{{ $fee }}%</span></span>
                                    </label>
                                    <div class="input-group input-group-sm mb-1">
                                        <input type="number" class="form-control text-dark border-0" disabled
                                            aria-label="Amount (to the nearest dollar)" id="totalbuymarket">
                                        <span class="input-group-text text-dark border-0">{{ $currency }}</span>
                                    </div>
                                    <div class="d-grid mt-1">
                                        <button class="btn btn-success btn-sm marketType" id="marketOrderBtnBuy"
                                        type="submit">Buy</button>
                                    </div>
                                </form>
                                </div>
                                <div class="col-6">
                                    <form class="text-center mt-1" id="marketFormSell">
                                    <label for="basic-url"
                                        class="form-label mb-1 d-flex justify-content-between text-1 text-dark">
                                        <span>Amount</span>
                                        <span>
                                            <a class="text-dark" onclick="getPercSell('.MarketSell','#totalsellmarket','0.25')">25%</a>
                                            <a class="text-dark" onclick="getPercSell('.MarketSell','#totalsellmarket','0.5')">50%</a>
                                            <a class="text-dark" onclick="getPercSell('.MarketSell','#totalsellmarket','0.75')">75%</a>
                                            <a class="text-dark" onclick="getPercSell('.MarketSell','#totalsellmarket','1')">100%</a>
                                        </span>
                                    </label>
                                    <div class="input-group input-group-sm mb-1">
                                        <input type="number" class="form-control text-dark border-0 MarketSell" min="{{ $limit->min_amount }}" max="{{ $limit->max_amount }}" step="0.00000001" required="" id="amount"
                                        name="amountMarketSell" placeholder="Amount" onkeyup="getPriceSell('.MarketSell','#totalsellmarket')"
                                            aria-label="Amount (to the nearest dollar)">
                                        <span class="input-group-text text-dark border-0">{{ $symbol }}</span>
                                    </div>
                                    <label for="basic-url"
                                        class="form-label mb-1 d-flex justify-content-between text-1 text-dark">
                                        <span>Total</span>
                                        <span>Processing Fee: <span class="text-warning">{{ $fee }}%</span></span>
                                    </label>
                                    <div class="input-group input-group-sm mb-1">
                                        <input type="number" class="form-control text-dark border-0" disabled
                                            aria-label="Amount (to the nearest dollar)" id="totalsellmarket">
                                        <span class="input-group-text text-dark border-0">{{ $currency }}</span>
                                    </div>
                                    <div class="d-grid mt-1">
                                        <button class="btn btn-danger btn-sm marketType" id="marketOrderBtnSell"
                                        type="submit">Sell</button>
                                    </div>
                                </form>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="pills-limit" role="tabpanel" aria-labelledby="pills-limit-tab">
                            <div class="row pb-1 px-1">
                                <div class="col-6">
                                    <form class="text-center mt-1" id="limitFormBuy">
                                    <label for="basic-url"
                                        class="form-label mb-1 d-flex justify-content-between text-1 text-dark">
                                        <span>Price</span>
                                        <span class="text-dark">Best Ask</span>
                                    </label>
                                    <div class="input-group input-group-sm mb-1">
                                        <input type="number" class="form-control text-dark border-0 priceNowAsk" step="0.00000001" required="" id="price"
                                        name="priceBuy" placeholder="Price"
                                            aria-label="Amount (to the nearest dollar)">
                                        <span class="input-group-text text-dark border-0">{{ $currency }}</span>
                                        <span class="input-group-text text-dark border-0 ms-1">
                                            <i class="bi bi-caret-up-fill"></i>
                                            <i class="bi bi-caret-down-fill"></i>
                                        </span>
                                    </div>
                                    <label for="basic-url"
                                        class="form-label mb-1 d-flex justify-content-between text-1 text-dark">
                                        <span>Amount</span>
                                        <span>
                                            <a class="text-dark" onclick="getPercBuy('.LimitBuy','#totalbuylimit','0.25')">25%</a>
                                            <a class="text-dark" onclick="getPercBuy('.LimitBuy','#totalbuylimit','0.5')">50%</a>
                                            <a class="text-dark" onclick="getPercBuy('.LimitBuy','#totalbuylimit','0.75')">75%</a>
                                            <a class="text-dark" onclick="getPercBuy('.LimitBuy','#totalbuylimit','1')">100%</a>
                                        </span>
                                    </label>
                                    <div class="input-group input-group-sm mb-1">
                                        <input type="number" class="form-control text-dark border-0 LimitBuy" min="{{ $limit->min_amount }}" max="{{ $limit->max_amount }}"  step="0.00000001" required="" id="amount"
                                        name="amountLimitBuy"
                                            aria-label="Amount (to the nearest dollar)" onkeyup="getPriceBuy('.LimitBuy','#totalbuylimit')">
                                        <span class="input-group-text text-dark border-0">{{ $symbol }}</span>
                                    </div>
                                    <label for="basic-url"
                                        class="form-label mb-1 d-flex justify-content-between text-1 text-dark">
                                        <span>Total</span>
                                        <span>Processing Fee: <span class="text-warning">{{ $fee }}%</span></span>
                                    </label>
                                    <div class="input-group input-group-sm mb-1">
                                        <input type="number" class="form-control text-dark border-0"
                                            aria-label="Amount (to the nearest dollar)" id="totalbuylimit" disabled>
                                        <span class="input-group-text text-dark border-0">{{ $currency }}</span>
                                    </div>
                                    <div class="d-grid mt-1">
                                        <button class="btn btn-success btn-sm limitType" id="limitOrderBtnBuy" type="submit">Buy</button>
                                    </div>
                                </form>
                                </div>
                                <div class="col-6">
                                    <form class="text-center mt-1" id="limitFormSell">
                                    <label for="basic-url"
                                        class="form-label mb-1 d-flex justify-content-between text-1 text-dark">
                                        <span>Price</span>
                                        <span class="text-dark">Best Bid</span>
                                    </label>
                                    <div class="input-group input-group-sm mb-1">
                                        <input type="number" class="form-control text-dark border-0 priceNowBid" step="0.00000001" required="" id="price"
                                        name="priceSell" placeholder="Price"
                                            aria-label="Amount (to the nearest dollar)">
                                        <span class="input-group-text text-dark border-0">{{ $currency }}</span>
                                        <span class="input-group-text text-dark border-0 ms-1">
                                            <i class="bi bi-caret-up-fill"></i>
                                            <i class="bi bi-caret-down-fill"></i>
                                        </span>
                                    </div>
                                    <label for="basic-url"
                                        class="form-label mb-1 d-flex justify-content-between text-1 text-dark">
                                        <span>Amount</span>
                                        <span>
                                            <a class="text-dark" onclick="getPercSell('.LimitSell','#totalselllimit','0.25')">25%</a>
                                            <a class="text-dark" onclick="getPercSell('.LimitSell','#totalselllimit','0.5')">50%</a>
                                            <a class="text-dark" onclick="getPercSell('.LimitSell','#totalselllimit','0.75')">75%</a>
                                            <a class="text-dark" onclick="getPercSell('.LimitSell','#totalselllimit','1')">100%</a>
                                        </span>
                                    </label>
                                    <div class="input-group input-group-sm mb-1">
                                        <input type="number" class="form-control text-dark border-0 LimitSell" min="{{ $limit->min_amount }}" max="{{ $limit->max_amount }}"  step="0.00000001" required="" id="amount"
                                        name="amountLimitSell"
                                            aria-label="Amount (to the nearest dollar)" onkeyup="getPriceSell('.LimitSell','#totalselllimit')">
                                        <span class="input-group-text text-dark border-0">{{ $symbol }}</span>
                                    </div>
                                    <label for="basic-url"
                                        class="form-label mb-1 d-flex justify-content-between text-1 text-dark">
                                        <span>Total</span>
                                        <span>Processing Fee: <span class="text-warning">{{ $fee }}%</span></span>
                                    </label>
                                    <div class="input-group input-group-sm mb-1">
                                        <input type="number" class="form-control text-dark border-0"
                                            aria-label="Amount (to the nearest dollar)" id="totalselllimit" disabled>
                                        <span class="input-group-text text-dark border-0">{{ $currency }}</span>
                                    </div>
                                    <div class="d-grid mt-1">
                                        <button class="btn btn-danger btn-sm limitType" id="limitOrderBtnSell" type="submit">Sell</button>
                                    </div>
                                </form>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-funds" role="tabpanel" aria-labelledby="pills-funds-tab">
                            @livewire('exchange.balance', ['symbol' => $symbol, 'currency' => $currency])
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="col-12 col-lg-3 bordered darked px-0">
            {{-- <ul class="nav nav-tabs" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pills-graph-tab" data-bs-toggle="pill"
                        data-bs-target="#pills-graph" type="button" role="tab" aria-controls="pills-graph"
                        aria-selected="false">
                        <i class="bi bi-graph-up text-dark"></i>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-graph-up-tab" data-bs-toggle="pill"
                        data-bs-target="#pills-graph-up" type="button" role="tab" aria-controls="pills-graph-up"
                        aria-selected="false">
                        <i class="bi bi-graph-up-arrow text-success"></i>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-graph-down-tab" data-bs-toggle="pill"
                        data-bs-target="#pills-graph-down" type="button" role="tab" aria-controls="pills-graph-down"
                        aria-selected="false">
                        <i class="bi bi-graph-down-arrow text-danger"></i>
                    </button>
                </li>
            </ul>
            <div class="tab-content" id="pills-graph-tabContent">
                <div class="tab-pane fade show active" id="pills-graph" role="tabpanel"
                    aria-labelledby="pills-graph-tab"> --}}

                        <div class="table-responsive ask_orderbooks">
                            <div class="ask_orderbook">
                                <table class="table ask_table text-dark table-sm table-borderless" style="overflow-x:hidden;">
                                    <thead class="text-muted">
                                        <tr>
                                            <th class="text-start" scope="col">Price</th>
                                            <th class="text-start" scope="col">Amount</th>
                                            <th class="text-end" scope="col">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="asks">
                                        <tr class="ask">
                                            <td class="text-start"><span class="price"></span></td>
                                            <td class="text-start"><span class="size"></span></td>
                                            <td class="text-end"><span class="total"></span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    <div class="table-responsive bordered-y">
                        <table class="table text-dark table-sm table-borderless my-auto">
                            <tbody>
                                <tr>
                                    <td class="text-mute ">
                                        <span class="fs-5">Last Price: </span>
                                        <span class="fs-3 best_ask"></span><i class="fs-3 best_ask_icon bi"></i>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="table-responsive bid_orderbooks">
                        <div class="bid_orderbook">
                            <table class="table bid_table text-dark table-sm table-borderless" style="overflow-x:hidden;">
                                <tbody class="bids">
                                    <tr class="bid">
                                        <td class="text-start"><span class="price"></span></td>
                                        <td class="text-start"><span class="size"></span></td>
                                        <td class="text-end"><span class="total"></span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
            {{--<div class="tab-pane fade" id="pills-graph-down" role="tabpanel" aria-labelledby="pills-graph-down-tab">
                    <div class="table-responsive">
                        <table class="table text-dark table-sm table-borderless">
                            <thead class="text-muted">
                                <tr>
                                    <th scope="col">Price</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Total</th>
                                </tr>
                            </thead>
                        </table>
                        <div class="card-body mb-0 p-2">
                            <table class="table small m-0">
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="pills-graph-up" role="tabpanel" aria-labelledby="pills-graph-up-tab">

                    <div class="table-responsive">
                        <table class="table text-dark table-sm table-borderless">
                            <thead class="text-muted">
                                <tr>
                                    <th scope="col">Price</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Total</th>
                                </tr>
                            </thead>
                        </table>
                        <table class="bids_only" ></table>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>

    <div class="row match-height">
        <div class="col-12 bordered darked px-0 d-none d-md-block ">
            <ul class="nav nav-tabs" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill"
                        data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home"
                        aria-selected="true">Open Orders</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill"
                        data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile"
                        aria-selected="false">Order History</button>
                </li>
                {{-- <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-contact-tab" data-bs-toggle="pill"
                        data-bs-target="#pills-contact" type="button" role="tab" aria-controls="pills-contact"
                        aria-selected="false">Trade History</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-fundss-tab" data-bs-toggle="pill"
                        data-bs-target="#pills-fundss" type="button" role="tab" aria-controls="pills-fundss"
                        aria-selected="false">Balances</button>
                </li> --}}
            </ul>

            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                    <div class="table-responsive">
                        <table class="table text-dark table-sm table-borderless">
                            <thead>
                                <tr>
                                    <th scope="col">TxHash</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Pair</th>
                                    <th scope="col">Side</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Filled</th>
                                    <th scope="col">Status</th>
                                    {{-- <th scope="col">Action</th> --}}
                                </tr>
                            </thead>
                            @livewire('exchange.open-orders', ['provider' => $provide, 'symbol' => $symbol.'/'.$currency])
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                    {{-- <div class="orders-widget__subtoolbar p-1 d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input class="form-check-input custom-checkbox" type="checkbox" value=""
                                id="flexCheckDefault">
                            <label class="form-check-label" for="flexCheckDefault">
                                Hide Other Pairs
                            </label>
                        </div>

                        <div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                            <button type="button" class="btn btn-secondary me-1">1 Day</button>
                            <button type="button" class="btn btn-secondary me-1">1 Week</button>
                            <button type="button" class="btn btn-secondary me-1">1 Month</button>
                            <button type="button" class="btn btn-secondary me-1">3 Months</button>
                        </div>
                    </div> --}}

                    <div class="table-responsive">
                        <table class="table text-dark table-sm table-borderless">
                            <thead>
                                <tr>
                                    <th scope="col">TxHash</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Pair</th>
                                    <th scope="col">Side</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Filled</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            @livewire('exchange.orders', ['provider' => $provide, 'symbol' => $symbol.'/'.$currency])
                        </table>
                    </div>
                </div>

                {{-- <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
                    <div class="orders-widget__subtoolbar p-1 row">
                        <div class="col-2">
                            <div class="form-check">
                                <input class="form-check-input custom-checkbox" type="checkbox" value=""
                                    id="flexCheckDefault">
                                <label class="form-check-label" for="flexCheckDefault">
                                    Hide Other Pairs
                                </label>
                            </div>
                        </div>
                        <div class="col-10 d-flex align-items-center justify-content-end">
                            <div class="me-1">
                                <label for="">Pair: </label>
                                <input type="text" class="text-dark border-0" style="width: 50px;"
                                    placeholder="Coin">
                                <label for="">/</label>
                                <div class="btn-group me-2">
                                    <button class="btn btn-secondary btn-sm dropdown-toggle" type="button"
                                        id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                        BNB
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-black dropdown-menu-end"
                                        aria-labelledby="dropdownMenuButton1">
                                        <li><a class="dropdown-item" href="#">BNB</a></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="me-2">
                                <label for="">Type: </label>
                                <div class="btn-group">
                                    <button class="btn btn-secondary btn-sm dropdown-toggle" type="button"
                                        id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                        <span class="text-success">Buy</span> &
                                        <span class="text-danger">Sell</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-black" aria-labelledby="dropdownMenuButton1">
                                        <li>
                                            <a class="dropdown-item" href="#">
                                                <span class="text-success">Buy</span> &
                                                <span class="text-danger">Sell</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="#">
                                                <span class="text-success">Buy</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="#">
                                                <span class="text-danger">Sell</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                                <button type="button" class="btn btn-secondary me-1">1 Day</button>
                                <button type="button" class="btn btn-secondary me-1">1 Week</button>
                                <button type="button" class="btn btn-secondary me-1">1 Month</button>
                                <button type="button" class="btn btn-secondary me-1">3 Months</button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table text-dark table-sm table-borderless">
                            <thead>
                                <tr>
                                    <th scope="col">BlockHeight</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Pair</th>
                                    <th scope="col">Side</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Filled</th>
                                    <th scope="col">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="pills-fundss" role="tabpanel" aria-labelledby="pills-fundss-tab">
                    <div class="orders-widget__subtoolbar p-1 d-flex justify-content-end align-items-center">
                        <div class="text-1">Estimated Value: 0BNB / $0.00</div>
                    </div>

                    <div class="table-responsive">
                        <table class="table text-dark table-sm table-borderless">
                            <thead>
                                <tr>
                                    <th scope="col">Asset</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Total Balance</th>
                                    <th scope="col">Available Balance</th>
                                    <th scope="col">Frozen</th>
                                    <th scope="col">In Order</th>
                                    <th scope="col">{{ $symbol }} Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
</div>

@endsection
@section('page-script')
<script type="text/javascript" src="{{ mix('/vendors/js/ccxt.js') }}"></script>
    <script>
        var bestAsk;
        var bestBid;
        var symbol = "{{ $symbol }}";
        var currency = "{{ $currency }}";
        let old = [];
        var last_price;
        var day_change;
        var bestAsker;
        class Demo {
            constructor () {
                this.sideLength = 15
                this.refreshRate = 250
                const config = { enableRateLimit: true, newUpdates: true , proxy: '{{$cors}}','options': { 'tradesLimit': 10,}}
                this.params = [
                    {
                        exchange: new ccxt.{{ $exchange }} (config),
                        symbol: '{{strtoupper($symbol)}}/{{strtoupper($currency)}}',
                        ba: 0,
                    },
                ]

                const params = this.params[0]
                const exchange = params.exchange
                params.$asks = []
                params.$bids = []
                params.$trades = []
                const $ask_orderbooks = $('.ask_orderbooks')
                const $ask_orderbook = $($ask_orderbooks.find ('.ask_orderbook')[0])
                $ask_orderbooks.empty ()
                const $bid_orderbooks = $('.bid_orderbooks')
                const $bid_orderbook = $($bid_orderbooks.find ('.bid_orderbook')[0])
                $bid_orderbooks.empty ()
                for (let i = 0; i < this.params.length; i++) {
                    const params = this.params[i]
                    const exchange = params.exchange
                    const $ask_clone = $ask_orderbook.clone ()
                    const $bid_clone = $bid_orderbook.clone ()
                    params.$asks = []
                    params.$bids = []
                    const $ask_table = $($ask_clone.find ('.ask_table')[0])
                    const $bid_table = $($bid_clone.find ('.bid_table')[0])
                    let $asks = $($ask_table.find ('.asks')[0])
                    const $ask = $($asks.find ('.ask')[0])
                    $asks.empty ()
                    let $bids = $($bid_table.find ('.bids')[0])
                    const $bid = $($bids.find ('.bid')[0])
                    $bids.empty ()
                    for (let j = 0; j < this.sideLength; j++) {
                        // ------------------------------------------------------------
                        // asks
                        const $askClone = $ask.clone ()
                        let $price = $($askClone.find ('.price')[0])
                        let $size = $($askClone.find ('.size')[0])
                        let $total = $($askClone.find ('.total')[0])
                        $asks.append ($askClone)
                        // unshift for proper ordering
                        this.params[i].$asks.unshift ({ $price, $size, $total })
                        // ------------------------------------------------------------
                        // bids
                        const $bidClone = $bid.clone ()
                        $price = $($bidClone.find ('.price')[0])
                        $size = $($bidClone.find ('.size')[0])
                        $total = $($bidClone.find ('.total')[0])
                        $bids.append ($bidClone)
                        // push for proper ordering
                        this.params[i].$bids.push ({ $price, $size, $total })
                    }
                    $ask_table.append ($asks)
                    $bid_table.append ($bids)
                    $ask_orderbooks.append ($ask_clone)
                    $bid_orderbooks.append ($bid_clone)
                }
            }

            static formatPrice (price) {
                return ccxt.decimalToPrecision (price, ccxt.ROUND, 9, ccxt.SIGNIFICANT_DIGITS, ccxt.PAD_WITH_ZERO)
            }

            static formatSize (size) {
                return ccxt.decimalToPrecision (size, ccxt.ROUND, 6, ccxt.DECIMAL_PLACES, ccxt.PAD_WITH_ZERO)
            }

            static formatTotal (total) {
                return ccxt.decimalToPrecision (total, ccxt.ROUND, 3, ccxt.DECIMAL_PLACES, ccxt.PAD_WITH_ZERO)
            }

            static formatTime (time) {
                return time.split("T")[1].split(".")[0]
            }

            async updateOrderbook (orderbook, params) {
                const now = Date.now ()
                if (!params.lastUpdated || ((now - params.lastUpdated) > this.refreshRate)) {
                    for (let i = 0; i < this.sideLength; i++) {
                        params.$asks[i].$price.text (Demo.formatPrice (orderbook['asks'][i][0]))
                        params.$asks[i].$size.text (Demo.formatSize (orderbook['asks'][i][1]))
                        params.$asks[i].$total.text (Demo.formatTotal (orderbook['asks'][i][0] * orderbook['asks'][i][1]))
                        params.$bids[i].$price.text (Demo.formatPrice (orderbook['bids'][i][0]))
                        params.$bids[i].$size.text (Demo.formatSize (orderbook['bids'][i][1]))
                        params.$bids[i].$total.text (Demo.formatTotal (orderbook['bids'][i][0] * orderbook['bids'][i][1]))

                        this.best_asks = $('.best_ask')
                        this.best_ask_Icons = $('.best_ask_icon')
                        const best_ask = this.best_asks
                        const best_ask_Icon = this.best_ask_Icons
                        if (!bestAsker || orderbook['asks'][0][0] > bestAsker){
                            best_ask.text (orderbook['asks'][0][0])
                            best_ask.toggleClass('text-success')
                            best_ask_Icon.addClass('bi-arrow-up text-success').removeClass('bi-arrow-down text-danger')
                        } else if(orderbook['asks'][0][0] < bestAsker){
                            best_ask.text (orderbook['asks'][0][0])
                            best_ask.toggleClass('text-danger')
                            best_ask_Icon.addClass('bi-arrow-down text-danger').removeClass('bi-arrow-up text-success')
                        }
                        bestAsker = orderbook['asks'][0][0];

                        //this.priceNowAsks = $('.priceNowAsk')
                        //const priceNowAsk = this.priceNowAsks
                        //priceNowAsk.val(orderbook['asks'][0][0]);
                        bestAsk = orderbook['asks'][0][0];

                        //this.priceNowBids = $('.priceNowBid')
                        //const priceNowBid = this.priceNowBids
                        //priceNowBid.val(orderbook['bids'][0][0]);
                        bestBid = orderbook['bids'][0][0];
                    }
                    params.lastUpdated = now
                } else {
                    await ccxt.sleep (parseInt (this.refreshRate / 2))
                }
            }

            async loopOrderbook (i) {
                const params = this.params[i]
                const { exchange, symbol, ba } = params
                while (true) {
                    if (document.hidden) {
                        await ccxt.sleep (1000)
                        continue
                    }
                    try {
                        const orderbook = await exchange.watchOrderBook (symbol)
                        await this.updateOrderbook (orderbook, params)
                    } catch (e) {
                        break
                    }
                }
            }

            startOrderbook () {
                Object.keys (this.params).map (i =>
                    new Promise ((resolve, reject) =>
                        this.loopOrderbook (i).catch (e => { resolve (e) })))
            }

            async updateTrades (trade, params) {
                let lastId = ''
                let color = ''
                var tabled = document.getElementById('tradeTable');
                for (let i = 0; i < trade.length; i++) {
                    if (trade[i]['id'] > lastId) {
                        if(trade[i]['side'] == 'sell') {
                            color = "rgb(246,70,93)"
                        } else {
                            color = "rgb(14,203,129)"
                        }
                        let row = $('<tr>')
                            .append($('<td>').css("color", color).addClass("price").append($("<span>").text(Demo.formatPrice(trade[i]['price']))))
                            .append($('<td>').addClass("amount").append($("<span>").text(Demo.formatSize(trade[i]['amount']))))
                            .append($('<td>').addClass("time").append($("<span>").text(Demo.formatTime(trade[i]['datetime']))));
                        $('.trade').prepend(row);
                        lastId = trade[i]['id']
                    }
                    if (tabled.rows.length > 10) {
                        tabled.deleteRow(tabled.rows.length -1);
                    }
                }
            }

            async loopTrades (i) {
                const params = this.params[i]
                const { exchange, symbol } = params
                while (true) {
                    if (document.hidden) {
                        await ccxt.sleep (1000)
                        continue
                    }
                    try {
                        const trade = await exchange.watchTrades (symbol)
                        await this.updateTrades (trade, params)
                    } catch (e) {
                        break
                    }
                }
            }

            startTrades () {
                Object.keys (this.params).map (i =>
                    new Promise ((resolve, reject) =>
                        this.loopTrades (i).catch (e => { resolve (e) })))
            }

            async updateTicker (tick, params) {
                this.tickElements = $('.last_price')
                this.tickIcons = $('.last_price-icon')
                const tickElement = this.tickElements
                const tickIcon = this.tickIcons
                if (!last_price || tick['last'] > last_price){
                    tickElement.text (tick['last'])
                    tickElement.toggleClass('text-success')
                    tickIcon.toggleClass('bi-arrow-up text-success')
                } else if(tick['last'] < last_price){
                    tickElement.text (tick['last'])
                    tickElement.toggleClass('text-danger')
                    tickIcon.toggleClass('bi-arrow-down text-danger')
                }
                last_price = tick['last'];

                @if ($provide != 'coinbasepro')
                    this.percentageElements = $('.day_change')
                    this.percentageIcons = $('.day_change-icon')
                    const percentageElement = this.percentageElements
                    const percentageIcon = this.percentageIcons
                    if (!day_change || tick['percentage'] > day_change){
                        percentageElement.text (tick['percentage'])
                        percentageElement.toggleClass('text-success')
                        percentageIcon.toggleClass('bi-arrow-up text-success')
                    } else if(tick['percentage'] < day_change){
                        percentageElement.text (tick['percentage'])
                        percentageElement.toggleClass('text-danger')
                        percentageIcon.toggleClass('bi-arrow-down text-danger')
                    }
                    day_change = tick['percentage'];
                    this.day_volume_currencys = $('.day_volume_currency')
                    const day_volume_currency = this.day_volume_currencys
                    day_volume_currency.text (new Intl.NumberFormat().format(tick['quoteVolume']))
                @endif

                this.day_highs = $('.day_high')
                const day_high = this.day_highs
                day_high.text (Demo.formatPrice (tick['high']))

                this.day_lows = $('.day_low')
                const day_low = this.day_lows
                day_low.text (Demo.formatPrice (tick['low']))

                this.day_volume_pairs = $('.day_volume_pair')
                const day_volume_pair = this.day_volume_pairs
                day_volume_pair.text (new Intl.NumberFormat().format(tick['baseVolume']))
                if($(".order-loaded").hasClass('hidden')){
                    $(".order-loader").fadeOut("slow");
                    $(".order-loaded").removeClass('hidden');
                }
            }

            async loopTicker (i) {
                const params = this.params[i]
                const { exchange, symbol} = params
                while (true) {
                    if (document.hidden) {
                        await ccxt.sleep (1000)
                        continue
                    }
                    try {
                        const ticker = await exchange.watchTicker (symbol)
                        await this.updateTicker (ticker, params)
                    } catch (e) {
                        break
                    }
                }
            }

            startTicker () {
                Object.keys (this.params).map (i =>
                    new Promise ((resolve, reject) =>
                        this.loopTicker (i).catch (e => { resolve (e) })))
            }
            handle (tickers) {
                this.tickerElements = this.tickerElements || {}
                this.tickerIcons = this.tickerIcons || {}
                this.changeElements = this.changeElements || {}
                for (const [ symbol, ticker ] of Object.entries (tickers)) {
                    const symbolWithoutSlash = symbol.replace('/','')
                    if (!(symbol in this.tickerElements)) {
                        this.tickerElements[symbol] = $('.tic-'+ symbolWithoutSlash)
                    }
                    if (!(symbol in this.tickerIcons)) {
                        this.tickerIcons[symbol] = $('.tic-'+symbolWithoutSlash+'-icon')
                    }
                    const tickerElement = this.tickerElements[symbol]
                    const tickerIcon = this.tickerIcons[symbol]
                    if (!old[symbol] || ticker['last'] > old[symbol]){
                        tickerElement.text (ticker['last'])
                        tickerElement.toggleClass('text-success')
                        tickerIcon.toggleClass('bi-arrow-up text-success')
                    } else if(ticker['last'] < old[symbol]){
                        tickerElement.text (ticker['last'])
                        tickerElement.toggleClass('text-danger')
                        tickerIcon.toggleClass('bi-arrow-down text-danger')
                    }
                    old[symbol] = ticker['last']
                    if (!(symbol in this.changeElements)) {
                        this.changeElements[symbol] = $('.change-'+symbolWithoutSlash)
                    }
                    const changeElement = this.changeElements[symbol]
                    if (ticker['change'] > 0){
                        changeElement.text (Demo.formatTotal (ticker['change'])+'%')
                        changeElement.addClass('text-success').removeClass('text-danger')
                    } else if (ticker['change'] < 0){
                        changeElement.text (Demo.formatTotal (ticker['change'])+'%')
                        changeElement.addClass('text-danger').removeClass('text-success')
                    } else {
                        changeElement.text (Demo.formatTotal (ticker['change'])+'%')
                    }
                }
            }

            async loop (i) {
                const params = this.params[i]
                const { exchange } = params
                while (true) {
                    if (document.hidden) {
                        await ccxt.sleep (1000)
                        continue
                    }
                    try {
                        const tickers = await exchange.fetchTickers ()
                        await this.handle (tickers)
                    } catch (e) {
                        break
                    }
                }
            }

            startTickers () {
                Object.keys (this.params).map (i =>
                    new Promise ((resolve, reject) =>
                        this.loop (i).catch (e => { resolve (e) })))
            }
        }

        const demo = new Demo()

        $(document).ready(async function () {
            demo.startOrderbook ()
            demo.startTrades ()
            demo.startTicker ()
            @if (getPlatform('trading')->pair_prices == 1)
                demo.startTickers ()
            @endif
            $('.icon-unlock').hover(function () {
                $(this).addClass('bi-star-fill');
                $(this).removeClass('bi-star');
            }, function () {
                $(this).addClass('bi-star');
                $(this).removeClass('bi-star-fill');
            });
            $("#marketFormBuy").on('submit', function (event1) {
                event1.preventDefault();

                if(bestAsk != null){
                    var amount = $('input[name="amountMarketBuy"]').val();
                    $.ajax({
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        },
                        url: "{{ route('user.trade.store') }}",
                        method: "POST",
                        data: {
                            amount: amount,
                            symbol: symbol,
                            currency: currency,
                            tradeType: "market",
                            type: 1,
                            wallettype: "trading",
                            price: bestAsk
                        },
                        beforeSend: function() {
                            $("#marketOrderBtnBuy").prop('disabled', true); // disable button
                        },
                        success: function (response) {
                            $("#marketOrderBtnBuy").prop('disabled', false); // disable button
                            if (response == 1) {
                                notify('success', 'Order placed successfully');
                                Livewire.emit('refreshBalance');
                            } else {
                                $.each(response, function (i, val) {
                                    notify('error', val)
                                });
                            }
                        }
                    });
                } else {
                    notify('error', 'Try Again');
                }
            });

            $("#marketFormSell").on('submit', function (event2) {
                event2.preventDefault();
                if(bestBid != null){
                    var amount = $('input[name="amountMarketSell"]').val();
                    $.ajax({
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        },
                        url: "{{ route('user.trade.store') }}",
                        method: "POST",
                        data: {
                            amount: amount,
                            symbol: symbol,
                            currency: currency,
                            tradeType: "market",
                            type: 2,
                            wallettype: "trading",
                            price: bestBid
                        },
                        beforeSend: function() {
                            $("#marketOrderBtnSell").prop('disabled', true); // disable button
                        },
                        success: function (response) {
                            $("#marketOrderBtnSell").prop('disabled', false); // disable button
                            if (response == 1) {
                                notify('success', 'Order placed successfully');
                                Livewire.emit('refreshBalance');
                            } else {
                                $.each(response, function (i, val) {
                                    notify('error', val)
                                });
                            }
                        }
                    });
                } else {
                    notify('error', 'Try Again');
                }
            });

            $("#limitFormBuy").on('submit', function (event3) {
                event3.preventDefault();
                if(bestAsk != null){
                    var amount = $('input[name="amountLimitBuy"]').val();
                    var price = $('input[name="priceBuy"]').val();
                    $.ajax({
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        },
                        url: "{{ route('user.trade.store') }}",
                        method: "POST",
                        data: {
                            amount: amount,
                            price: price,
                            symbol: symbol,
                            currency: currency,
                            tradeType: "limit",
                            type: 1,
                            wallettype: "trading",
                            price: bestAsk
                        },
                        beforeSend: function() {
                            $("#limitOrderBtnBuy").prop('disabled', true); // disable button
                        },
                        success: function (response) {
                            $("#limitOrderBtnBuy").prop('disabled', false); // disable button
                            if (response == 1) {
                                notify('success', 'Order placed successfully');
                                Livewire.emit('refreshBalance');
                            } else {
                                $.each(response, function (i, val) {
                                    notify('error', val)
                                });
                            }
                        }
                    });
                } else {
                    notify('error', 'Try Again');
                }
            });

            $("#limitFormSell").on('submit', function (event4) {
                event4.preventDefault();
                if(bestBid != null){
                    var amount = $('input[name="amountLimitSell"]').val();
                    var price = $('input[name="priceSell"]').val();
                    $.ajax({
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        },
                        url: "{{ route('user.trade.store') }}",
                        method: "POST",
                        data: {
                            amount: amount,
                            price: price,
                            symbol: symbol,
                            currency: currency,
                            tradeType: "limit",
                            type: 2,
                            wallettype: "trading",
                            price: bestBid
                        },
                        beforeSend: function() {
                            $("#limitOrderBtnSell").prop('disabled', true); // disable button
                        },
                        success: function (response) {
                            $("#limitOrderBtnSell").prop('disabled', false); // disable button
                            if (response == 1) {
                                notify('success', 'Order placed successfully');
                                Livewire.emit('refreshBalance');
                            } else {
                                $.each(response, function (i, val) {
                                    notify('error', val)
                                });
                            }
                        }
                    });
                } else {
                    notify('error', 'Try Again');
                }
            });

            /*$("#cancelOrder").on('submit', function (event5) {
                event5.preventDefault();
                var symbol = $(".orderSymbol").attr("value");
                var order_id = $(".cancelBtn").attr("value");
                $.ajax({
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    },
                    url: "{{ route('user.trade.cancel') }}",
                    method: "POST",
                    data: {
                        symbol: symbol,
                        order_id: order_id,
                    },
                    beforeSend: function() {
                        $("#cancelBtn").prop('disabled', true); // disable button
                    },
                    success: function (response) {
                        $("#cancelBtn").prop('disabled', false); // disable button
                        if (response == 1) {
                            notify('success', 'Order '+order_id+' Cancelled successfully');
                            Livewire.emit('refreshBalance');
                        } else {
                            $.each(response, function (i, val) {
                                notify('error', val)
                            });
                        }
                    }
                });
            });*/
        });

        function getPercBuy($a,$b,$value){
            if(bestAsk != null){
                $($a).val((({{ $wallet['currency']->balance ?? '0' }} * $value) / {{ $pfee }}) / bestAsk);
                $($b).val(({{ $wallet['currency']->balance ?? '0' }} * $value).toFixed(8));
            } else {
                notify('error', 'Try Again');
            }
        }
        function getPercSell($a,$b,$value){
            if(bestBid != null){
                $($a).val({{ $wallet['symbol']->balance ?? '0' }} * $value);
                $($b).val(({{ $wallet['symbol']->balance ?? '0' }} * $value * bestBid ).toFixed(8));
            } else {
                notify('error', 'Try Again');
            }
        }
        function getPriceBuy($a,$b){
            if(bestAsk != null){
                $($b).val(($($a).val() * bestAsk * {{ $pfee }} ).toFixed(8));
            } else {
                notify('error', 'Try Again');
            }
        }
        function getPriceSell($a,$b){
            if(bestBid != null){
                $($b).val(($($a).val() * bestBid ).toFixed(8));
            } else {
                notify('error', 'Try Again');
            }
        }

    </script>
@endsection
