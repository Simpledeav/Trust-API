@extends('layouts.admin')

@section('title', ' Dashboard')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
        <div class="page-title">
            <div class="row">
            <div class="col-6">
                <h4>Dashboard</h4>
            </div>
            <div class="col-6"> 
                <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('index.html') }}">                                       
                    <svg class="stroke-icon">
                        <use href="../assets/svg/icon-sprite.svg#stroke-home"></use>
                    </svg></a></li>
                <li class="breadcrumb-item">Dashboard</li>
                <li class="breadcrumb-item active">Default</li>
                </ol>
            </div>
            </div>
        </div>
        </div>
        <!-- Container-fluid starts-->
        <div class="container-fluid"> 
        <div class="row size-column">
            <div class="col-xl-8 col-md-12 box-col-12">
            <div class="card">
                <div class="card-header sales-chart card-no-border">
                <h4>Statistics  </h4>
                <div class="sales-chart-dropdown"> 
                    <ul class="balance-data">
                    <li> <span class="circle bg-warning"> </span><span class="ms-1 f-w-400"> Transactions </span></li>
                    <li>  <span class="circle bg-primary"> </span><span class="ms-1 f-w-400">Online Sale</span></li>
                    </ul>
                    <div class="sales-chart-dropdown-select">
                    <div class="card-header-right-icon online-store"> 
                        <div class="dropdown"> 
                        <button class="btn dropdown-toggle dropdown-toggle-store" id="dropdownMenuButtonToggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">This Year</button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButtonToggle"><span class="dropdown-item">Last Month</span><span class="dropdown-item">Last Week</span><span class="dropdown-item">Today   </span></div>
                        </div>
                    </div>
                    </div>
                </div>
                </div>
                <div class="card-body pt-0"> 
                <div class="row"> 
                    <div class="col-xxl-8 col-xl-12">
                    <div class="revenuegrowth">
                        <div class="revenuegrowth-chart" id="revenuegrowth"></div>
                    </div>
                    </div>
                    <div class="col-xxl-4 col-xl-4 d-xxl-block d-none ">
                    <div class="revenuegrowth-details"> 
                        <div class="growth-details"><span class="f-light f-12  text-uppercase">Total </span>
                        <h4 class="f-w-500 mb-2">$56.265.08 </h4>
                        <div class="d-flex justify-content-center align-items-center gap-2 mb-4">
                            <p class="mb-0 f-w-500 f-12">Compared to  </p><span class="f-light f-12 f-w-500 ">(+40.15% than)</span>
                            <p class="mb-0 f-w-500 f-12">last year </p>
                        </div>
                        </div>
                        <div class="growth-details"><span class="f-light f-12  text-uppercase">Total Trades </span>
                        <h4 class="f-w-500 mb-2">$42,256.26  </h4>
                        <div class="d-flex justify-content-center align-items-center gap-2 mb-4">
                            <p class="mb-0 f-w-500 f-12">Compared to </p><span class="txt-secondary f-12 f-w-500">(-20.25% than) </span>
                            <p class="mb-0 f-w-500 f-12">last year  </p>
                        </div>
                        </div>
                        <div class="growth-details"> <span class="f-light f-12  text-uppercase">Total Savings</span>
                        <h4 class="f-w-500 mb-2">$5,215.62  </h4>
                        <div class="d-flex justify-content-center align-items-center gap-2">
                            <p class="mb-0 f-w-500 f-12">Compared to </p><span class=" f-w-500 f-light f-12">(+18.15% than)  </span>
                            <p class="mb-0 f-w-500 f-12">last year </p>
                        </div>
                        </div>
                    </div>
                    </div>
                </div>
                </div>
            </div>
            </div>
            <div class="col-xl-4 col-md-6 box-col-none">
            <div class="row">
                <div class="col-md-12 col-sm-6">
                    <div class="card"> 
                        <div class="card-header card-no-border total-revenue pb-0">
                        <h4>Latest Activities</h4>
                        <div class="icon-menu-header">
                            <svg>
                            <use href="../assets/svg/icon-sprite.svg#more-horizontal"></use>
                            </svg>
                        </div>
                        </div>
                        <div class="card-body pt-0" >
                            <div class="table-responsive custom-scrollbar deliveries-percentage" style="max-height: 350px; height: 350px;">
                                <table class="percentage-data w-100">
                                <thead> 
                                    <tr> 
                                    <th class="f-light f-12 f-w-500" scope="col">User</th>
                                    <th class="f-light f-12 f-w-500" scope="col">Amount</th>
                                    <th class="f-light f-12 f-w-500" scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    <tr>
                                        <td class="f-w-400 f-10"> <a class="line-clamp" href="{{ url('cart.html') }}">John Doe</a></td>
                                        <td class="f-w-500 f-10">$45,452.23</td>
                                        <td class="f-w-700 f-10"><a href="#" class="text-success">view</a></td>
                                    </tr>
                                    <tr>
                                        <td class="f-w-400 f-10"> <a class="line-clamp" href="{{ url('cart.html') }}">DEMO Test</a></td>
                                        <td class="f-w-500 f-10">$15,256.23</td>
                                        <td class="f-w-700 f-10"><a href="#" class="text-success">view</a></td>
                                    </tr>
                                    
                                </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
            <div class="col-xxl-3 col-sm-6 box-col-6">
                <div class="card height-equal">
                    <div class="card-header card-no-border total-revenue"> 
                    <h4>New User  </h4><a href="{{ url('product.html') }}">View All</a>
                    </div>
                    <div class="card-body pt-0"> 
                    <div class="new-user"> 
                        <ul> 
                        <li>
                            <div class="space-common d-flex user-name"><img class="img-40 rounded-circle img-fluid me-2" src="{{ asset('admin/assets/images/user/22.png') }}" alt="user"/>
                            <div class="common-space w-100">
                                <div>
                                <h6> <a class="f-w-500 f-14 " href="{{ url('user-profile.html') }}">Smith John</a></h6><span class="f-light f-w-500 f-12">India</span>
                                </div>
                                <div class="product-sub">
                                <div class="dropdown"> 
                                    <div id="dropdownMenuButtonicon31" data-bs-toggle="dropdown" aria-expanded="false" role="menu">
                                    <svg class="invoice-icon"> 
                                        <use href="../assets/svg/icon-sprite.svg#more-vertical"></use>
                                    </svg>
                                    </div>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButtonicon31"><span class="dropdown-item">Last Month </span><span class="dropdown-item">Last Week</span><span class="dropdown-item">Last Day </span></div>
                                </div>
                                </div>
                            </div>
                            </div>
                        </li>
                        <li>
                            <div class="space-common d-flex user-name"><img class="img-40 rounded-circle img-fluid me-2" src="{{ asset('admin/assets/images/user/28.png') }}" alt="user"/>
                            <div class="common-space w-100">
                                <div>
                                <h6> <a class="f-w-500 f-14 " href="{{ url('user-profile.html') }}">Robert Fox</a></h6><span class="f-light f-w-500 f-12">Afghanistan</span>
                                </div>
                                <div class="product-sub">
                                <div class="dropdown"> 
                                    <div id="dropdownMenuButtonicon32" data-bs-toggle="dropdown" aria-expanded="false" role="menu">
                                    <svg class="invoice-icon"> 
                                        <use href="../assets/svg/icon-sprite.svg#more-vertical"></use>
                                    </svg>
                                    </div>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButtonicon32"><span class="dropdown-item">Last Month </span><span class="dropdown-item">Last Week</span><span class="dropdown-item">Last Day </span></div>
                                </div>
                                </div>
                            </div>
                            </div>
                        </li>
                        <li>
                            <div class="space-common d-flex user-name"><img class="img-40 rounded-circle img-fluid me-2" src="{{ asset('admin/assets/images/user/26.png') }}" alt="user"/>
                            <div class="common-space w-100">
                                <div>
                                <h6> <a class="f-w-500 f-14 " href="{{ url('user-profile.html') }}">Darlene Robtson</a></h6><span class="f-light f-w-500 f-12">Georgia</span>
                                </div>
                                <div class="product-sub">
                                <div class="dropdown"> 
                                    <div id="dropdownMenuButtonicon33" data-bs-toggle="dropdown" aria-expanded="false" role="menu">
                                    <svg class="invoice-icon"> 
                                        <use href="../assets/svg/icon-sprite.svg#more-vertical"></use>
                                    </svg>
                                    </div>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButtonicon33"><span class="dropdown-item">Last Month </span><span class="dropdown-item">Last Week</span><span class="dropdown-item">Last Day </span></div>
                                </div>
                                </div>
                            </div>
                            </div>
                        </li>
                        <li>
                            <div class="space-common d-flex user-name"><img class="img-40 rounded-circle img-fluid me-2" src="{{ asset('admin/assets/images/user/24.png') }}" alt="user"/>
                            <div class="common-space w-100">
                                <div>
                                <h6> <a class="f-w-500 f-14 " href="{{ url('user-profile.html') }}">Floyd Miles</a></h6><span class="f-light f-w-500 f-12">Pakistan</span>
                                </div>
                                <div class="product-sub">
                                <div class="dropdown"> 
                                    <div id="dropdownMenuButtonicon34" data-bs-toggle="dropdown" aria-expanded="false" role="menu">
                                    <svg class="invoice-icon"> 
                                        <use href="../assets/svg/icon-sprite.svg#more-vertical"></use>
                                    </svg>
                                    </div>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButtonicon34"><span class="dropdown-item">Last Month </span><span class="dropdown-item">Last Week</span><span class="dropdown-item">Last Day </span></div>
                                </div>
                                </div>
                            </div>
                            </div>
                        </li>
                        <li>
                            <div class="space-common d-flex user-name"><img class="img-40 rounded-circle img-fluid me-2" src="{{ asset('admin/assets/images/user/49.png') }}" alt="user"/>
                            <div class="common-space w-100">
                                <div>
                                <h6> <a class="f-w-500 f-14 " href="{{ url('user-profile.html') }}">Jacob Jones</a></h6><span class="f-light f-w-500 f-12">Monaco</span>
                                </div>
                                <div class="product-sub">
                                <div class="dropdown"> 
                                    <div id="dropdownMenuButtonicon35" data-bs-toggle="dropdown" aria-expanded="false" role="menu">
                                    <svg class="invoice-icon"> 
                                        <use href="../assets/svg/icon-sprite.svg#more-vertical"></use>
                                    </svg>
                                    </div>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButtonicon35"><span class="dropdown-item">Last Month </span><span class="dropdown-item">Last Week</span><span class="dropdown-item">Last Day </span></div>
                                </div>
                                </div>
                            </div>
                            </div>
                        </li>
                        </ul>
                    </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-9 col-sm-6 box-col-6">
                <div class="card height-equal"> 
                    <div class="card-header card-no-border total-revenue pb-0"> 
                    <h4>Transaction Activity </h4><a href="{{ url('product.html') }}">View All </a>
                    </div>
                    <div class="card-body pt-0"> 
                        <div class="activity-table table-responsive custom-scrollbar">
                            <table class="order-table overflow-hidden project-table w-100 activity-log">
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="team-activity">
                                            <div class="activity-data d-flex align-items-center gap-3">
                                                <div class="common-space gap-2 "> 
                                                    <div class="user-activity me-3">
                                                        <img class="rounded-circle p-1 img-fluid me-3 img-50" src="{{ asset('admin/assets/images/user/50.png') }}" alt="user">
                                                        <a class="f-10 f-w-500 username line-clamp" href="{{ url('edit-profile.html') }}">Floyd Miles</a>
                                                    </div>
                                                    <div class="user-activity me-3">
                                                        <a class="f-10 f-w-500 p-1 me-3 line-clamp" href="{{ url('edit-profile.html') }}">Floyd Miles</a>
                                                    </div>
                                                    <div class="user-activity me-3">
                                                        <span class="f-w-700 f-10 p-1 me-3">$4500.65</span>
                                                    </div>
                                                    <div class="activity-time">
                                                        <span class="f-light f-w-500 f-10">5 min ago</span>
                                                    </div>
                                                </div>
                                                <div class="subtitle"> 
                                                    <p class="f-w-400 f-10">Floyd has moved to the warehouse.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="team-activity">
                                            <div class="activity-data d-flex align-items-center gap-3">
                                                <div class="common-space gap-2 "> 
                                                    <div class="user-activity me-3">
                                                        <img class="rounded-circle p-1 img-fluid me-3 img-50" src="{{ asset('admin/assets/images/user/50.png') }}" alt="user">
                                                        <a class="f-10 f-w-500 username" href="{{ url('edit-profile.html') }}">Floyd Miles</a>
                                                    </div>
                                                    <div class="user-activity me-3">
                                                        <a class="f-10 f-w-500 p-1 me-3" href="{{ url('edit-profile.html') }}">Floyd Miles</a>
                                                    </div>
                                                    <div class="user-activity me-3">
                                                        <span class="f-w-700 f-10 p-1 me-3">$4500.65</span>
                                                    </div>
                                                    <div class="activity-time">
                                                        <span class="f-light f-w-500 f-10">5 min ago</span>
                                                    </div>
                                                </div>
                                                <div class="subtitle"> 
                                                    <p class="f-w-400 f-10">Floyd has moved to the warehouse.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="team-activity">
                                            <div class="activity-data d-flex align-items-center gap-3">
                                                <div class="common-space gap-2 "> 
                                                    <div class="user-activity me-3">
                                                        <img class="rounded-circle p-1 img-fluid me-3 img-50" src="{{ asset('admin/assets/images/user/50.png') }}" alt="user">
                                                        <a class="f-10 f-w-500 username" href="{{ url('edit-profile.html') }}">Floyd Miles</a>
                                                    </div>
                                                    <div class="user-activity me-3">
                                                        <a class="f-10 f-w-500 p-1 me-3" href="{{ url('edit-profile.html') }}">Floyd Miles</a>
                                                    </div>
                                                    <div class="user-activity me-3">
                                                        <span class="f-w-700 f-10 p-1 me-3">$4500.65</span>
                                                    </div>
                                                    <div class="activity-time">
                                                        <span class="f-light f-w-500 f-10">5 min ago</span>
                                                    </div>
                                                </div>
                                                <div class="subtitle"> 
                                                    <p class="f-w-400 f-10">Floyd has moved to the warehouse.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="team-activity">
                                            <div class="activity-data d-flex align-items-center gap-3">
                                                <div class="common-space gap-2 "> 
                                                    <div class="user-activity me-3">
                                                        <img class="rounded-circle p-1 img-fluid me-3 img-50" src="{{ asset('admin/assets/images/user/50.png') }}" alt="user">
                                                        <a class="f-10 f-w-500 username" href="{{ url('edit-profile.html') }}">Floyd Miles</a>
                                                    </div>
                                                    <div class="user-activity me-3">
                                                        <a class="f-10 f-w-500 p-1 me-3" href="{{ url('edit-profile.html') }}">Floyd Miles</a>
                                                    </div>
                                                    <div class="user-activity me-3">
                                                        <span class="f-w-700 f-10 p-1 me-3">$4500.65</span>
                                                    </div>
                                                    <div class="activity-time">
                                                        <span class="f-light f-w-500 f-10">5 min ago</span>
                                                    </div>
                                                </div>
                                                <div class="subtitle"> 
                                                    <p class="f-w-400 f-10">Floyd has moved to the warehouse.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="team-activity">
                                            <div class="activity-data d-flex align-items-center gap-3">
                                                <div class="common-space gap-2 "> 
                                                    <div class="user-activity me-3">
                                                        <img class="rounded-circle p-1 img-fluid me-3 img-50" src="{{ asset('admin/assets/images/user/50.png') }}" alt="user">
                                                        <a class="f-10 f-w-500 username" href="{{ url('edit-profile.html') }}">Floyd Miles</a>
                                                    </div>
                                                    <div class="user-activity me-3">
                                                        <a class="f-10 f-w-500 p-1 me-3" href="{{ url('edit-profile.html') }}">Floyd Miles</a>
                                                    </div>
                                                    <div class="user-activity me-3">
                                                        <span class="f-w-700 f-10 p-1 me-3">$4500.65</span>
                                                    </div>
                                                    <div class="activity-time">
                                                        <span class="f-light f-w-500 f-10">5 min ago</span>
                                                    </div>
                                                </div>
                                                <div class="subtitle"> 
                                                    <p class="f-w-400 f-10">Floyd has moved to the warehouse.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="team-activity">
                                            <div class="activity-data d-flex align-items-center gap-3">
                                                <div class="common-space gap-2 "> 
                                                    <div class="user-activity me-3">
                                                        <img class="rounded-circle p-1 img-fluid me-3 img-50" src="{{ asset('admin/assets/images/user/50.png') }}" alt="user">
                                                        <a class="f-10 f-w-500 username" href="{{ url('edit-profile.html') }}">Floyd Miles</a>
                                                    </div>
                                                    <div class="user-activity me-3">
                                                        <a class="f-10 f-w-500 p-1 me-3" href="{{ url('edit-profile.html') }}">Floyd Miles</a>
                                                    </div>
                                                    <div class="user-activity me-3">
                                                        <span class="f-w-700 f-10 p-1 me-3">$4500.65</span>
                                                    </div>
                                                    <div class="activity-time">
                                                        <span class="f-light f-w-500 f-10">5 min ago</span>
                                                    </div>
                                                </div>
                                                <div class="subtitle"> 
                                                    <p class="f-w-400 f-10">Floyd has moved to the warehouse.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        <!-- Container-fluid Ends-->
    </div>
@endsection
