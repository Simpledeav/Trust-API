@extends('layouts.admin')

@section('title', ' Auto-Plan')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
        <div class="page-title">
            <div class="row">
            <div class="col-6">
                <h4>
                    Auto Investing Plans</h4>
            </div>
            <div class="col-6">
                <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">                                       
                    <svg class="stroke-icon">
                        <use href="../assets/svg/icon-sprite.svg#stroke-home"></use>
                    </svg></a></li>
                <li class="breadcrumb-item">Dashboard </li>
                <li class="breadcrumb-item active">Auot-Investing</li>
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
                                    <h4>Plans</h4>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input class="form-control" id="inputEmail4" type="email" placeholder="Search...">
                                    <a class="btn btn-success w-100 mx-2" href="#" data-bs-toggle="modal" data-bs-target="#addAutoPlan">
                                        <i class="fa fa-plus"></i>Add Plans
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="row m-3">
                            @foreach($plans as $plan)
                                <div class="col-md-6 col-sm-6">
                                    <div class="card social-profile">
                                        <div class="card-body">
                                            <div class="social-img-wrap"> 
                                                <div class="social-img">
                                                    <img class="img-fluid rounded-circle" src="{{ $plan->img }}" alt="profile">
                                                </div>
                                                <div class="edit-icon">
                                                <svg>
                                                    @if($plan->status == 'active')
                                                        <use href="../assets/svg/icon-sprite.svg#profile-check"></use>
                                                    @else
                                                        <use href="../assets/svg/icon-sprite.svg"></use>
                                                    @endif
                                                </svg>
                                                </div>
                                            </div>
                                            <div class="social-details">
                                                <h2 class="mb-1">{{ $plan->name }}</h2>
                                                <ul class="social-follow"> 
                                                    <li>
                                                        <h5 class="mb-0">{{ $plan->min_invest }}</h5><span class="f-light">Min Investment</span>
                                                    </li>
                                                    <li>
                                                        <h5 class="mb-0">{{ $plan->max_invest }}</h5><span class="f-light">Max Investment</span>
                                                    </li>
                                                    <li>
                                                        <h5 class="mb-0">{{ $plan->milestone }} {{ $plan->duration }}</h5><span class="f-light">Duration</span>
                                                    </li>
                                                </ul>
                                                <ul class="social-follow"> 
                                                    <li>
                                                        <h5 class="mb-0">{{ $plan->win_rate }}%</h5><span class="f-light">Win Rate</span>
                                                    </li>
                                                    <li>
                                                        <h5 class="mb-0">{{ $plan->expected_returns }} ({{ $plan->day_returns }})</h5><span class="f-light">Returns</span>
                                                    </li>
                                                    <li>
                                                        <h5 class="mb-0">{{ $plan->aum }}</h5><span class="f-light">AMU (USD)</span>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="d-flex my-3">
                                                <button class="btn btn-primary w-100 mx-2" data-bs-toggle="modal" data-bs-target="#editAutoPlan{{ $plan->id }}">Edit</button>
                                                <form action="{{ route('admin.auto.plans.delete', $plan->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger w-100 mx-2" type="submit">Delete</button>
                                                </form>
                                            </div>
                                            <!-- Edit Auto Plan Modal -->
                                            <div class="modal fade" id="editAutoPlan{{ $plan->id }}" tabindex="-1" aria-labelledby="editAutoPlanLabel{{ $plan->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-body"> 
                                                            <h4 class="text-center pb-2">Edit Auto Plan</h4> 
                                                            <form action="{{ route('admin.auto.plans.update', $plan->id) }}" method="POST" enctype="multipart/form-data">
                                                                @csrf
                                                                @method('PUT')

                                                                <div class="row">
                                                                    @foreach ([
                                                                        'name' => 'Plan Name',
                                                                        'min_invest' => 'Minimum Investment',
                                                                        'max_invest' => 'Maximum Investment',
                                                                        'win_rate' => 'Win Rate',
                                                                        'aum' => 'AUM',
                                                                        'day_returns' => '24hrs Returns',
                                                                        'expected_returns' => 'Expected Returns',
                                                                    ] as $field => $label)
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label class="form-label">{{ $label }}</label>
                                                                            <input class="form-control" type="text" name="{{ $field }}" value="{{ $plan->$field }}" required>
                                                                        </div>
                                                                    </div>
                                                                    @endforeach

                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Type</label>
                                                                            <select class="form-select" name="type" id="type">
                                                                                <option value="conservative" {{ $plan->type == 'conservative' ? 'selected' : '' }}>Conservative</option>
                                                                                <option value="moderate" {{ $plan->type == 'moderate' ? 'selected' : '' }}>Moderate</option>
                                                                                <option value="aggressive" {{ $plan->type == 'aggressive' ? 'selected' : '' }}>Aggressive</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Milestone</label>
                                                                            <input class="form-control" type="number" name="milestone" required value="{{ $plan->milestone }}">
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Duration</label>
                                                                            <select class="form-select" name="duration" id="duration">
                                                                                <option value="">---- Select Duration -----</option>
                                                                                <option value="day" {{ $plan->duration == 'day' ? 'selected' : '' }}>Day(s)</option>
                                                                                <option value="month" {{ $plan->duration == 'month' ? 'selected' : '' }}>Month(s)</option>
                                                                                <option value="year" {{ $plan->duration == 'year' ? 'selected' : '' }}>Year(s)</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-12">
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Image</label>
                                                                            <input class="form-control" type="file" name="img" accept="image/*" onchange="previewImage(event, 'editImagePreview{{ $plan->id }}')">
                                                                            <img id="editImagePreview{{ $plan->id }}" class="img-fluid mt-2" src="{{ asset('storage/' . $plan->img) }}" style="max-width: 200px;" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="col-md-12">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Status</label>
                                                                        <div class="form-check-size">
                                                                            <div class="form-check form-switch form-check-inline">
                                                                                <input class="form-check-input check-size" type="checkbox" role="switch" name="status"
                                                                                    value="active" {{ old('status', $plan->status ?? '') === 'active' ? 'checked' : '' }}>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="form-footer d-flex mt-4">
                                                                    <button class="btn btn-primary btn-block" type="submit">Update</button>
                                                                    <button class="btn btn-secondary btn-block mx-2" type="button" data-bs-dismiss="modal">Cancel</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            @if($plans->count() < 1)

                            <div class="my-4">
                                <p class="text-center">No Plans</p>
                            </div>

                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Container-fluid Ends-->
    </div>

    <!-- Add Auto Plan Modal -->
    <div class="modal fade" id="addAutoPlan" tabindex="-1" aria-labelledby="addAutoPlanLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body"> 
                    <h4 class="text-center pb-2">Add Auto Plan</h4> 
                    <form action="{{ route('admin.auto.plans.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            @foreach ([
                                'name' => 'Plan Name',
                                'min_invest' => 'Minimum Investment',
                                'max_invest' => 'Maximum Investment',
                                'win_rate' => 'Win Rate',
                                'day_returns' => '24hrs Returns',
                                'expected_returns' => 'Expected Returns',
                                'aum' => 'AUM',
                            ] as $field => $label)
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">{{ $label }}</label>
                                    <input class="form-control" type="text" name="{{ $field }}" required>
                                </div>
                            </div>
                            @endforeach

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Type</label>
                                    <select class="form-select" name="type" id="type">
                                        <option value="conservative">Conservative</option>
                                        <option value="moderate">Moderate</option>
                                        <option value="aggressive">Aggressive</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Milestone</label>
                                    <input class="form-control" type="number" name="milestone" required placeholder="Enter number of duration...">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Duration</label>
                                    <select class="form-select" name="duration" id="duration">
                                        <option value="day">Day(s)</option>
                                        <option value="month">Month(s)</option>
                                        <option value="year">Year(s)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Image</label>
                                    <input class="form-control" type="file" name="img" accept="image/*" onchange="previewImage(event, 'addImagePreview')">
                                    <img id="addImagePreview" class="img-fluid mt-2" style="max-width: 200px; display: none;" />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <div class="form-check-size">
                                        <div class="form-check form-switch form-check-inline">
                                            <input class="form-check-input check-size" type="checkbox" role="switch" name="status"
                                                value="active" {{ old('status', $plan->status ?? '') === 'active' ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-footer d-flex mt-4">
                            <button class="btn btn-success btn-block" type="submit">Submit</button>
                            <button class="btn btn-danger btn-block mx-2" type="button" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('scripts')
    <script>
        function previewImage(event, previewId) {
            const reader = new FileReader();
            reader.onload = function(){
                const output = document.getElementById(previewId);
                output.src = reader.result;
                output.style.display = 'block';
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
    <script src="{{ asset('admin/assets/js/js-datatables/simple-datatables@latest.js') }}"></script>
    <script src="{{ asset('admin/assets/js/custom-list-product.js') }}"></script>
    <script src="{{ asset('admin/assets/js/owlcarousel/owl.carousel.js') }}"></script>
    <script src="{{ asset('admin/assets/js/ecommerce.js') }}"></script>
    <script src="{{ asset('admin/assets/js/tooltip-init.js') }}"></script>
@endsection