@extends('layouts.admin')

@section('title', ' Dashboard')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
        <div class="page-title">
            <div class="row">
            <div class="col-6">
                <h4>User Details</h4>
            </div>
            <div class="col-6">
                <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">                                       
                    <svg class="stroke-icon">
                        <use href="../assets/svg/icon-sprite.svg#stroke-home"></use>
                    </svg></a></li>
                <li class="breadcrumb-item">Users</li>
                <li class="breadcrumb-item active"> User detail</li>
                </ol>
            </div>
            </div>
        </div>
        </div>
        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="edit-profile">
                <div class="row">
                <div class="col-xl-5">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Profile</h4>
                            <div class="card-options"><a class="card-options-collapse" href="#" data-bs-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a><a class="card-options-remove" href="#" data-bs-toggle="card-remove"><i class="fe fe-x"></i></a></div>
                        </div>
                        <div class="card-body">
                                <div class="row mb-2">
                                    <div class="profile-title">
                                    <div class="media">   
                                        @if($user->avatar)                     
                                            <img class="img-70 rounded-circle" alt="" src="{{$user->avatar}}">
                                        @else
                                            <img class="img-70 rounded-circle" alt="" src="https://cdn-icons-png.flaticon.com/512/6596/6596121.png">
                                        @endif
                                        <div class="media-body">
                                        <h5 class="fw-bold f-20">{{ $user->first_name }} {{ $user->last_name }}</h5>
                                        <p>@ {{ $user->username }}</p>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-7">
                                        <div class="mb-3">
                                            <label class="form-label">Email-Address</label>
                                            <input class="form-control" value="{{ $user->email }}" disabled>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Phone</label>
                                            <input class="form-control" value="{{ $user->phone }}" disabled>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Country</label>
                                            <input class="form-control" value="{{ $user->country->name }}" disabled>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Currency</label>
                                            <input class="form-control" value="{{ $user->currency->name }} ({{ $user->currency->symbol }})" disabled>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <span class="mx-2 px-4 badge @if($user->status == 'active') badge-light-success @else badge-light-danger @endif">
                                                @if($user->status == 'active') Active @else Suspended @endif
                                            </span>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">KYC</label>
                                            <span class="mx-2 px-4 badge @if($user->kyc == 'approved') badge-light-success @elseif($user->kyc == 'pending') badge-light-warning @else badge-light-danger @endif">
                                                @if($user->kyc == 'approved') Approved @elseif($user->kyc == 'pending') Pending @else Declined @endif
                                            </span>
                                        </div>

                                        <div class="mb-3 d-flex">
                                            <label class="form-label">ID</label>
                                            <span class="mx-2 px-4 badge badge-light-primary">
                                                @if($user->id_type)
                                                    {{ $user->id_type }} - {{ $user->id_number }} 
                                                @else
                                                    ---- - 000-000-000
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-5">
                                        <div class="revenuegrowth-details my-3"> 
                                            <div class="growth-details">
                                                <span class="f-light f-12  text-uppercase">Cash Balance</span>
                                                <h4 class="fw-bold mb-1">{{ $user->currency->sign }}{{ number_format($balance, 2) }}</h4>
                                                <div class="mb-4">
                                                    <!-- <span class="f-light text-success f-12 f-w-600">+40.15%</span> -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="revenuegrowth-details my-3"> 
                                            <div class="growth-details">
                                                <span class="f-light f-12  text-uppercase">Brokerage Balance</span>
                                                <h4 class="fw-bold mb-1">{{ $user->currency->sign }}{{ number_format($brokerage_balance, 2) }}</h4>
                                                <div class="mb-4">
                                                    <!-- <span class="f-light text-success f-12 f-w-600">+40.15%</span> -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="revenuegrowth-details my-3"> 
                                            <div class="growth-details">
                                                <span class="f-light f-12  text-uppercase">Auto Balance</span>
                                                <h4 class="fw-bold mb-1">{{ $user->currency->sign }}{{ number_format($auto_balance, 2) }}</h4>
                                                <div class="mb-4">
                                                    <!-- <span class="f-light text-success f-12 f-w-600">+40.15%</span> -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="revenuegrowth-details my-3"> 
                                            <div class="growth-details">
                                                <span class="f-light f-12  text-uppercase">Savings Balance</span>
                                                <h4 class="fw-bold mb-1">{{ $user->currency->sign }}{{ number_format($savings_balance, 2) }}</h4>
                                                <div class="mb-4">
                                                    <!-- <span class="f-light text-success f-12 f-w-600">+40.15%</span> -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        @if($user->front_id && $user->back_id)
                                            <div class="d-flex">
                                                <div class="m-1">
                                                    <span class="f-light">Front ID:</span>
                                                    <img class="rounded" style="width: 200px;" src="{{$user->front_id}}" alt="front id">
                                                </div>
                                                <div class="m-1">
                                                    <span class="f-light">Back ID:</span>
                                                    <img class="rounded" style="width: 200px;" src="{{$user->back_id}}" alt="back id">
                                                </div>
                                            </div>
                                            <div class="form-footer mt-4 d-flex">
                                                <form action="{{ route('admin.users.kyc', $user->id) }}" method="post">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="action" value="approved">
                                                    <button class="btn f-light badge badge-light-success" type="submit">Approve</button>
                                                </form>
                                                <form action="{{ route('admin.users.kyc', $user->id) }}" method="post">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="action" value="declined">
                                                    <button class="btn f-light badge badge-light-danger mx-2" type="submit">Decline</button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-footer mt-4 d-flex">
                                    <!-- <a href="{{ route('admin.altLogin') }}?email={{ $user->email }}" type="button" class="btn btn-primary btn-block" onclick="window.open('{{ route('admin.altLogin') }}?email={{ $user->email }}', 'newwindow', 'width=full'); return false;">
                                        User Login
                                    </a> -->
                                    @if($user->status == 'active')
                                        <form action="{{ route('admin.users.toggle', $user->id) }}" method="post">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="action" value="suspended">
                                            <button class="btn btn-danger btn-block" type="submit">Suspend User</button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.users.toggle', $user->id) }}" method="post">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="action" value="active">
                                            <button class="btn btn-success btn-block" type="submit">Activate User</button>
                                        </form>
                                    @endif
                                </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-7">
                    <form class="card" action="{{ route('admin.users.update', $user->id) }}" method="post">
                        @csrf
                        @method('PUT')
                        <div class="card-header d-flex justify-content-between">
                            <h4 class="card-title mb-0">Edit Profile</h4>
                            <div class="form-footer d-flex">
                                <a href="#" 
                                    class="badge px-3 f-light badge badge-light-success text-success" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#transactionModal" 
                                    data-action="Credit"
                                    data-url="{{ route('admin.user.credit', $user->id) }}">
                                    Credit
                                </a>
                                <a href="#" 
                                    class="badge px-3 f-light badge badge-light-danger text-danger" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#transactionModal" 
                                    data-action="Debit"
                                    data-url="{{ route('admin.user.debit', $user->id) }}">
                                    Debit
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">First Name</label>
                                    <input class="form-control" type="text" value="{{ $user->first_name }}" name="first_name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Last Name</label>
                                    <input class="form-control" type="text" value="{{ $user->last_name }}" name="last_name">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input class="form-control" type="email" value="{{ $user->email }}" name="email">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Address</label>
                                    <input class="form-control" type="text" value="{{ $user->address }}" name="address">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Country</label>
                                    <input class="form-control" type="text" value="{{ $user->country->name }}" name="country">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">State</label>
                                    <input class="form-control" type="text" value="{{ $user->state->name }}" name="state">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Zipcode</label>
                                    <input class="form-control" type="text" value="{{ $user->zipcode }}" name="zipcode">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">DOB</label>
                                    <input class="form-control" type="date" value="{{ $user->dob ? \Carbon\Carbon::parse($user->dob)->format('Y-m-d') : '' }}" name="dob">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Employed</label>
                                    <input class="form-control" type="text" value="{{ $user->employed }}" name="employed">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nationality</label>
                                    <input class="form-control" type="text" value="{{ $user->nationality }}" name="nationality">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Experience</label>
                                    <input class="form-control" type="text" value="{{ $user->experience }}" name="experience">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                <label class="form-label">Currency</label>
                                <select class="form-control btn-square" name="currency_id">
                                    @foreach($currencies as $currency)
                                        <option value="{{$currency->id}}" @if($currency->id == $user->currency->id) selected @endif>{{$currency->name}}  ({{$currency->symbol}})</option>
                                    @endforeach
                                </select>
                                </div>
                            </div>
                        </div>
                        </div>
                        <div class="card-footer text-end">
                            <button class="btn btn-primary" type="submit">Update Profile</button>
                        </div>
                    </form>
                </div>
                
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <ul class="nav nav-tabs border-tab border-0 mb-0 nav-primary" id="topline-tab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link nav-border pt-0 txt-primary nav-primary active" id="topline-top-user-tab" data-bs-toggle="tab" href="#topline-top-user" role="tab" aria-controls="topline-top-user" aria-selected="false" tabindex="-1">
                                        Transactions
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link nav-border txt-primary nav-primary" id="topline-top-description-tab" data-bs-toggle="tab" href="#topline-top-description" role="tab" aria-controls="topline-top-description" aria-selected="false" tabindex="-1">
                                        Trades
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link nav-border txt-primary nav-primary" id="topline-top-review-tab" data-bs-toggle="tab" href="#topline-top-review" role="tab" aria-controls="topline-top-description" aria-selected="false" tabindex="-1">
                                        Savings
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content" id="topline-tabContent">
                                <div class="tab-pane fade active show" id="topline-top-user" role="tabpanel" aria-labelledby="topline-top-user-tab">
                                    <div class="card-body px-0 pb-0">
                                        <div class="user-content"> 
                                            <div class="table-responsive custom-scrollbar">
                                            <table class="table mb-0">
                                                <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">Amount</th>
                                                    <th scope="col">Type</th>
                                                    <th scope="col">Comment</th>
                                                    <th scope="col">Status</th>
                                                    <th scope="col">Date</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($transactions as $transaction)
                                                        <tr>
                                                            <th scope="row">{{ $loop->iteration + ($transactions->currentPage() - 1) * $transactions->perPage() }}</th>
                                                            <td class="truncate-content">{{ $transaction->amount }}{{ $user->currency->symbol }}</td>
                                                            <td> 
                                                                <span class="truncate-content badge @if($transaction->type == 'credit') badge-light-success @elseif($transaction->type == 'transfer') badge-light-info @else badge-light-danger @endif">
                                                                    @if($transaction->type == 'credit') Credit @elseif($transaction->type == 'transfer') Transfer @else Debit @endif
                                                                </span> 
                                                            </td>
                                                            <td class="truncate-con"> 
                                                                {{ $transaction->comment }}
                                                            </td>
                                                            <td> 
                                                                <span class="badge @if($transaction->status == 'approved') badge-light-success  @elseif($transaction->status == 'pending') badge-light-warning @else badge-light-danger @endif">
                                                                    @if($transaction->status == 'approved') Approved @elseif($transaction->status == 'pending') Pending  @else Declined @endif
                                                                </span>
                                                            </td>
                                                            <td> <p class="truncate-content">{{ $transaction['created_at']->format('d M, Y \a\t h:i A') }}</p> </td>
                                                        </tr>

                                                    @endforeach
                                                </tbody>
                                            </table>
                                            @if($transactions->count() < 1)
                                                <p class="text-center my-2 py-4">No Transaction</p>
                                            @else
                                                <!-- Pagination Links -->
                                                <div class="jsgrid-pager">
                                                    Pages:
                                                    @if ($transactions->onFirstPage())
                                                        <span class="jsgrid-pager-nav-button jsgrid-pager-nav-inactive-button">
                                                            <a href="javascript:void(0);">First</a>
                                                        </span>
                                                        <span class="jsgrid-pager-nav-button jsgrid-pager-nav-inactive-button">
                                                            <a href="javascript:void(0);">Prev</a>
                                                        </span>
                                                    @else
                                                        <span class="jsgrid-pager-nav-button">
                                                            <a href="{{ $transactions->url(1) }}">First</a>
                                                        </span>
                                                        <span class="jsgrid-pager-nav-button">
                                                            <a href="{{ $transactions->previousPageUrl() }}">Prev</a>
                                                        </span>
                                                    @endif

                                                    <!-- Page Numbers -->
                                                    @foreach ($transactions->getUrlRange(1, $transactions->lastPage()) as $page => $url)
                                                        @if ($page == $transactions->currentPage())
                                                            <span class="jsgrid-pager-page jsgrid-pager-current-page">{{ $page }}</span>
                                                        @else
                                                            <span class="jsgrid-pager-page">
                                                                <a href="{{ $url }}">{{ $page }}</a>
                                                            </span>
                                                        @endif
                                                    @endforeach

                                                    @if ($transactions->hasMorePages())
                                                        <span class="jsgrid-pager-nav-button">
                                                            <a href="{{ $transactions->nextPageUrl() }}">Next</a>
                                                        </span>
                                                        <span class="jsgrid-pager-nav-button">
                                                            <a href="{{ $transactions->url($transactions->lastPage()) }}">Last</a>
                                                        </span>
                                                    @else
                                                        <span class="jsgrid-pager-nav-button jsgrid-pager-nav-inactive-button">
                                                            <a href="javascript:void(0);">Next</a>
                                                        </span>
                                                        <span class="jsgrid-pager-nav-button jsgrid-pager-nav-inactive-button">
                                                            <a href="javascript:void(0);">Last</a>
                                                        </span>
                                                    @endif

                                                    &nbsp;&nbsp; {{ $transactions->currentPage() }} of {{ $transactions->lastPage() }}
                                                </div>
                                            @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="topline-top-description" role="tabpanel" aria-labelledby="topline-top-description-tab">
                                    <div class="card-body px-0 pb-0">  
                                    <!-- <div class="user-header pb-2"> 
                                        <h6 class="fw-bold">User Details:</h6>
                                    </div> -->
                                    <div class="user-content"> 
                                        <div class="table-responsive custom-scrollbar">
                                        <table class="table mb-0">
                                            <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Asset</th>
                                                <th scope="col">Amount</th>
                                                <th scope="col">Quantity</th>
                                                <th scope="col">P/L</th>
                                                <th scope="col">Date</th>
                                                <th scope="col">Status</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($trades as $trade)
                                                    <tr>
                                                        <th scope="row">1</th>
                                                        <td class="truncate-content">{{ $trade->asset->name }}</td>
                                                        <td class="truncate-content">{{ number_format($trade->amount, 2) }} {{ $user->currency->symbol }}</td>
                                                        <td>{{ number_format($trade->quantity, 6) }}</td>
                                                        <td> <p class="text-success">+0.00 {{ $user->currency->sign }}</p> </td>
                                                        <td> <p class="text-success">{{ $trade->status }}</p> </td>
                                                        <td> <p class="truncate-content">{{ $trade['created_at']->format('d M, Y \a\t h:i A') }}</p> </td>
                                                        <td> 
                                                            <span class="badge @if($trade->status == 'open') badge-light-success  @elseif($trade->status == 'hold') badge-light-warning @else badge-light-danger @endif">
                                                                @if($trade->status == 'open') Open @elseif($trade->status == 'hold') Hold  @else Closed @endif
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        @if($trades->count() < 1)
                                            <p class="text-center my-2 py-4">No Trades</p>
                                        @endif
                                        </div>
                                    </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="topline-top-review" role="tabpanel" aria-labelledby="topline-top-review-tab">
                                    <div class="card-body px-0 pb-0">
                                    <div class="user-content"> 
                                        <div class="table-responsive custom-scrollbar">
                                        <table class="table mb-0">
                                            <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Account</th>
                                                <th scope="col">Amount</th>
                                                <th scope="col">Profit</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($savings_account as $account)
                                                    <tr>
                                                        <th scope="row">1</th>
                                                        <td>{{ $account->savingsAccount->name }}</td>
                                                        <td>{{ number_format($account->balance, 2) }} {{ $user->currency->symbol }}</td>
                                                        <td> <p class="text-success">+0.00 {{ $user->currency->sign }}</p> </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        @if($savings_account->count() < 1)
                                            <p class="text-center my-2 py-4">No Account</p>
                                        @endif
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-6">
                    <form class="card" action="{{ route('admin.users.bank', $user->id) }}" method="post">
                        @csrf
                        <input type="hidden" name="type" value="admin">
                        <div class="card-header d-flex justify-content-between">
                            <h4 class="card-title mb-0">Bank Deposit Details</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Bank Name</label>
                                        <input class="form-control" type="text" value="{{ $deposit->bank_name }}" name="bank_name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Account Name</label>
                                        <input class="form-control" type="text" value="{{ $deposit->account_name }}" name="account_name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Account Number</label>
                                        <input class="form-control" type="text" value="{{ $deposit->bank_account_number }}" name="bank_account_number">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Address</label>
                                        <input class="form-control" type="text" value="{{ $deposit->bank_address }}" name="bank_address">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Bank Reference</label>
                                        <input class="form-control" type="text" value="{{ $deposit->bank_reference }}" name="bank_reference">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Routing Number</label>
                                        <input class="form-control" type="text" value="{{ $deposit->bank_routing_number }}" name="bank_routing_number">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-start">
                            <button class="btn btn-success" type="submit">Update Information</button>
                        </div>
                    </form>
                    <form class="card" action="{{ route('admin.users.bank', $user->id) }}" method="post">
                        @csrf
                        <input type="hidden" name="type" value="admin">
                        <div class="card-header d-flex justify-content-between">
                            <h4 class="card-title mb-0">Crypto Deposit Details</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">BTC Wallet Address</label>
                                            <input class="form-control" type="text" value="{{ $deposit->btc_wallet }}" name="btc_wallet">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">ETH Wallet Address</label>
                                            <input class="form-control" type="text" value="{{ $deposit->eth_wallet }}" name="eth_wallet">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">USDT (TRC20) Wallet Address</label>
                                            <input class="form-control" type="text" value="{{ $deposit->trc_wallet }}" name="trc_wallet">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">USDT (ERC20) Wallet Address</label>
                                            <input class="form-control" type="text" value="{{ $deposit->erc_wallet }}" name="erc_wallet">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <div class="card-footer text-start">
                            <button class="btn btn-success" type="submit">Update Information</button>
                        </div>
                    </form>
                </div>
                <div class="col-xl-6">
                    <form class="card" action="{{ route('admin.users.bank', $user->id) }}" method="post">
                        @csrf
                        <input type="hidden" name="type" value="user">
                        <div class="card-header d-flex justify-content-between">
                            <h4 class="card-title mb-0">Bank Withdrawal Details</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Bank Name</label>
                                        <input class="form-control" type="text" value="{{ $withdrawal->bank_name }}" name="bank_name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Account Name</label>
                                        <input class="form-control" type="text" value="{{ $withdrawal->account_name }}" name="account_name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Account Number</label>
                                        <input class="form-control" type="text" value="{{ $withdrawal->bank_account_number }}" name="bank_account_number">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Address</label>
                                        <input class="form-control" type="text" value="{{ $withdrawal->bank_address }}" name="bank_address">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Bank Reference</label>
                                        <input class="form-control" type="text" value="{{ $withdrawal->bank_reference }}" name="bank_reference">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Routing Number</label>
                                        <input class="form-control" type="text" value="{{ $withdrawal->bank_routing_number }}" name="bank_routing_number">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-start">
                            <button class="btn btn-success" type="submit">Update Information</button>
                        </div>
                    </form>
                    <form class="card" action="{{ route('admin.users.bank', $user->id) }}" method="post">
                        @csrf
                        <input type="hidden" name="type" value="user">
                        <div class="card-header d-flex justify-content-between">
                            <h4 class="card-title mb-0">Crypto Withdrawal Details</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">BTC Wallet Address</label>
                                        <input class="form-control" type="text" value="{{ $withdrawal->btc_wallet }}" name="btc_wallet">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">ETH Wallet Address</label>
                                        <input class="form-control" type="text" value="{{ $withdrawal->eth_wallet }}" name="eth_wallet">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">USDT (TRC20) Wallet Address</label>
                                        <input class="form-control" type="text" value="{{ $withdrawal->trc_wallet }}" name="trc_wallet">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">USDT (ERC20) Wallet Address</label>
                                        <input class="form-control" type="text" value="{{ $withdrawal->erc_wallet }}" name="erc_wallet">
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

   <!-- ::::::  MODAL SECTION   :::::: -->
    <div>
        <!-- Reusable Modal -->
        <div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModal" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body"> 
                        <div class="modal-toggle-wrapper"> 
                            <h4 class="text-center pb-2" id="modalTitle"></h4> 
                            <form id="transactionForm" action="{{ route('admin.user.credit', $user->id) }}" method="POST">
                                @csrf
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Amount</label>
                                        <input class="form-control" type="text" placeholder="Enter amount..." name="amount">
                                    </div>
                                </div>
                                <div class="form-footer mt-4 d-flex">
                                    <button class="btn btn-primary btn-block" type="submit">Submit</button>
                                    <button class="btn btn-danger btn-block mx-2" type="button" data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Credit Modal -->
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const transactionModal = document.getElementById('transactionModal');
            const modalTitle = document.getElementById('modalTitle');
            const transactionForm = document.getElementById('transactionForm');
            
            transactionModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const action = button.getAttribute('data-action');
                const url = button.getAttribute('data-url');
                
                // Set modal title and form action dynamically
                modalTitle.textContent = `${action} {{ $user->first_name }}`;
                transactionForm.action = url;
            });
        });
    </script>
@endsection

@section('scripts')
    <script src="{{ asset('admin/assets/js/js-datatables/simple-datatables@latest.js') }}"></script>
    <script src="{{ asset('admin/assets/js/custom-list-product.js') }}"></script>
    <script src="{{ asset('admin/assets/js/owlcarousel/owl.carousel.js') }}"></script>
    <script src="{{ asset('admin/assets/js/ecommerce.js') }}"></script>
    <script src="{{ asset('admin/assets/js/tooltip-init.js') }}"></script>
@endsection