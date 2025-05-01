@extends('layouts.admin')

@section('title', ' Dashboard')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="page-title">
                <div class="row">
                <div class="col-6">
                    <h4>Savings Transactions</h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">                                       
                        <svg class="stroke-icon">
                            <use href="../assets/svg/icon-sprite.svg#stroke-home"></use>
                        </svg></a></li>
                    <li class="breadcrumb-item">Users</li>
                    <li class="breadcrumb-item">Savings</li>
                    <li class="breadcrumb-item active"> Savings Transactions</li>
                    </ol>
                </div>
                </div>
            </div>
        </div>
        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="edit-profile">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="user-content"> 
                                <div class="table-responsive custom-scrollbar">
                                {{-- <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex">
                                        <p class="mr-2">Balance: </p><h4 class="fw-bold mx-3">###</h4>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <a class="btn btn-success w-100 mx-2" 
                                            href="#"
                                            data-bs-toggle="modal"
                                            data-bs-target="#addContributionModal" 
                                            data-action="Credit"
                                            data-url=""
                                        >
                                            <i class="fa fa-plus"></i>Add
                                        </a>
                                    </div>
                                </div> --}}
                                <table class="table mb-0">
                                    <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">User</th>
                                        <th scope="col">Amount</th>
                                        <th scope="col">Account</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">Method</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($transactions as $transaction)
                                            <tr>
                                                <th scope="row">{{ $loop->iteration + ($transactions->currentPage() - 1) * $transactions->perPage() }}</th>
                                                <td> 
                                                    <div class="product-names fw-bold">
                                                        <a href="{{ route('admin.users.show', $transaction->user->id) }}" class="text-success truncate-content">{{ $transaction->user->first_name }} {{ $transaction->user->last_name }}</a>
                                                    </div>
                                                </td>
                                                <td class="truncate-content">
                                                    <div class="product-names fw-bold">
                                                        <a href="{{ route('admin.users.show', $transaction->user->id) }}" class="text-dark truncate-content">{{ $transaction->amount }} {{ $transaction->user->currency->symbol }}</a>
                                                    </div>
                                                </td>
                                                <td> 
                                                    <div class="product-names fw-bold">
                                                        <a href="{{ route('admin.accounts.transactions', ['user' => $transaction->user->id, 'savings' => $transaction->savings_id]) }}" class="text-success truncate-content">{{ $transaction->savings->savingsAccount->name }}</a>
                                                    </div>
                                                </td>
                                                <td> 
                                                    <span class="truncate-content badge @if($transaction->type == 'credit') badge-light-success @elseif($transaction->type == 'transfer') badge-light-info @else badge-light-danger @endif">
                                                        @if($transaction->type == 'credit') Credit @elseif($transaction->type == 'transfer') Transfer @else Debit @endif
                                                    </span> 
                                                </td>
                                                <td> 
                                                    <div class="product-names fw-bold">
                                                        <a href="#" class=" @if($transaction->method == 'profit') text-success @else text-muted @endif text-capitalize "> @if($transaction->type == 'credit') {{ $transaction->method }} @else Cashout @endif</a>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge text-capitalize @if($transaction->status == 'approved') badge-light-success @elseif($transaction->status == 'pending') badge-light-warning @else badge-light-danger @endif">
                                                        {{ $transaction->status }}
                                                    </span>
                                                </td>
                                                <td> <p class="truncate-content">{{ $transaction['created_at']->format('d M, Y \a\t h:i A') }}</p> </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <button class="btn btn-dark rounded-pill dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Action</button>
                                                            <ul class="dropdown-menu dropdown-menu-dark dropdown-block">
                                                                @if($transaction->status == 'pending')
                                                                    <li>
                                                                        <form action="{{ route('admin.account.savings.approve', $transaction->id) }}" method="POST" style="display: inline;">
                                                                            @csrf
                                                                            @method('PUT')
                                                                            <button type="submit" 
                                                                                    class="dropdown-item fw-bold text-dark">
                                                                                Approve
                                                                            </button>
                                                                        </form>
                                                                    </li>
                                                                    <li>
                                                                        <form action="{{ route('admin.account.savings.decline', $transaction->id) }}" method="POST" style="display: inline;">
                                                                            @csrf
                                                                            @method('PUT')
                                                                            <button type="submit" 
                                                                                    class="dropdown-item fw-bold text-dark">
                                                                                Decline
                                                                            </button>
                                                                        </form>
                                                                    </li>
                                                                @endif
                                                                <li>
                                                                    <form action="{{ route('admin.savings.destroy', $transaction->id) }}" method="POST" style="display: inline;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="dropdown-item text-white bg-danger fw-bold">Delete</button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                    </div>
                                                </td>
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
                </div>
            </div>
        </div>
        <!-- Container-fluid Ends-->
    </div>

   <!-- ::::::  MODAL SECTION   :::::: -->
    {{-- <div>
        <!-- Reusable Modal -->
        <div class="modal fade" id="addContributionModal" tabindex="-1" aria-labelledby="addContributionModal" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body"> 
                        <div class="modal-toggle-wrapper"> 
                            <h4 class="text-center pb-2" id="modalTitle"></h4> 
                            <form id="transactionForm" action="{{ route('admin.accounts.contribute', ['user' => $user->id, 'savings' => $savings->id]) }}" method="POST">
                                @csrf
                                <h4 class="text-center my-1">Create Transaction</h4>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Amount</label>
                                        <input class="form-control" type="text" placeholder="Enter amount..." name="amount">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Type</label>
                                        <select class="form-select" id="" required="" name="type">
                                            <option selected="" disabled="" value="">--- Select Type ---</option>
                                            <option value="credit">Contribute</option>
                                            <option value="debit">Cashout</option>
                                            <option value="profit">Profit</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Date</label>
                                        <input class="form-control" type="datetime-local" name="created_at" id="date" required>
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
    </div> --}}

@endsection

@section('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let now = new Date();
            let formattedDateTime = now.toISOString().slice(0, 16); // Format: YYYY-MM-DDTHH:MM
            document.getElementById("date").value = formattedDateTime;
        });
    </script>
    <script src="{{ asset('admin/assets/js/js-datatables/simple-datatables@latest.js') }}"></script>
    <script src="{{ asset('admin/assets/js/custom-list-product.js') }}"></script>
    <script src="{{ asset('admin/assets/js/owlcarousel/owl.carousel.js') }}"></script>
    <script src="{{ asset('admin/assets/js/ecommerce.js') }}"></script>
    <script src="{{ asset('admin/assets/js/tooltip-init.js') }}"></script>
@endsection