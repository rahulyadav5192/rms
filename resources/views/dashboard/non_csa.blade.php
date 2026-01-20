<script>
document.addEventListener("DOMContentLoaded", function() {
    var duration = 3 * 1000; // 3 seconds
    var end = Date.now() + duration;

    (function frame() {
        confetti({
            particleCount: 5,
            angle: 60,
            spread: 55,
            origin: { x: 0, y: 0 }, // Force top-left
            startVelocity: 45 // Faster so it comes from top
        });
        confetti({
            particleCount: 5,
            angle: 120,
            spread: 55,
            origin: { x: 1, y: 0 }, // Force top-right
            startVelocity: 45
        });

        if (Date.now() < end) {
            requestAnimationFrame(frame);
        }
    }());
});
</script>


    
@extends('layouts.app')
<script>
        document.addEventListener("DOMContentLoaded", function() {
            var duration = 1 * 1000; // 3 seconds
            var end = Date.now() + duration;
    
            (function frame() {
                confetti({
                    particleCount: 5,
                    angle: 60,
                    spread: 55,
                    origin: { x: 0 }
                });
                confetti({
                    particleCount: 5,
                    angle: 120,
                    spread: 55,
                    origin: { x: 1 }
                });
    
                if (Date.now() < end) {
                    requestAnimationFrame(frame);
                }
            }());
        });
    </script>
