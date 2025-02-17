@extends('layouts.admin')

@section('title', ' Dashboard')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
        <div class="page-title">
            <div class="row">
            <div class="col-6">
                <h4>Savings</h4>
            </div>
            <div class="col-6">
                <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">                                       
                    <svg class="stroke-icon">
                        <use href="../assets/svg/icon-sprite.svg#stroke-home"></use>
                    </svg></a></li>
                <li class="breadcrumb-item">Dashboard</li>
                <li class="breadcrumb-item active">Savings</li>
                </ol>
            </div>
            </div>
        </div>
        </div>
        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4>Accounts</h4>
                                <span>Add and update the list of savings accounts</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <input class="form-control" id="inputEmail4" type="email" placeholder="Search...">
                                <a class="btn btn-primary fw-bold mx-2 d-flex align-items-center" href="#"><i class="fa fa-plus mx-2"></i> Add</a>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive custom-scrollbar px-4">
                        <table class="table">
                            <thead>
                            <tr class="border-bottom-primary">
                                <th scope="col">S/N</th>
                                <th scope="col">Name</th>
                                <th scope="col">Title</th>
                                <th scope="col">Slug</th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($accounts as $account)
                                <tr class="">
                                    <th>{{ $loop->iteration + ($accounts->currentPage() - 1) * $accounts->perPage() }}</th>
                                    <td>{{ $account->name }}</td>
                                    <td>{{ $account->title }}</td>
                                    <td>{{ $account->slug }}</td>
                                    <td> 
                                        <span class="badge @if($account->status == 'active') badge-light-success @else badge-light-danger @endif">
                                            @if($account->status == 'active') Active  @else Inactive @endif
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-primary rounded-pill dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Action</button>
                                            <ul class="dropdown-menu dropdown-menu-dark dropdown-block">
                                                <li>
                                                    <form action="{{ route('admin.transactions.deposit', $account->id) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        <input type="hidden" name="action" value="approved">
                                                        <button type="submit" class="dropdown-item fw-bold">Approve</button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="{{ route('admin.transactions.deposit', $account->id) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        <input type="hidden" name="action" value="decline">
                                                        <button type="submit" class="dropdown-item text-danger fw-bold">Decline</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <!-- Pagination Links -->
                        <div class="jsgrid-pager my-3 mx-2">
                            Pages:
                            @if ($accounts->onFirstPage())
                                <span class="jsgrid-pager-nav-button jsgrid-pager-nav-inactive-button">
                                    <a href="javascript:void(0);">First</a>
                                </span>
                                <span class="jsgrid-pager-nav-button jsgrid-pager-nav-inactive-button">
                                    <a href="javascript:void(0);">Prev</a>
                                </span>
                            @else
                                <span class="jsgrid-pager-nav-button">
                                    <a href="{{ $accounts->url(1) }}">First</a>
                                </span>
                                <span class="jsgrid-pager-nav-button">
                                    <a href="{{ $accounts->previousPageUrl() }}">Prev</a>
                                </span>
                            @endif

                            <!-- Page Numbers -->
                            @foreach ($accounts->getUrlRange(1, $accounts->lastPage()) as $page => $url)
                                @if ($page == $accounts->currentPage())
                                    <span class="jsgrid-pager-page jsgrid-pager-current-page">{{ $page }}</span>
                                @else
                                    <span class="jsgrid-pager-page">
                                        <a href="{{ $url }}">{{ $page }}</a>
                                    </span>
                                @endif
                            @endforeach

                            @if ($accounts->hasMorePages())
                                <span class="jsgrid-pager-nav-button">
                                    <a href="{{ $accounts->nextPageUrl() }}" class="fw-bold">Next</a>
                                </span>
                                <span class="jsgrid-pager-nav-button">
                                    <a href="{{ $accounts->url($accounts->lastPage()) }}" class="fw-bold">Last</a>
                                </span>
                            @else
                                <span class="jsgrid-pager-nav-button jsgrid-pager-nav-inactive-button">
                                    <a href="javascript:void(0);" class="fw-bold">Next</a>
                                </span>
                                <span class="jsgrid-pager-nav-button jsgrid-pager-nav-inactive-button">
                                    <a href="javascript:void(0);" class="fw-bold">Last</a>
                                </span>
                            @endif

                            &nbsp;&nbsp; {{ $accounts->currentPage() }} of {{ $accounts->lastPage() }}
                        </div>
                    </div>
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
                            <form id="transactionForm" action="" method="post">
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
                modalTitle.textContent = `${action}`;
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