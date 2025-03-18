@extends('layouts.admin')

@section('title', ' Dashboard')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">

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
                            </div>
                            <div class="d-flex align-items-center">
                                <input class="form-control" id="inputEmail4" type="email" placeholder="Search...">
                                <a class="btn btn-success fw-bold mx-2 d-flex align-items-center" href="#" data-bs-toggle="modal" data-bs-target="#addAccount"><i class="fa fa-plus mx-2"></i> Add</a>
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
                                            <button class="btn btn-dark rounded-pill dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Action</button>
                                            <ul class="dropdown-menu dropdown-menu-dark dropdown-block">
                                                <li>
                                                    <button type="button" class="dropdown-item fw-bold" data-bs-toggle="modal" data-bs-target="#editAccount{{ $account->slug }}">
                                                        Edit
                                                    </button>
                                                </li>
                                                <li>
                                                    <form action="{{ route('admin.account.savings.destroy', $account->id) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-white bg-danger fw-bold">Delete</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>

                                <div class="modal fade" id="editAccount{{ $account->slug }}" tabindex="-1" aria-labelledby="editAccount{{ $account->slug }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body"> 
                <div class="modal-toggle-wrapper"> 
                    <h4 class="text-center pb-2" id="">Edit Account</h4> 
                    <form id="transactionForm" action="{{ route('admin.account.savings.update', $account->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input class="form-control" type="text" placeholder="Enter name..." name="name" required value="{{ $account->name }}">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Title</label>
                                <input class="form-control" type="text" placeholder="Enter title..." name="title" required value="{{ $account->title }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Note</label>
                            <textarea id="editor-{{ $account->slug }}" name="note" class="form-control">{{ $account->note }}</textarea>
                        </div>

                        <!-- Worldwide Checkbox -->
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">
                                    <input type="checkbox" id="worldwideCheckbox-{{ $account->slug }}"> Worldwide
                                </label>
                            </div>
                        </div>

                        <!-- Countries Select Field -->
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Countries</label>
                                <select id="country-select-{{ $account->slug }}" name="countries_id[]" multiple required class="text-capitalize">
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}" 
                                            @if(in_array($country->id, json_decode($account->country_id, true) ?? [])) selected @endif>
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-footer mt-4 d-flex">
                            <button class="btn btn-success btn-block" type="submit">Submit</button>
                            <button class="btn btn-danger btn-block mx-2" type="button" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

                                <script src="https://cdn.jsdelivr.net/npm/tom-select"></script>
                                <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
                                <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize TomSelect for the countries dropdown
        const countrySelect = new TomSelect("#country-select-{{ $account->slug }}", {
            plugins: ['remove_button'],
            maxItems: null,
            placeholder: "Select Countries",
        });

        // Add event listener to the Worldwide checkbox
        const worldwideCheckbox = document.getElementById('worldwideCheckbox-{{ $account->slug }}');
        worldwideCheckbox.addEventListener('change', function (e) {
            const isChecked = e.target.checked;
            const options = document.querySelectorAll('#country-select-{{ $account->slug }} option');

            if (isChecked) {
                // Select all countries
                options.forEach(option => {
                    option.selected = true;
                });
            } else {
                // Deselect all countries
                options.forEach(option => {
                    option.selected = false;
                });
            }

            // Refresh TomSelect to reflect changes
            countrySelect.sync();
        });

        // Initialize CKEditor
        CKEDITOR.replace('editor-{{ $account->slug }}', {
            toolbar: [
                { name: 'document', items: ['Source', '-', 'Preview', 'Print'] },
                { name: 'clipboard', items: ['Undo', 'Redo'] },
                { name: 'editing', items: ['Find', 'Replace', '-', 'SelectAll'] },
                { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike'] },
                { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Blockquote'] },
                { name: 'styles', items: ['Format', 'Font', 'FontSize'] },
                { name: 'colors', items: ['TextColor', 'BGColor'] },
            ],
            height: 300,
            removePlugins: 'elementspath',
            resize_enabled: false
        });
    });
</script>
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

   <!-- Reusable Modal -->
    <div class="modal fade" id="addAccount" tabindex="-1" aria-labelledby="addAccount" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body"> 
                    <div class="modal-toggle-wrapper"> 
                        <h4 class="text-center pb-2" id="">Add Account</h4> 
                        <form id="transactionForm" action="{{ route('admin.account.savings.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input class="form-control" type="text" placeholder="Enter name..." name="name" required>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Title</label>
                                    <input class="form-control" type="text" placeholder="Enter title..." name="title" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Note</label>
                                <textarea id="editor" name="note" class="form-control"></textarea>
                            </div>

                            <!-- Worldwide Checkbox -->
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <input type="checkbox" id="worldwideCheckbox"> Worldwide
                                    </label>
                                </div>
                            </div>

                            <!-- Countries Select Field -->
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Countries</label>
                                    <select id="country-select" name="countries_id[]" multiple required>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id }}" class="text-captialize">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-footer mt-4 d-flex">
                                <button class="btn btn-success btn-block" type="submit">Submit</button>
                                <button class="btn btn-danger btn-block mx-2" type="button" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select"></script>
    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
    <script>
        // Initialize TomSelect for the countries dropdown
        const countrySelect = new TomSelect("#country-select", {
            plugins: ['remove_button'],
            maxItems: null,
            placeholder: "Select Countries",
        });

        // Add event listener to the Worldwide checkbox
        document.getElementById('worldwideCheckbox').addEventListener('change', function (e) {
            const isChecked = e.target.checked;
            const options = document.querySelectorAll('#country-select option');

            if (isChecked) {
                // Select all countries
                options.forEach(option => {
                    option.selected = true;
                });
            } else {
                // Deselect all countries
                options.forEach(option => {
                    option.selected = false;
                });
            }

            // Refresh TomSelect to reflect changes
            countrySelect.sync();
        });

        // Initialize CKEditor
        CKEDITOR.replace('editor', {
            toolbar: [
                { name: 'document', items: ['Source', '-', 'Preview', 'Print'] },
                { name: 'clipboard', items: ['Undo', 'Redo'] },
                { name: 'editing', items: ['Find', 'Replace', '-', 'SelectAll'] },
                { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike'] },
                { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Blockquote'] },
                { name: 'styles', items: ['Format', 'Font', 'FontSize'] },
                { name: 'colors', items: ['TextColor', 'BGColor'] },
            ],
            height: 300,
            removePlugins: 'elementspath',
            resize_enabled: false
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