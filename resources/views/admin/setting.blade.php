@extends('layouts.admin')

@section('title', ' Dashboard')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
        <div class="page-title">
            <div class="row">
            <div class="col-6">
                <h4>
                    Settings</h4>
            </div>
            <div class="col-6">
                <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">                                       
                    <svg class="stroke-icon">
                        <use href="../assets/svg/icon-sprite.svg#stroke-home"></use>
                    </svg></a></li>
                <li class="breadcrumb-item">Dashboard </li>
                <li class="breadcrumb-item active">Settings</li>
                </ol>
            </div>
            </div>
        </div>
        </div>
        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-8">
                    <form class="card" action="{{ route('admin.settings.update') }}" method="post">
                        @csrf
                        <input type="hidden" name="type" value="admin">
                        <div class="card-header d-flex justify-content-between">
                            <h4 class="card-title mb-0">General</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">BTC Wallet Address</label>
                                        <input class="form-control" type="text" value="{{ $settings->btc_wallet }}" name="btc_wallet">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">ETH Wallet Address</label>
                                        <input class="form-control" type="text" value="{{ $settings->eth_wallet }}" name="eth_wallet">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">USDT (TRC20) Wallet Address</label>
                                        <input class="form-control" type="text" value="{{ $settings->trc_wallet }}" name="trc_wallet">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">USDT (ERC20) Wallet Address</label>
                                        <input class="form-control" type="text" value="{{ $settings->erc_wallet }}" name="erc_wallet">
                                    </div>
                                </div>
                                <div class="my-3">

                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Bank Name</label>
                                        <input class="form-control" type="text" value="{{ $settings->bank_name }}" name="bank_name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Account Name</label>
                                        <input class="form-control" type="text" value="{{ $settings->bank_account_name }}" name="bank_account_name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Account Number</label>
                                        <input class="form-control" type="text" value="{{ $settings->bank_account_number }}" name="bank_account_number">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Address</label>
                                        <input class="form-control" type="text" value="{{ $settings->bank_address }}" name="bank_address">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Bank Reference</label>
                                        <input class="form-control" type="text" value="{{ $settings->bank_reference }}" name="bank_reference">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Routing Number</label>
                                        <input class="form-control" type="text" value="{{ $settings->bank_routing_number }}" name="bank_routing_number">
                                    </div>
                                </div>

                                <div class="my-3">

                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Min Deposit</label>
                                        <input class="form-control" type="text" value="{{ $settings->min_cash_deposit }}" name="min_cash_deposit">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Max Deposit</label>
                                        <input class="form-control" type="text" value="{{ $settings->max_cash_deposit }}" name="max_cash_deposit">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Min Withdrawal</label>
                                        <input class="form-control" type="text" value="{{ $settings->min_cash_withdrawal }}" name="min_cash_withdrawal">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Max Withdrawal</label>
                                        <input class="form-control" type="text" value="{{ $settings->max_cash_withdrawal }}" name="max_cash_withdrawal">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-start">
                            <button class="btn btn-success" type="submit">Update Information</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Container-fluid Ends-->
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('admin/assets/js/js-datatables/simple-datatables@latest.js') }}"></script>
    <script src="{{ asset('admin/assets/js/custom-list-product.js') }}"></script>
    <script src="{{ asset('admin/assets/js/owlcarousel/owl.carousel.js') }}"></script>
    <script src="{{ asset('admin/assets/js/ecommerce.js') }}"></script>
    <script src="{{ asset('admin/assets/js/tooltip-init.js') }}"></script>
@endsection
    