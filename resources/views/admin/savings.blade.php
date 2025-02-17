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
                <li class="breadcrumb-item active">Trades list</li>
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
                                                <a href="#" class="text-success">{{ $saving->user->first_name }} {{ $saving->user->last_name }}</a>
                                            </div>
                                        </td>
                                        <td> 
                                            <p class="f-light fw-bold">{{ $saving->balance }} USD</p>
                                        </td>
                                        <td> 
                                            {{ $saving->accounts }}
                                        </td>
                                        <td> 
                                            <p class="f-light text-success">+-- USD</p>
                                        </td>
                                        <td> 
                                            <span class="badge badge-light-success">
                                                Active
                                            </span>
                                        </td>
                                        <td> 
                                            <a href="#" class="btn btn-primary">Action</a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
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
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('admin/assets/js/js-datatables/simple-datatables@latest.js') }}"></script>
    <script src="{{ asset('admin/assets/js/custom-list-product.js') }}"></script>
    <script src="{{ asset('admin/assets/js/owlcarousel/owl.carousel.js') }}"></script>
    <script src="{{ asset('admin/assets/js/ecommerce.js') }}"></script>
    <script src="{{ asset('admin/assets/js/tooltip-init.js') }}"></script>
@endsection
    