@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
         :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #8b5cf6;
            --accent: #a855f7;
            --light: #f8fafc;
            --dark: #1e293b;
            --gray: #94a3b8;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
        }

        

        .dashboard-card {
            background: white;
            border: none;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            border-left: 4px solid var(--primary);
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.15);
        }

        .dashboard-card h4 {
            color: var(--dark);
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .dashboard-card h4 i {
            color: var(--primary);
            font-size: 18px;
        }

        .dashboard-card .value {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 5px;
        }

        .dashboard-card .subtext {
            color: var(--gray);
            font-size: 13px;
        }

        .alert-card {
            /*background: linear-gradient(135deg, #fff9db, #ffec99);*/
            border-left: 4px solid #f59e0b;
        }

        .alert-card h4 {
            color: #d97706;
        }

        .alert-card h4 i {
            color: #d97706;
        }

        .user-activity-timeline {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border-left: 4px solid var(--secondary);
        }

        .user-activity-timeline h4 {
            color: var(--dark);
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-activity-timeline h4 i {
            color: var(--secondary);
        }

        .user-activity-timeline ul {
            list-style: none;
            padding: 0;
        }

        .user-activity-timeline li {
            padding: 12px 0;
            border-bottom: 1px dashed #e2e8f0;
            color: var(--dark);
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-activity-timeline li:last-child {
            border-bottom: none;
        }

        .user-activity-timeline li i {
            color: var(--accent);
            width: 20px;
            text-align: center;
        }

        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 12px;
            overflow: hidden;
        }

        .data-table th {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 12px;
            padding: 15px;
            text-align: left;
        }

        .data-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #f1f5f9;
            color: var(--dark);
            font-size: 14px;
        }

        .data-table tr:hover {
            background-color: #f8fafc;
        }

        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .status-leave, .status-wo {
            background-color: #fee2e2 !important;
            color: var(--danger) !important;
        }
        
        .status-cl {
            background-color: #dcfce7 !important;
            color: #166534 !important;
        }

        .custom-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: none;
        }

        .tab-button {
            padding: 12px 25px;
            cursor: pointer;
            color: var(--gray);
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.03);
        }

        .tab-button:hover {
            color: var(--primary);
            border-color: var(--primary);
            transform: translateY(-2px);
        }

        .tab-button.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            box-shadow: 0 4px 6px rgba(99, 102, 241, 0.2);
        }

        .tab-button.active i {
            color: white;
        }

        .tab-pane {
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .tab-pane.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-hours-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-success {
            background-color: #dcfce7;
            color: #166534;
        }

        .badge-warning {
            background-color: #fef9c3;
            color: #854d0e;
        }

        .filter-card {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .filter-card select {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 15px;
            background: white;
            color: var(--dark);
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .filter-card select:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--gray);
        }

        .empty-state i {
            font-size: 40px;
            margin-bottom: 15px;
            color: #e2e8f0;
        }

        .empty-state p {
            font-size: 16px;
        }
        
        /* Customization Panel Styles */
.customization-panel {
    position: fixed;
    top: 50%;
    right: -300px;
    transform: translateY(-50%);
    width: 300px;
    background: white;
    box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
    border-radius: 12px 0 0 12px;
    padding: 20px;
    z-index: 1000;
    transition: right 0.3s ease;
}

.customization-panel.open {
    right: 0;
}

.customization-toggle {
    position: absolute;
    left: -40px;
    top: 50%;
    transform: translateY(-50%);
    width: 40px;
    height: 40px;
    background: var(--primary);
    color: white;
    border-radius: 8px 0 0 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: -5px 0 10px rgba(0, 0, 0, 0.1);
}

.color-options {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
    margin-top: 15px;
}

.color-option {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    cursor: pointer;
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.color-option.selected {
    border-color: var(--dark);
    transform: scale(1.1);
}

.panel-title {
    font-weight: 600;
    margin-bottom: 15px;
    color: var(--dark);
    display: flex;
    align-items: center;
    gap: 8px;
}

.panel-title i {
    color: var(--primary);
}
    </style>
@endpush

@section('content')

<div class="px-4 py-0 py-lg-3 border-top-0 non-csa-dashboard">
    <div class="row">
        <!-- Profile + Filter -->
        <div class="col-md-6">
            <div class="dashboard-card d-flex align-items-center justify-content-between">
                <div>
                    <h4><i class="fas fa-user-circle"></i> User Profile</h4>
                    <div class="value">{{ user()->name }}</div>
                    <div class="subtext">{{ user()->email }}</div>
                </div>
                <div>
                    <img src="{{ auth()->user()->image_url ?? asset('default-user.png') }}"
                         alt="User Image"
                         style="width: 80px; height: 80px; object-fit: cover; border-radius: 12px; margin-left: 20px; box-shadow: 0 0 8px rgba(0,0,0,0.15);">
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="dashboard-card">
                <h4><i class="fas fa-filter"></i> Filter by Month and Year</h4>
                <form action="{{ route('non.csa.dash') }}" method="GET" class="filter-card">
                    <select name="month" class="form-control" onchange="this.form.submit()">
                        @foreach ($months as $month)
                            <option value="{{ $month }}" {{ \Carbon\Carbon::parse($startDate)->month === $month ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($month)->format('F') }}
                            </option>
                        @endforeach
                    </select>
                    <select name="year" class="form-control" onchange="this.form.submit()">
                        @foreach ($years as $yearOption)
                            <option value="{{ $yearOption }}" {{ \Carbon\Carbon::parse($startDate)->year === $yearOption ? 'selected' : '' }}>
                                {{ $yearOption }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="col-md-6">
            <div class="dashboard-card">
                <h4><i class="fas fa-calendar-check"></i> Total Present Days</h4>
                <div class="value">{{ number_format($totalPresentDays, 2) }}</div>
                <div class="subtext">Current month attendance</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="dashboard-card alert-card">
                <h4><i class="fas fa-exclamation-triangle"></i> Short Login Alert</h4>
                <div class="value">{{ $shortLoginDays }} day{{ $shortLoginDays > 1 ? 's' : '' }}</div>
                <div class="subtext">
                    Max Short Login Days Allowed: 3. After every 3 short login days, a Half Day will be marked.
                </div>
            </div>
        </div>
        
        <!--<div class="col-md-6">-->
        <!--    <div class="dashboard-card">-->
        <!--        <h4><i class="fas fa-clock"></i> Target Login Hours</h4>-->
        <!--        <div class="value">{{ number_format(234, 2) }}</div>-->
        <!--        <div class="subtext">For whole month</div>-->
        <!--    </div>-->
        <!--</div> -->
        <div class="col-md-6">
            <div class="dashboard-card">
                <h4><i class="fas fa-clock"></i> Total Login Hours</h4>
                <div class="value">{{ number_format($totalLoginHours, 2) }}</div>
                <div class="subtext">Accumulated work hours</div>
            </div>
        </div> 

        <!-- Alert Card -->
        

        <!-- Tabs -->
        <div class="col-md-12 mt-4">
            <div class="dashboard-card">
                <div class="custom-tabs">
                    <button class="tab-button active" onclick="openTab('non-csa-tab')">
                        <i class="fas fa-calendar-alt"></i> Non-CSA Attendance
                    </button>
                    <button class="tab-button" onclick="openTab('biometric-tab')">
                        <i class="fas fa-fingerprint"></i> Biometric
                    </button>
                </div>

                <div class="tab-content">
                    <div class="tab-pane active" id="non-csa-tab">
                        <h4><i class="fas fa-table"></i> Non-CSA Attendance Data ({{ $currentMonthYear }})</h4>
                        @if (empty($filteredData))
                            <div class="empty-state">
                                <i class="fas fa-database"></i>
                                <p>No data available for the selected month.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Department</th>
                                            <th>Name</th>
                                            <th>Supervisor</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>In Time</th>
                                            <th>Out Time</th>
                                            <th>Login Hours</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($filteredData as $attendance)
                                            <tr class="{{ in_array(strtolower($attendance['attendance_status'] ?? ''), ['leave', 'lp', 'cl']) ? 'status-cl' : (strtolower($attendance['attendance_status'] ?? '') === 'wo' ? 'status-wo' : '') }}">
                                                <td>{{ $attendance['department'] ?? '--' }}</td>
                                                <td>{{ $attendance['name'] ?? '--' }}</td>
                                                <td>{{ $attendance['supervisor_name'] ?? '--' }}</td>
                                                <td>{{ $attendance['date'] ?? '--' }}</td>
                                                <td>{{ $attendance['attendance_status'] ?? '--' }}</td>
                                                <td>{{ $attendance['in_time'] ?? '--' }}</td>
                                                <td>{{ $attendance['out_time'] ?? '--' }}</td>
                                                <td>
                                                    @if(isset($attendance['login_hours']))
                                                        <span class="login-hours-badge {{ $attendance['login_hours'] >= 9 ? 'badge-success' : 'badge-warning' }}">
                                                            {{ number_format($attendance['login_hours'], 2) }}
                                                            @if($attendance['login_hours'] < 9)
                                                                <i class="fas fa-exclamation-circle"></i>
                                                            @endif
                                                        </span>
                                                    @else
                                                        --
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <div class="tab-pane" id="biometric-tab">
                        <h4><i class="fas fa-table"></i> Biometric Data ({{ $currentMonthYear }})</h4>
                        @if (empty($biometricData))
                            <div class="empty-state">
                                <i class="fas fa-database"></i>
                                <p>No biometric data available for the selected month.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Clock In</th>
                                            <th>Clock Out</th>
                                            <th>Login Hour</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($biometricData as $record)
                                            <tr>
                                                <td>{{ $record['date'] ?? '--' }}</td>
                                                <td>{{ $record['clock_in_time'] ?? '--' }}</td>
                                                <td>{{ $record['clock_out_time'] ?? '--' }}</td>
                                                <td>
                                                    @if(isset($record['login_hour']))
                                                        <span class="login-hours-badge {{ $record['login_hour'] >= 9 ? 'badge-success' : 'badge-warning' }}">
                                                            {{ number_format($record['login_hour'], 2) }}
                                                            @if($record['login_hour'] < 9 && $record['login_hour'] > 0)
                                                                <i class="fas fa-exclamation-circle"></i>
                                                            @endif
                                                        </span>
                                                    @else
                                                        --
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline -->
        <div class="col-md-12 mt-4">
            <div class="user-activity-timeline">
                <h4><i class="fas fa-history"></i> Recent Activity Timeline</h4>
                <ul>
                    <li>
                        <i class="fas fa-sign-in-alt"></i>
                        <span>{{ user()->name }} logged in at {{ now()->format('h:i a') }}</span>
                    </li>
                    @foreach ($filteredData as $attendance)
                        @if (isset($attendance['attendance_status']))
                            <li>
                                <i class="fas fa-{{ strtolower($attendance['attendance_status']) === 'present' ? 'check-circle' : 'calendar-minus' }}"></i>
                                <span>
                                    {{ $attendance['attendance_status'] }} on {{ $attendance['date'] }} • 
                                    {{ $attendance['in_time'] ? $attendance['in_time'] . ' to ' . $attendance['out_time'] : 'No login data' }}
                                </span>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        function openTab(tabId) {
            document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));
            document.querySelectorAll('.tab-button').forEach(button => button.classList.remove('active'));

            document.getElementById(tabId).classList.add('active');
            document.querySelector(`.tab-button[onclick="openTab('${tabId}')"]`).classList.add('active');
        }

        document.addEventListener('DOMContentLoaded', () => { 
            openTab('non-csa-tab');
        });
        
        // Color customization functionality
document.addEventListener('DOMContentLoaded', () => {
    
    
    // Color presets
    const colorPresets = [
        { name: 'Indigo', primary: '#6366f1', primaryDark: '#4f46e5', secondary: '#8b5cf6' },
        { name: 'Emerald', primary: '#10b981', primaryDark: '#059669', secondary: '#34d399' },
        { name: 'Rose', primary: '#f43f5e', primaryDark: '#e11d48', secondary: '#fb7185' },
        { name: 'Amber', primary: '#f59e0b', primaryDark: '#d97706', secondary: '#fbbf24' },
        { name: 'Violet', primary: '#8b5cf6', primaryDark: '#7c3aed', secondary: '#a78bfa' },
        { name: 'Sky', primary: '#0ea5e9', primaryDark: '#0284c7', secondary: '#38bdf8' },
        { name: 'Fuchsia', primary: '#d946ef', primaryDark: '#c026d3', secondary: '#e879f9' },
        { name: 'Teal', primary: '#14b8a6', primaryDark: '#0d9488', secondary: '#2dd4bf' }
    ];

    // Create color options
    const colorOptionsContainer = document.createElement('div');
    colorOptionsContainer.className = 'color-options';
    
    colorPresets.forEach(preset => {
        const colorOption = document.createElement('div');
        colorOption.className = 'color-option';
        colorOption.style.backgroundColor = preset.primary;
        colorOption.dataset.primary = preset.primary;
        colorOption.dataset.primaryDark = preset.primaryDark;
        colorOption.dataset.secondary = preset.secondary;
        colorOptionsContainer.appendChild(colorOption);
    });

    // Create panel
    const panel = document.createElement('div');
    panel.className = 'customization-panel';
    
    const toggleBtn = document.createElement('div');
    toggleBtn.className = 'customization-toggle';
    toggleBtn.innerHTML = '<i class="fas fa-palette"></i>';
    
    const panelTitle = document.createElement('div');
    panelTitle.className = 'panel-title';
    panelTitle.innerHTML = '<i class="fas fa-paint-brush"></i> Theme Customization';
    
    panel.appendChild(panelTitle);
    panel.appendChild(colorOptionsContainer);
    panel.appendChild(toggleBtn);
    document.body.appendChild(panel);

    // Toggle panel
    toggleBtn.addEventListener('click', () => {
        panel.classList.toggle('open');
    });

    // Apply color scheme
    document.querySelectorAll('.color-option').forEach(option => {
        option.addEventListener('click', function() {
            // Remove selected class from all options
            document.querySelectorAll('.color-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            
            // Add selected class to clicked option
            this.classList.add('selected');
            
            // Get color values
            const primary = this.dataset.primary;
            const primaryDark = this.dataset.primaryDark;
            const secondary = this.dataset.secondary;
            
            // Update CSS variables
            document.documentElement.style.setProperty('--primary', primary);
            document.documentElement.style.setProperty('--primary-dark', primaryDark);
            document.documentElement.style.setProperty('--secondary', secondary);
            
            // Update table header gradient
            const styleElement = document.createElement('style');
            styleElement.id = 'dynamic-gradient';
            styleElement.innerHTML = `
                .data-table th {
                    background: linear-gradient(135deg, ${primary}, ${secondary}) !important;
                }
                .customization-toggle, .tab-button.active {
                    background: ${primary} !important;
                }
            `;
            
            // Remove old style if exists
            const oldStyle = document.getElementById('dynamic-gradient');
            if (oldStyle) {
                oldStyle.remove();
            }
            
            document.head.appendChild(styleElement);
            
            // Save to localStorage
            localStorage.setItem('dashboardTheme', JSON.stringify({
                primary,
                primaryDark,
                secondary
            }));
        });
    });

    // Load saved theme
    const savedTheme = localStorage.getItem('dashboardTheme');
    if (savedTheme) {
        const theme = JSON.parse(savedTheme);
        document.documentElement.style.setProperty('--primary', theme.primary);
        document.documentElement.style.setProperty('--primary-dark', theme.primaryDark);
        document.documentElement.style.setProperty('--secondary', theme.secondary);
        
        // Find and select the matching color option
        document.querySelectorAll('.color-option').forEach(option => {
            if (option.dataset.primary === theme.primary) {
                option.classList.add('selected');
                
                // Add dynamic gradient style
                const styleElement = document.createElement('style');
                styleElement.id = 'dynamic-gradient';
                styleElement.innerHTML = `
                    .data-table th {
                        background: linear-gradient(135deg, ${theme.primary}, ${theme.secondary}) !important;
                    }
                    .customization-toggle, .tab-button.active {
                        background: ${theme.primary} !important;
                    }
                `;
                document.head.appendChild(styleElement);
            }
        });
    }
});
    </script>
    
@endpush