@extends('layouts.admin')

@section('title', ' Dashboard')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
        <div class="page-title">
            <div class="row">
            <div class="col-6">
                <h4>
                    Article list</h4>
            </div>
            <div class="col-6">
                <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">                                       
                    <svg class="stroke-icon">
                        <use href="../assets/svg/icon-sprite.svg#stroke-home"></use>
                    </svg></a></li>
                <li class="breadcrumb-item">Dashboard </li>
                <li class="breadcrumb-item active">Articles</li>
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
                                    <h4>Articles</h4>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input class="form-control" id="inputEmail4" type="email" placeholder="Search...">
                                    <a class="btn btn-success w-100 mx-2" href="#" data-bs-toggle="modal" data-bs-target="#addArticle">
                                        <i class="fa fa-plus"></i>Add Article
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive custom-scrollbar px-4">
                            <table class="table">
                                <thead>
                                <tr class="border-bottom-primary">
                                    <th> <span class="f-light f-w-600">S/N</span></th>
                                    <th> <span class="f-light f-w-600">Title</span></th>
                                    <th> <span class="f-light f-w-600">Slug</span></th>
                                    <th> <span class="f-light f-w-600">Category </span></th>
                                    <th> <span class="f-light f-w-600">Status</span></th>
                                    <th> <span class="f-light f-w-600">Date</span></th>
                                    <th> <span class="f-light f-w-600">Action</span></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($articles as $index => $article)
                                    <tr class="">
                                        <td>{{ $index +  1 }}</td>
                                        <td> 
                                            <div class="product-names fw-bold">
                                                <a href="#" class="text-success  truncate-content">{{ $article->title }}</a>
                                            </div>
                                        </td>
                                        <td> 
                                            <p class="truncate-content">{{ $article->slug }}</p>
                                        </td>
                                        <td> 
                                            <p class="f-light fw-bold text-capitalize">{{ $article->category }}</p>
                                        </td>
                                        <td> 
                                            <span class="badge @if($article->status == 'enabled') badge-light-success @else badge-light-danger @endif">
                                                @if($article->status == 'enabled') Active @else Inactive @endif
                                            </span>
                                        </td>
                                        <td> 
                                            <p class="f-light truncate-content">{{ $article['created_at']->format('d M, Y \a\t h:i A') }}</p>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button class="btn btn-dark rounded-pill dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Action</button>
                                                    <ul class="dropdown-menu dropdown-menu-dark dropdown-block">
                                                        <li>
                                                            <a href="#" class="dropdown-item text-dark fw-bold" data-bs-toggle="modal" data-bs-target="#editArticle{{ $article->id }}">
                                                                Edit
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('admin.article.toggle', $article->id) }}" method="POST" style="display: inline;">
                                                                @csrf
                                                                @method('PUT')
                                                                 @if($article->status == 'disabled')
                                                                    <input type="hidden" name="action" value="enabled">
                                                                    <button type="submit" class="dropdown-item fw-bold text-dark">Enable</button>
                                                                @else
                                                                    <input type="hidden" name="action" value="disabled">
                                                                    <button type="submit" class="dropdown-item fw-bold text-dark">Disable</button>
                                                                @endif
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('admin.article.destroy', $article->id) }}" method="POST" style="display: inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <!-- <input type="hidden" name="action" value="open"> -->
                                                                <button type="submit" class="dropdown-item fw-bold text-danger">Delete</button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Edit Trade Modal -->
                                    <div class="modal fade" id="editArticle{{$article->id}}" tabindex="-1" aria-labelledby="editArticle{{$article->id}}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-body"> 
                                                    <div class="modal-toggle-wrapper"> 
                                                        <h4 class="text-center pb-2" id="">Edit Article</h4> 
                                                        <form id="transactionForm" action="{{ route('admin.article.edit', $article->id) }}" method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="col-md-12">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Title</label>
                                                                    <input class="form-control" type="text" placeholder="Enter title..." name="title" required value="{{ $article->title }}">
                                                                </div>
                                                            </div>

                                                            <div class="col-md-12">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Category</label>
                                                                    <select class="form-control" name="category" required>
                                                                        <option value="" disabled selected>Select a category</option>
                                                                        <option value="business" {{ $article->category == 'business' ? 'selected' : '' }}>Business</option>
                                                                        <option value="investing" {{ $article->category == 'investing' ? 'selected' : '' }}>Investing</option>
                                                                        <option value="savings" {{ $article->category == 'savings' ? 'selected' : '' }}>Savings</option>
                                                                        <option value="retirement" {{ $article->category == 'retirement' ? 'selected' : '' }}>Retirement</option>
                                                                        <option value="management" {{ $article->category == 'management' ? 'selected' : '' }}>Management</option>
                                                                        <option value="trends" {{ $article->category == 'trends' ? 'selected' : '' }}>Trends</option>
                                                                        <option value="technology" {{ $article->category == 'technology' ? 'selected' : '' }}>Technology</option>
                                                                        <option value="news" {{ $article->category == 'news' ? 'selected' : '' }}>News</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label">Content</label>
                                                                <textarea id="editorEdit{{ $index +  1 }}" name="content" class="form-control">{!! $article->content !!}</textarea>
                                                            </div>

                                                            <div class="col-md-12">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Image</label>
                                                                    <input class="form-control" type="file" name="image" id="imageEdit" accept="image/*" onchange="previewImageEdit(event)">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <img id="imageEditPreview" class="@if($article->image) d-block @else d-none @endif" src="{{ isset($article->image) ? asset($article->image) : '' }}" alt="Image Preview" class="img-fluid" style="max-width: 350px; height: auto;">
                                                                </div>
                                                            </div>

                                                            <div class="col-md-12">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Date</label>
                                                                    <input class="form-control" type="datetime-local" name="created_at" id="dateEdit" required value="{{ $article->created_at }}">
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
                                    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
                                    <script>
                                        CKEDITOR.replace('editorEdit{{ $index +  1 }}', {
                                            toolbar: [
                                                { name: 'document', items: ['Source', '-', 'Preview', 'Print'] },
                                                { name: 'clipboard', items: ['Undo', 'Redo'] },
                                                { name: 'editing', items: ['Find', 'Replace', '-', 'SelectAll'] },
                                                { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike'] },
                                                { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Blockquote'] },
                                                // { name: 'insert', items: ['Image', 'Table', 'HorizontalRule', 'SpecialChar'] },
                                                { name: 'styles', items: ['Format', 'Font', 'FontSize'] },
                                                { name: 'colors', items: ['TextColor', 'BGColor'] },
                                            ],
                                            height: 300, // Adjust height
                                            removePlugins: 'elementspath', // Removes element path display
                                            resize_enabled: false // Disables resizing
                                        });
                                    </script>
                                @endforeach
                                </tbody>
                            </table>
                            @if($articles->count() < 1)
                                <div class="">
                                    <p class="text-center my-4 py-4">No data</p>
                                </div>
                            @endif
                            <!-- Pagination Links -->
                            <div class="jsgrid-pager my-3 mx-2">
                                Pages:
                                @if ($articles->onFirstPage())
                                    <span class="jsgrid-pager-nav-button jsgrid-pager-nav-inactive-button">
                                        <a href="javascript:void(0);">First</a>
                                    </span>
                                    <span class="jsgrid-pager-nav-button jsgrid-pager-nav-inactive-button">
                                        <a href="javascript:void(0);">Prev</a>
                                    </span>
                                @else
                                    <span class="jsgrid-pager-nav-button">
                                        <a href="{{ $articles->url(1) }}">First</a>
                                    </span>
                                    <span class="jsgrid-pager-nav-button">
                                        <a href="{{ $articles->previousPageUrl() }}">Prev</a>
                                    </span>
                                @endif

                                <!-- Page Numbers -->
                                @foreach ($articles->getUrlRange(1, $articles->lastPage()) as $page => $url)
                                    @if ($page == $articles->currentPage())
                                        <span class="jsgrid-pager-page jsgrid-pager-current-page">{{ $page }}</span>
                                    @else
                                        <span class="jsgrid-pager-page">
                                            <a href="{{ $url }}">{{ $page }}</a>
                                        </span>
                                    @endif
                                @endforeach

                                @if ($articles->hasMorePages())
                                    <span class="jsgrid-pager-nav-button">
                                        <a href="{{ $articles->nextPageUrl() }}" class="fw-bold">Next</a>
                                    </span>
                                    <span class="jsgrid-pager-nav-button">
                                        <a href="{{ $articles->url($articles->lastPage()) }}" class="fw-bold">Last</a>
                                    </span>
                                @else
                                    <span class="jsgrid-pager-nav-button jsgrid-pager-nav-inactive-button">
                                        <a href="javascript:void(0);" class="fw-bold">Next</a>
                                    </span>
                                    <span class="jsgrid-pager-nav-button jsgrid-pager-nav-inactive-button">
                                        <a href="javascript:void(0);" class="fw-bold">Last</a>
                                    </span>
                                @endif

                                &nbsp;&nbsp; {{ $articles->currentPage() }} of {{ $articles->lastPage() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Container-fluid Ends-->
    </div>

    <!-- Reusable Modal -->
    <div class="modal fade" id="addArticle" tabindex="-1" aria-labelledby="addArticle" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body"> 
                    <div class="modal-toggle-wrapper"> 
                        <h4 class="text-center pb-2" id="">Add Article</h4> 
                        <form id="transactionForm" action="{{ route('admin.article.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Title</label>
                                    <input class="form-control" type="text" placeholder="Enter title..." name="title" required>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Category</label>
                                    <select class="form-control" name="category" required>
                                        <option value="" disabled selected>Select a category</option>
                                        <option value="business">Business</option>
                                        <option value="investing">Investing</option>
                                        <option value="savings">Savings</option>
                                        <option value="retirement">Retirement</option>
                                        <option value="management">Management</option>
                                        <option value="trends">Trends</option>
                                        <option value="technology">Technology</option>
                                        <option value="news">News</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Content</label>
                                <textarea id="editor" name="content" class="form-control"></textarea>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Image</label>
                                    <input class="form-control" type="file" name="image" id="image" accept="image/*" onchange="previewImage(event)">
                                </div>
                                <div class="mb-3">
                                    <img id="imagePreview" src="" alt="Image Preview" class="img-fluid" style="max-width: 200px; height: auto; display: none;">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Date</label>
                                    <input class="form-control" type="datetime-local" name="created_at" id="date" required>
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
    <!-- Credit Modal -->

@endsection

@section('scripts')

    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('editor', {
            toolbar: [
                { name: 'document', items: ['Source', '-', 'Preview', 'Print'] },
                { name: 'clipboard', items: ['Undo', 'Redo'] },
                { name: 'editing', items: ['Find', 'Replace', '-', 'SelectAll'] },
                { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike'] },
                { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Blockquote'] },
                // { name: 'insert', items: ['Image', 'Table', 'HorizontalRule', 'SpecialChar'] },
                { name: 'styles', items: ['Format', 'Font', 'FontSize'] },
                { name: 'colors', items: ['TextColor', 'BGColor'] },
            ],
            height: 300, // Adjust height
            removePlugins: 'elementspath', // Removes element path display
            resize_enabled: false // Disables resizing
        });

        document.addEventListener("DOMContentLoaded", function() {
            let now = new Date();
            let formattedDateTime = now.toISOString().slice(0, 16); // Format: YYYY-MM-DDTHH:MM
            document.getElementById("date").value = formattedDateTime;
        });

        function previewImage(event) {
            const image = document.getElementById("image").files[0];
            const imagePreview = document.getElementById("imagePreview");

            if (image) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = "block";
                };
                reader.readAsDataURL(image);
            }
        }

        function previewImageEdit(event) {
            const image = document.getElementById("imageEdit").files[0];
            const imagePreview = document.getElementById("imageEditPreview");

            if (image) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = "block";
                };
                reader.readAsDataURL(image);
            }
        }

    </script>

    <script src="{{ asset('admin/assets/js/js-datatables/simple-datatables@latest.js') }}"></script>
    <script src="{{ asset('admin/assets/js/custom-list-product.js') }}"></script>
    <script src="{{ asset('admin/assets/js/owlcarousel/owl.carousel.js') }}"></script>
    <script src="{{ asset('admin/assets/js/ecommerce.js') }}"></script>
    <script src="{{ asset('admin/assets/js/tooltip-init.js') }}"></script>
@endsection
    