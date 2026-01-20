@extends('layouts.app')

@push('datatable-styles')
    @include('sections.daterange_css')
@endpush

@push('styles')
    <style>
        .h-200 {
            height: 340px;
            overflow-y: auto;
        }

        .dashboard-settings {
            width: 600px;
        }

        @media (max-width: 768px) {
            .dashboard-settings {
                width: 300px;
            }
        }

        /* Professional Dashboard Styling */
        .dashboard-header {
            background: #ffffff;
            border-bottom: 1px solid #e0e0e0;
            padding: 15px 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .dashboard-card {
            background: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: box-shadow 0.3s ease;
        }

        .dashboard-card:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        .dashboard-card h4 {
            color: #333333;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 5px;
        }

        .dashboard-card .value {
            font-size: 20px;
            color: #444444;
            font-weight: 500;
            margin-bottom: 10px;
        }

        .user-activity-timeline {
            background: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .user-activity-timeline h4 {
            color: #333333;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 5px;
        }

        .user-activity-timeline li {
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
            color: #666666;
            font-size: 14px;
        }

        .user-activity-timeline li:last-child {
            border-bottom: none;
        }

        .user-activity-timeline i {
            color: #666666;
            margin-right: 8px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th, .data-table td {
            padding: 12px 15px;
            text-align: center;
            border-bottom: 1px solid #e0e0e0;
            color: #444444;
            font-size: 14px;
        }

        .data-table th {
            background: #f5f5f5;
            color: #333333;
            font-weight: 600;
            text-transform: uppercase;
        }

        .data-table tr:hover {
            background: #f9f9f9;
        }
    </style>
@endpush

@section('filter-section')
    <!-- FILTER START -->
    <!-- DASHBOARD HEADER START -->
    <div class="d-flex filter-box project-header bg-white dashboard-header">
        <div class="mobile-close-overlay w-100 h-100" id="close-client-overlay"></div>
        <div class="project-menu d-lg-flex" id="mob-client-detail">
            <a class="d-none close-it" href="javascript:;" id="close-client-detail">
                <i class="fa fa-times"></i>
            </a>

            @if ($viewOverviewDashboard == 'all')
                <x-tab :href="route('dashboard.advanced').'?tab=overview'" :text="__('modules.projects.overview')"
                       class="overview" ajax="false"/>
            @endif

            @if (in_array('projects', user_modules()) && $viewProjectDashboard == 'all')
                <x-tab :href="route('dashboard.advanced').'?tab=project'" :text="__('app.project')" class="project"
                       ajax="false"/>
            @endif

            @if (in_array('clients', user_modules()) && $viewClientDashboard == 'all')
                <x-tab :href="route('dashboard.advanced').'?tab=client'" :text="__('app.client')" class="client"
                       ajax="false"/>
            @endif

            @if ($viewHRDashboard == 'all')
                <x-tab :href="route('dashboard.advanced').'?tab=hr'" :text="__('app.menu.hr')" class="hr" ajax="false"/>
            @endif

            @if (in_array('tickets', user_modules()) && $viewTicketDashboard == 'all')
                <x-tab :href="route('dashboard.advanced').'?tab=ticket'" :text="__('app.menu.ticket')" class="ticket"
                       ajax="false"/>
            @endif

            @if ($viewFinanceDashboard == 'all')
                <x-tab :href="route('dashboard.advanced').'?tab=finance'" :text="__('app.menu.finance')" class="finance"
                       ajax="false"/>
            @endif
        </div>

        <div class="ml-auto d-flex align-items-center justify-content-center ">
            <!-- DATE START -->
            <div
                class="{{ request('tab') == 'overview' || request('tab') == '' ? 'd-none' : 'd-flex' }} align-items-center border-left-grey border-left-grey-sm-0 h-100 pl-4">
                <i class="fa fa-calendar-alt mr-2 f-14 text-dark-grey"></i>
                <div class="select-status">
                    <input type="text"
                           class="position-relative text-dark form-control border-0 p-2 text-left f-14 f-w-500 border-additional-grey"
                           id="datatableRange2" placeholder="@lang('placeholders.dateRange')">
                </div>
            </div>
            <!-- DATE END -->
            @if (isset($widgets) && in_array('admin', user_roles()))
                <div class="admin-dash-settings">
                    <x-form id="dashboardWidgetForm" method="POST">
                        <div class="dropdown keep-open">
                            <a class="d-flex align-items-center justify-content-center dropdown-toggle px-lg-4 border-left-grey text-dark"
                               type="link" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true"
                               aria-expanded="false">
                                <i class="fa fa-cog" title="{{__('modules.dashboard.dashboardWidgetsSettings')}}" data-toggle="tooltip"></i>
                            </a>
                            <!-- Dropdown - User Information -->
                            <ul class="dropdown-menu dropdown-menu-right dashboard-settings p-20"
                                aria-labelledby="dropdownMenuLink" tabindex="0">
                                <li class="border-bottom mb-3">
                                    <h4 class="heading-h3">@lang('modules.dashboard.dashboardWidgets')</h4>
                                </li>
                                @foreach ($widgets as $widget)
                                    @php
                                        $wname = \Illuminate\Support\Str::camel($widget->widget_name);
                                    @endphp
                                    <li class="mb-2 float-left w-50">
                                        <div class="checkbox checkbox-info ">
                                            <input id="{{ $widget->widget_name }}" name="{{ $widget->widget_name }}"
                                                   value="true" @if ($widget->status) checked @endif type="checkbox">
                                            <label for="{{ $widget->widget_name }}">@lang('modules.dashboard.' .
                                            $wname)</label>
                                        </div>
                                    </li>
                                @endforeach
                                @if (count($widgets) % 2 != 0)
                                    <li class="mb-2 float-left w-50 height-35"></li>
                                @endif
                                <li class="float-none w-100">
                                    <x-forms.button-primary id="save-dashboard-widget" icon="check">@lang('app.save')
                                    </x-forms.button-primary>
                                </li>
                            </ul>
                        </div>
                    </x-form>
                </div>
            @endif
        </div>

        <a class="mb-0 d-block d-lg-none text-dark-grey mr-2 border-left-grey border-bottom-0"
           onclick="openClientDetailSidebar()"><i class="fa fa-ellipsis-v"></i></a>
    </div>
    <!-- FILTER END -->
    <!-- DASHBOARD HEADER END -->
@endsection

@section('content')
    <!-- CONTENT WRAPPER START -->
    <div class="px-4 py-0 py-lg-3 border-top-0 admin-dashboard">
        <div class="row">
            <!-- User Profile Card -->
            <div class="col-md-12">
                <div class="dashboard-card">
                    <h4>User Profile</h4>
                    <div class="value">Preeti Sabat</div>
                </div>
            </div>

            <!-- Today Data Card -->
            <div class="col-md-12 mt-4">
                <div class="dashboard-card">
                    <h4>Today - {{ date('F d, Y') }}</h4>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Number of Present Day</th>
                                <th>Target Login Hrs</th>
                                <th>Total Login</th>
                                <th>Extra Login hrs</th>
                                <th>Less Login Hrs</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Preeti Sabat</td>
                                <td>1</td>
                                <td>7</td>
                                <td>8</td>
                                <td>1</td>
                                <td>0</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Monthly Data Card -->
            <div class="col-md-12 mt-4">
                <div class="dashboard-card">
                    <h4>Monthly - {{ date('F Y') }}</h4>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Number of Present Day</th>
                                <th>Target Login Hrs</th>
                                <th>Total Login</th>
                                <th>Extra Login hrs</th>
                                <th>Less Login Hrs</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Preeti Sabat</td>
                                <td>20</td>
                                <td>140 (20 days x 7 hrs)</td>
                                <td>145</td>
                                <td>5</td>
                                <td>0</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- User Activity Timeline -->
            <div class="col-md-12 mt-4">
                <div class="user-activity-timeline">
                    <h4>User Activity Timeline</h4>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-user mr-2"></i> Preeti Sabat - Logged in - 09:43 am</li>
                        <li><i class="fas fa-user-edit mr-2"></i> Preeti Sabat - Updated Profile - 08:20 am</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- CONTENT WRAPPER END -->
@endsection

@push('scripts')
    <script src="{{ asset('vendor/jquery/daterangepicker.min.js') }}"></script>
    <script type="text/javascript">
        $(function () {
            var format = '{{ company()->moment_date_format }}';
            var startDate = "{{ $startDate->translatedFormat(company()->date_format) }}";
            var endDate = "{{ $endDate->translatedFormat(company()->date_format) }}";
            var start = moment(startDate, format);
            var end = moment(endDate, format);

            $('#datatableRange2').daterangepicker({
                locale: daterangeLocale,
                linkedCalendars: false,
                startDate: start,
                endDate: end,
                ranges: daterangeConfig,
                opens: 'left',
                parentEl: '.dashboard-header'
            }, cb);

            $('#datatableRange2').on('apply.daterangepicker', function (ev, picker) {
                showTable();
            });
        });
    </script>

    <script>
        $(".dashboard-header").on("click", ".ajax-tab", function (event) {
            event.preventDefault();

            $('.project-menu .p-sub-menu').removeClass('active');
            $(this).addClass('active');

            const dateRangePicker = $('#datatableRange2').data('daterangepicker');
            let startDate = $('#datatableRange').val();

            let endDate;

            if (startDate === '') {
                startDate = null;
                endDate = null;
            } else {
                startDate = dateRangePicker.startDate.format('{{ company()->moment_date_format }}');
                endDate = dateRangePicker.endDate.format('{{ company()->moment_date_format }}');
            }

            const requestUrl = this.href;

            $.easyAjax({
                url: requestUrl,
                blockUI: true,
                container: ".admin-dashboard",
                historyPush: true,
                data: {
                    startDate: startDate,
                    endDate: endDate
                },
                success: function (response) {
                    if (response.status === "success") {
                        $('.admin-dashboard').html(response.html);
                        init('.admin-dashboard');
                    }
                }
            });
        });

        $('.keep-open .dropdown-menu').on({
            "click": function (e) {
                e.stopPropagation();
            }
        });

        function showTable() {
            const dateRangePicker = $('#datatableRange2').data('daterangepicker');
            let startDate = $('#datatableRange').val();

            let endDate;
            if (startDate === '') {
                startDate = null;
                endDate = null;
            } else {
                startDate = dateRangePicker.startDate.format('{{ company()->moment_date_format }}');
                endDate = dateRangePicker.endDate.format('{{ company()->moment_date_format }}');
            }

            const requestUrl = this.href;

            $.easyAjax({
                url: requestUrl,
                blockUI: true,
                container: ".admin-dashboard",
                data: {
                    startDate: startDate,
                    endDate: endDate
                },
                success: function (response) {
                    if (response.status === "success") {
                        $('.admin-dashboard').html(response.html);
                        init('.admin-dashboard');
                    }
                }
            });
        }
    </script>
    <script>
        const activeTab = "{{ $activeTab }}";
        $('.project-menu .' + activeTab).addClass('active');
    </script>
@endpush