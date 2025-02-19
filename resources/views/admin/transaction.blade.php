@extends('layouts.admin')

@section('title', ' Dashboard')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
        <div class="page-title">
            <div class="row">
            <div class="col-6">
                <h4>
                    Transactions list</h4>
            </div>
            <div class="col-6">
                <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">                                       
                    <svg class="stroke-icon">
                        <use href="../assets/svg/icon-sprite.svg#stroke-home"></use>
                    </svg></a></li>
                <li class="breadcrumb-item">Dashboard </li>
                <li class="breadcrumb-item active">Transactions list</li>
                </ol>
            </div>
            </div>
        </div>
        </div>
        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row"> 
                <div class="col-sm-12"> 
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4>{{ $title }}</h4>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input class="form-control" id="inputEmail4" type="email" placeholder="Search...">
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive custom-scrollbar px-4">
                            <table class="table">
                                <thead>
                                <tr class="border-bottom-primary">
                                    <th> <span class="f-light f-w-600">S/N</span></th>
                                    <th> <span class="f-light f-w-600">Name</span></th>
                                    <th> <span class="f-light f-w-600">Amount </span></th>
                                    <th> <span class="f-light f-w-600">Type</span></th>
                                    <th> <span class="f-light f-w-600">Info</span></th>
                                    <th> <span class="f-light f-w-600">Status</span></th>
                                    <th> <span class="f-light f-w-600">Date</span></th>
                                    <th> <span class="f-light f-w-600">Action</span></th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $index => $transaction)
                                        <tr class="product-removes">
                                            <td>{{ $index +  1 }}</td>
                                            <td> 
                                                <div class="product-names fw-bold">
                                                    <a href="#" class="text-success">{{ $transaction->user->first_name }} {{ $transaction->user->last_name }}</a>
                                                </div>
                                            </td>
                                            <td> 
                                                <p class="f-light fw-bold">{{ $transaction->amount }} USD</p>
                                            </td>
                                            <td> 
                                                <span class="badge rounded-pill @if($transaction->type == 'credit') badge-light-success @else badge-danger @endif">
                                                    @if($transaction->type == 'credit') Credit @else Debit @endif
                                                </span>
                                            </td>
                                            <td> 
                                                <p class="f-light">{{ $transaction->comment }}</p>
                                            </td>
                                            <td> 
                                                <span class="badge @if($transaction->status == 'approved') badge-light-success  @elseif($transaction->status == 'pending') badge-light-warning @else badge-light-danger @endif">
                                                    @if($transaction->status == 'approved') Approved @elseif($transaction->status == 'pending') Pending  @else Declined @endif
                                                </span>
                                            </td>
                                            <td> 
                                                <p class="f-light">{{ $transaction['created_at']->format('d M, Y \a\t h:i A') }}</p>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <button class="btn btn-primary rounded-pill dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Action</button>
                                                    @if($transaction->status == 'pending' && $transaction->type == 'credit')
                                                        <ul class="dropdown-menu dropdown-menu-dark dropdown-block">
                                                            <li>
                                                                <form action="{{ route('admin.transactions.deposit', $transaction->id) }}" method="POST" style="display: inline;">
                                                                    @csrf
                                                                    <input type="hidden" name="action" value="approved">
                                                                    <button type="submit" class="dropdown-item fw-bold">Approve</button>
                                                                </form>
                                                            </li>
                                                            <li>
                                                                <form action="{{ route('admin.transactions.deposit', $transaction->id) }}" method="POST" style="display: inline;">
                                                                    @csrf
                                                                    <input type="hidden" name="action" value="decline">
                                                                    <button type="submit" class="dropdown-item text-danger fw-bold">Decline</button>
                                                                </form>
                                                            </li>
                                                        </ul>
                                                    @elseif($transaction->status == 'pending' && $transaction->type == 'debit')
                                                        <ul class="dropdown-menu dropdown-menu-dark dropdown-block">
                                                            <li>
                                                                <form action="{{ route('admin.transactions.withdraw', $transaction->id) }}" method="POST" style="display: inline;">
                                                                    @csrf
                                                                    <input type="hidden" name="action" value="approved">
                                                                    <button type="submit" class="dropdown-item fw-bold">Approve</button>
                                                                </form>
                                                            </li>
                                                            <li>
                                                                <form action="{{ route('admin.transactions.withdraw', $transaction->id) }}" method="POST" style="display: inline;">
                                                                    @csrf
                                                                    <input type="hidden" name="action" value="decline">
                                                                    <button type="submit" class="dropdown-item text-danger fw-bold">Decline</button>
                                                                </form>
                                                            </li>
                                                        </ul>
                                                    @endif
                                                </div>
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if($transactions->count() < 1)
                                <div class="">
                                    <p class="text-center my-4 py-4">No data</p>
                                </div>
                            @endif
                            <!-- Pagination Links -->
                            <div class="jsgrid-pager my-3 mx-2">
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
                                        <a href="{{ $transactions->nextPageUrl() }}" class="fw-bold">Next</a>
                                    </span>
                                    <span class="jsgrid-pager-nav-button">
                                        <a href="{{ $transactions->url($transactions->lastPage()) }}" class="fw-bold">Last</a>
                                    </span>
                                @else
                                    <span class="jsgrid-pager-nav-button jsgrid-pager-nav-inactive-button">
                                        <a href="javascript:void(0);" class="fw-bold">Next</a>
                                    </span>
                                    <span class="jsgrid-pager-nav-button jsgrid-pager-nav-inactive-button">
                                        <a href="javascript:void(0);" class="fw-bold">Last</a>
                                    </span>
                                @endif

                                &nbsp;&nbsp; {{ $transactions->currentPage() }} of {{ $transactions->lastPage() }}
                            </div>
                        </div>
                    </div>
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
    