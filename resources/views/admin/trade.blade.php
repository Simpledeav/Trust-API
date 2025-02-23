@extends('layouts.admin')

@section('title', ' Dashboard')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
        <div class="page-title">
            <div class="row">
            <div class="col-6">
                <h4>
                    Trades list</h4>
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
                                    <h4>Trades</h4>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input class="form-control" id="inputEmail4" type="email" placeholder="Search...">
                                    <a class="btn btn-success w-100 mx-2" 
                                        href="#"
                                        data-bs-toggle="modal"
                                        data-bs-target="#addTrade" 
                                        data-action="Credit"
                                        data-url=""
                                    >
                                        <i class="fa fa-plus"></i>Add Trade
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive custom-scrollbar px-4">
                            <table class="table">
                                <thead>
                                <tr class="border-bottom-primary">
                                    <th> <span class="f-light f-w-600">S/N</span></th>
                                    <th> <span class="f-light f-w-600">Name</span></th>
                                    <th> <span class="f-light f-w-600">Asset</span></th>
                                    <th> <span class="f-light f-w-600">Amount </span></th>
                                    <th> <span class="f-light f-w-600">Type</span></th>
                                    <th> <span class="f-light f-w-600">P/L</span></th>
                                    <th> <span class="f-light f-w-600">Status</span></th>
                                    <th> <span class="f-light f-w-600">Date</span></th>
                                    <th> <span class="f-light f-w-600">Action</span></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($trades as $index => $trade)
                                    @php 
                                        $assetPrice = $trade->asset->price;
                                        $quantity = $trade->quantity;
                                        $extra = $trade->extra;

                                        $singleProfit = ($assetPrice * $quantity) - $trade->amount;
                                        $profit = $singleProfit + $trade->extra;
                                    @endphp
                                    <tr class="">
                                        <td>{{ $index +  1 }}</td>
                                        <td> 
                                            <div class="product-names fw-bold">
                                                <a href="{{ route('admin.users.show', $trade->user->id) }}" class="text-success">{{ $trade->user->first_name }} {{ $trade->user->last_name }}</a>
                                            </div>
                                        </td>
                                        <td> 
                                            <div class="product-names fw-bold">
                                                <a href="#" class="text-success">{{ $trade->asset->name }}</a>
                                            </div>
                                        </td>
                                        <td> 
                                            <p class="f-light fw-bold">{{ $trade->amount }} USD</p>
                                        </td>
                                        <td> 
                                            <span class="badge rounded-pill @if($trade->type == 'buy') badge-light-success @else badge-light-danger @endif">
                                                @if($trade->type == 'buy') BUY @else SELL @endif
                                            </span>
                                        </td>
                                        <td> 
                                            <p class="f-light @if($profit >= 0) text-success @else text-danger @endif">{{ number_format($profit, 2) }} USD</p>
                                        </td>
                                        <td> 
                                            <span class="badge @if($trade->status == 'open') badge-light-success  @elseif($trade->status == 'hold') badge-light-warning @else badge-light-danger @endif">
                                                @if($trade->status == 'open') Open @elseif($trade->status == 'hold') Hold  @else Closed @endif
                                            </span>
                                        </td>
                                        <td> 
                                            <p class="f-light">{{ $trade['created_at']->format('d M, Y \a\t h:i A') }}</p>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button class="btn btn-dark rounded-pill dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Action</button>
                                                    <ul class="dropdown-menu dropdown-menu-dark dropdown-block">
                                                        <li>
                                                            <a href="#" class="dropdown-item fw-bold" data-bs-toggle="modal" data-bs-target="#editTrade{{ $trade->id }}">
                                                                Edit
                                                            </a>
                                                        </li>
                                                        @if($trade->status !== 'open')
                                                            <li>
                                                                <form action="{{ route('admin.trade.toggle', $trade->id) }}" method="POST" style="display: inline;">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input type="hidden" name="action" value="open">
                                                                    <button type="submit" class="dropdown-item fw-bold text-success">Open</button>
                                                                </form>
                                                            </li>
                                                        @endif
                                                        @if($trade->status !== 'hold')
                                                        <li>
                                                            <form action="{{ route('admin.trade.toggle', $trade->id) }}" method="POST" style="display: inline;">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="action" value="hold">
                                                                <button type="submit" class="dropdown-item text-warning fw-bold">Hold</button>
                                                            </form>
                                                        </li>
                                                        @endif
                                                        @if($trade->status !== 'close')
                                                        <li>
                                                            <form action="{{ route('admin.trade.toggle', $trade->id) }}" method="POST" style="display: inline;">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="action" value="close">
                                                                <button type="submit" class="dropdown-item text-danger fw-bold">Close</button>
                                                            </form>
                                                        </li>
                                                        @endif
                                                        <li>
                                                            <form action="{{ route('admin.trade.destroy', $trade->id) }}" method="POST" style="display: inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-white bg-danger fw-bold">Delete</button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Edit Trade Modal -->
                                    <div class="modal fade" id="editTrade{{$trade->id}}" tabindex="-1" aria-labelledby="editTrade{{$trade->id}}" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-body">
                                                    <div class="modal-toggle-wrapper">
                                                        <h4 class="text-center pb-2" id="modalTitle">Edit Trade</h4>
                                                        <form id="editTradeForm" action="{{ route('admin.trade.update', $trade->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="col-md-12">
                                                                <div class="mb-3">
                                                                    <label class="form-label">User</label>
                                                                    <select class="form-select" id="" required="" name="user_id">
                                                                        @foreach($users as $user)
                                                                            @if($user->id === $trade->user->id)
                                                                                <option selected value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                                                                            @endif
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-12">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Asset</label>
                                                                    <select class="form-select" id="" required="" name="asset_id">
                                                                        @foreach($assets as $asset)
                                                                            @if($asset->id === $trade->asset->id)
                                                                                <option selected value="{{ $asset->id }}">{{ $asset->name }} ({{ $asset->symbol }})</option>
                                                                            @else
                                                                                <option value="{{ $asset->id }}">{{ $asset->name }} ({{ $asset->symbol }})</option>
                                                                            @endif
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-12">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Amount</label>
                                                                    <input class="form-control" type="number" placeholder="Enter amount..." name="amount" id="editAmount" value="{{ $trade->amount }}" required>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-12">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Type</label>
                                                                    <select class="form-select" id="editType" required="" name="type">
                                                                        <option selected="" disabled="" value="">Trade type...</option>
                                                                        <option value="buy"  @if($trade->type == 'buy') selected @endif>BUY</option>
                                                                        <option value="sell"  @if($trade->type == 'sell') selected @endif>SELL</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-12">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Entry</label>
                                                                    <input class="form-control" type="number" placeholder="(Optional)" name="entry" value="{{ $trade->entry }}" id="editEntry">
                                                                </div>
                                                            </div>

                                                            <div class="col-md-12">
                                                                <div class="mb-3">
                                                                    <label class="form-label">S/L</label>
                                                                    <input class="form-control" type="number" placeholder="(Optional)" name="sl" value="{{ $trade->sl }}" id="editSl">
                                                                </div>
                                                            </div>

                                                            <div class="col-md-12">
                                                                <div class="mb-3">
                                                                    <label class="form-label">T/P</label>
                                                                    <input class="form-control" type="number" placeholder="(Optional)" name="tp" value="{{ $trade->tp }}" id="editTp">
                                                                </div>
                                                            </div>

                                                            <div class="col-md-12">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Leverage</label>
                                                                    <input class="form-control" type="text" placeholder="(Optional)" name="leverage" value="{{ $trade->leverage }}" id="leverage">
                                                                </div>
                                                            </div>

                                                            <div class="col-md-12">
                                                                <div class="mb-3">
                                                                    <label class="form-label">P&L (USD)</label>
                                                                    <input class="form-control" type="number" placeholder="(Optional)" name="extra" value="{{ $trade->extra }}" id="editExtra">
                                                                </div>
                                                            </div>

                                                            <div class="col-md-12">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Date</label>
                                                                    <input class="form-control" type="datetime-local" name="created_at" id="dateEdit" required value="{{ $trade->created_at }}">
                                                                </div>
                                                            </div>

                                                            <div class="form-footer mt-4 d-flex">
                                                                <button class="btn btn-primary btn-block" type="submit">Update</button>
                                                                <button class="btn btn-danger btn-block mx-2" type="button" data-bs-dismiss="modal">Cancel</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                </tbody>
                            </table>
                            @if($trades->count() < 1)
                                <div class="">
                                    <p class="text-center my-4 py-4">No data</p>
                                </div>
                            @endif
                            <!-- Pagination Links -->
                            <div class="jsgrid-pager my-3 mx-2">
                                Pages:
                                @if ($trades->onFirstPage())
                                    <span class="jsgrid-pager-nav-button jsgrid-pager-nav-inactive-button">
                                        <a href="javascript:void(0);">First</a>
                                    </span>
                                    <span class="jsgrid-pager-nav-button jsgrid-pager-nav-inactive-button">
                                        <a href="javascript:void(0);">Prev</a>
                                    </span>
                                @else
                                    <span class="jsgrid-pager-nav-button">
                                        <a href="{{ $trades->url(1) }}">First</a>
                                    </span>
                                    <span class="jsgrid-pager-nav-button">
                                        <a href="{{ $trades->previousPageUrl() }}">Prev</a>
                                    </span>
                                @endif

                                <!-- Page Numbers -->
                                @foreach ($trades->getUrlRange(1, $trades->lastPage()) as $page => $url)
                                    @if ($page == $trades->currentPage())
                                        <span class="jsgrid-pager-page jsgrid-pager-current-page">{{ $page }}</span>
                                    @else
                                        <span class="jsgrid-pager-page">
                                            <a href="{{ $url }}">{{ $page }}</a>
                                        </span>
                                    @endif
                                @endforeach

                                @if ($trades->hasMorePages())
                                    <span class="jsgrid-pager-nav-button">
                                        <a href="{{ $trades->nextPageUrl() }}" class="fw-bold">Next</a>
                                    </span>
                                    <span class="jsgrid-pager-nav-button">
                                        <a href="{{ $trades->url($trades->lastPage()) }}" class="fw-bold">Last</a>
                                    </span>
                                @else
                                    <span class="jsgrid-pager-nav-button jsgrid-pager-nav-inactive-button">
                                        <a href="javascript:void(0);" class="fw-bold">Next</a>
                                    </span>
                                    <span class="jsgrid-pager-nav-button jsgrid-pager-nav-inactive-button">
                                        <a href="javascript:void(0);" class="fw-bold">Last</a>
                                    </span>
                                @endif

                                &nbsp;&nbsp; {{ $trades->currentPage() }} of {{ $trades->lastPage() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Container-fluid Ends-->
    </div>

    <!-- Reusable Modal -->
    <div class="modal fade" id="addTrade" tabindex="-1" aria-labelledby="addTrade" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body"> 
                    <div class="modal-toggle-wrapper"> 
                        <h4 class="text-center pb-2" id="modalTitle"></h4> 
                        <form id="transactionForm" action="{{ route('admin.trade.create') }}" method="POST">
                            @csrf
                            <h4 class="text-center my-1">Open a Trade</h4>
                            {{-- <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Assets</label>
                                    <div class="select-box">
                                        <div class="options-container">
                                            @foreach($assets as $asset)
                                                <div class="selection-option">
                                                    <input class="radio" id="asset_{{ $asset->name }}" type="radio" name="asset" value="{{ $asset->id }}"  {{ $loop->first ? 'checked' : '' }}>
                                                    <label class="mb-0" for="asset_{{ $asset->name }}"> {{ $asset->name }} </label>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="selected-box">Select Asset</div>
                                        <div class="search-box">
                                            <input type="text" placeholder="Start Typing...">
                                        </div>
                                    </div>
                                </div>
                            </div> --}}

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
                                    <label class="form-label">Asset</label>
                                    <select class="form-select" id="" required="" name="asset_id">
                                        <option selected="" disabled="" value="">---- Select Asset ---</option>
                                        @foreach($assets as $asset)
                                            <option value="{{ $asset->id }}">{{ $asset->name }} ({{ $asset->symbol }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Amount</label>
                                    <input class="form-control" type="number" placeholder="Enter amount..." name="amount" required>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Type</label>
                                    <select class="form-select" id="" required="" name="type">
                                        <option selected="" disabled="" value="">Trade type...</option>
                                        <option value="buy">BUY</option>
                                        <option value="sell">SELL</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Entry</label>
                                    <input class="form-control" type="number" placeholder="(Optional)" name="entry">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">S/L</label>
                                    <input class="form-control" type="number" placeholder="(Optional)" name="sl">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">T/P</label>
                                    <input class="form-control" type="number" placeholder="(Optional)" name="tp">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Leverage</label>
                                    <input class="form-control" type="text" placeholder="(Optional)" name="leverage" value="{{ $trade->leverage }}" id="leverage">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">P&L (USD)</label>
                                    <input class="form-control" type="number" placeholder="" name="extra" value="0.00">
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
    