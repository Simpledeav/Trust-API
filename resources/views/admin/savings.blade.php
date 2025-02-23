@extends('layouts.admin')

@section('title', ' Dashboard')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
        <div class="page-title">
            <div class="row">
            <div class="col-6">
                <h4>
                    Savings list</h4>
            </div>
            <div class="col-6">
                <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">                                       
                    <svg class="stroke-icon">
                        <use href="../assets/svg/icon-sprite.svg#stroke-home"></use>
                    </svg></a></li>
                <li class="breadcrumb-item">Dashboard </li>
                <li class="breadcrumb-item active">Savings list</li>
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
                                    <h4>Savings</h4>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center">
                                        <a class="btn btn-success w-100 mx-2" 
                                            href="#"
                                            data-bs-toggle="modal"
                                            data-bs-target="#addSavingsModal" 
                                            data-action="Credit"
                                            data-url=""
                                        >
                                            <i class="fa fa-plus"></i>Add
                                        </a>
                                    </div>
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
                                    <th> <span class="f-light f-w-600">Account(s)</span></th>
                                    <th> <span class="f-light f-w-600">P/L</span></th>
                                    <th> <span class="f-light f-w-600">Status</span></th>
                                    <th> <span class="f-light f-w-600">Action</span></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($savings as $index => $saving)
                                    <tr class="product-removes">
                                        <td>{{ $index +  1 }}</td>
                                        <td> 
                                            <div class="product-names fw-bold">
                                                <a href="{{ route('admin.users.show', $saving->user->id) }}" class="text-success">{{ $saving->user->first_name }} {{ $saving->user->last_name }}</a>
                                            </div>
                                        </td>
                                        <td> 
                                            <p class="f-light fw-bold">{{ $saving->balance }} {{ $saving->user->currency->symbol }}</p>
                                        </td>
                                        <td> 
                                            <div class="product-names fw-bold">
                                                <a href="{{ route('admin.accounts.transactions', ['user' => $saving->user->id, 'savings' => $saving->id]) }}" class="text-success">{{ $saving->savingsAccount->name }}</a>
                                            </div>
                                        </td>
                                        <td> 
                                            <p class="f-light text-success">+{{ $saving->savingsTransaction->where('method', 'profit')->sum('amount') }} {{ $saving->user->currency->symbol }}</p>
                                        </td>
                                        <td> 
                                            <span class="badge badge-light-success">
                                                Active
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button class="btn btn-dark rounded-pill dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Action</button>
                                                    <ul class="dropdown-menu dropdown-menu-dark dropdown-block">
                                                        <li>
                                                            <a href="{{ route('admin.accounts.transactions', ['user' => $saving->user->id, 'savings' => $saving->id]) }}" class="dropdown-item fw-bold">
                                                                View
                                                            </a>
                                                        </li>
                                                    </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            @if($savings->count() < 1)
                                <div class="">
                                    <p class="text-center my-4 py-4">No data</p>
                                </div>
                            @endif
                            <!-- Pagination Links -->
                            <div class="jsgrid-pager my-3 mx-2">
                                Pages:
                                @if ($savings->onFirstPage())
                                    <span class="jsgrid-pager-nav-button jsgrid-pager-nav-inactive-button">
                                        <a href="javascript:void(0);">First</a>
                                    </span>
                                    <span class="jsgrid-pager-nav-button jsgrid-pager-nav-inactive-button">
                                        <a href="javascript:void(0);">Prev</a>
                                    </span>
                                @else
                                    <span class="jsgrid-pager-nav-button">
                                        <a href="{{ $savings->url(1) }}">First</a>
                                    </span>
                                    <span class="jsgrid-pager-nav-button">
                                        <a href="{{ $savings->previousPageUrl() }}">Prev</a>
                                    </span>
                                @endif

                                <!-- Page Numbers -->
                                @foreach ($savings->getUrlRange(1, $savings->lastPage()) as $page => $url)
                                    @if ($page == $savings->currentPage())
                                        <span class="jsgrid-pager-page jsgrid-pager-current-page">{{ $page }}</span>
                                    @else
                                        <span class="jsgrid-pager-page">
                                            <a href="{{ $url }}">{{ $page }}</a>
                                        </span>
                                    @endif
                                @endforeach

                                @if ($savings->hasMorePages())
                                    <span class="jsgrid-pager-nav-button">
                                        <a href="{{ $savings->nextPageUrl() }}" class="fw-bold">Next</a>
                                    </span>
                                    <span class="jsgrid-pager-nav-button">
                                        <a href="{{ $savings->url($savings->lastPage()) }}" class="fw-bold">Last</a>
                                    </span>
                                @else
                                    <span class="jsgrid-pager-nav-button jsgrid-pager-nav-inactive-button">
                                        <a href="javascript:void(0);" class="fw-bold">Next</a>
                                    </span>
                                    <span class="jsgrid-pager-nav-button jsgrid-pager-nav-inactive-button">
                                        <a href="javascript:void(0);" class="fw-bold">Last</a>
                                    </span>
                                @endif

                                &nbsp;&nbsp; {{ $savings->currentPage() }} of {{ $savings->lastPage() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Container-fluid Ends-->

        <div>
            <!-- Reusable Modal -->
            <div class="modal fade" id="addSavingsModal" tabindex="-1" aria-labelledby="addSavingsModal" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-body"> 
                            <div class="modal-toggle-wrapper"> 
                                <h4 class="text-center pb-2" id="modalTitle"></h4> 
                                <form id="transactionForm" action="{{ route('admin.account.store') }}" method="POST">
                                    @csrf
                                    <h4 class="text-center my-1">Create Savings Account</h4>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">User</label>
                                            <select class="form-select" id="" required="" name="user_id">
                                                <option selected="" disabled="" value="">---- Select User ---</option>
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Account</label>
                                            <select class="form-select" id="" required="" name="account_id">
                                                <option selected="" disabled="" value="">---- Select Account ---</option>
                                                @foreach($accounts as $account)
                                                    <option value="{{ $account->id }}">{{ $account->name }}</option>
                                                @endforeach
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
        </div>
    </div>
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
    