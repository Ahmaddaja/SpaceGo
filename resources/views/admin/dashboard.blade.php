@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
@endpush

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
        <p>halo, Selamat datang, <strong>{{ Auth::user()->name }}</strong>!</p>
    </x-slot>

    <!-- Main Content -->
    <div class="dashboard-main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="page-header">
            </div>
            <div class="top-actions">
                <select class="select-dropdown">
                    <option>Hari Ini</option>
                    <option>Minggu Ini</option>
                    <option>Bulan Ini</option>
                    <option>Tahun Ini</option>
                </select>
                <button class="btn-primary">
                    <i class="fas fa-download"></i>
                    Export Report
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-label">Total Revenue</div>
                    <div class="stat-icon green">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
                <div class="stat-value">Rp 48.392.000</div>
                <div class="stat-change">+12.5%</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-label">Active Bookings</div>
                    <div class="stat-icon blue">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
                <div class="stat-value">156</div>
                <div class="stat-change">+8.2%</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-label">Total Properties</div>
                    <div class="stat-icon purple">
                        <i class="fas fa-building"></i>
                    </div>
                </div>
                <div class="stat-value">89</div>
                <div class="stat-change">+3</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-label">New Users</div>
                    <div class="stat-icon orange">
                        <i class="fas fa-user-plus"></i>
                    </div>
                </div>
                <div class="stat-value">234</div>
                <div class="stat-change">+18.7%</div>
            </div>
        </div>

        <!-- Recent Bookings & Top Properties -->
        <div class="content-row">
            <div class="section-card">
                <div class="section-header">
                    <h2 class="section-title">Recent Bookings</h2>
                    <button class="btn-secondary">
                        <i class="fas fa-eye"></i>
                        View All
                    </button>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Customer</th>
                                <th>Property</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>BK-2401</td>
                                <td>
                                    <div class="customer-info">
                                        <div class="customer-name">Acme Corporation</div>
                                        <div class="customer-date">2024-01-15</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="property-info">
                                        <div class="property-name">Warehouse A-12</div>
                                        <div class="property-size">5000 sq ft</div>
                                    </div>
                                </td>
                                <td>Rp 12.500.000</td>
                                <td><span class="status-badge active">active</span></td>
                                <td><span class="action-dots">⋮</span></td>
                            </tr>
                            <tr>
                                <td>BK-2402</td>
                                <td>
                                    <div class="customer-info">
                                        <div class="customer-name">Tech Solutions Inc</div>
                                        <div class="customer-date">2024-01-16</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="property-info">
                                        <div class="property-name">Storage Unit B-08</div>
                                        <div class="property-size">2000 sq ft</div>
                                    </div>
                                </td>
                                <td>Rp 4.200.000</td>
                                <td><span class="status-badge pending">pending</span></td>
                                <td><span class="action-dots">⋮</span></td>
                            </tr>
                            <tr>
                                <td>BK-2403</td>
                                <td>
                                    <div class="customer-info">
                                        <div class="customer-name">Global Logistics</div>
                                        <div class="customer-date">2024-01-16</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="property-info">
                                        <div class="property-name">Warehouse C-05</div>
                                        <div class="property-size">8000 sq ft</div>
                                    </div>
                                </td>
                                <td>Rp 28.800.000</td>
                                <td><span class="status-badge active">active</span></td>
                                <td><span class="action-dots">⋮</span></td>
                            </tr>
                            <tr>
                                <td>BK-2404</td>
                                <td>
                                    <div class="customer-info">
                                        <div class="customer-name">Retail Plus</div>
                                        <div class="customer-date">2024-01-14</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="property-info">
                                        <div class="property-name">Storage Unit A-15</div>
                                        <div class="property-size">1500 sq ft</div>
                                    </div>
                                </td>
                                <td>Rp 1.800.000</td>
                                <td><span class="status-badge completed">completed</span></td>
                                <td><span class="action-dots">⋮</span></td>
                            </tr>
                            <tr>
                                <td>BK-2405</td>
                                <td>
                                    <div class="customer-info">
                                        <div class="customer-name">Manufacturing Co</div>
                                        <div class="customer-date">2024-01-13</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="property-info">
                                        <div class="property-name">Warehouse D-03</div>
                                        <div class="property-size">10000 sq ft</div>
                                    </div>
                                </td>
                                <td>Rp 72.000.000</td>
                                <td><span class="status-badge active">active</span></td>
                                <td><span class="action-dots">⋮</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="section-card">
                <div class="section-header">
                    <h2 class="section-title">Top Properties</h2>
                    <button class="btn-secondary">
                        <i class="fas fa-sliders-h"></i>
                        Manage
                    </button>
                </div>

                <div class="property-list">
                    <div class="property-card">
                        <div class="property-header">
                            <div>
                                <div class="property-title">Warehouse A-12</div>
                                <div class="property-location">Downtown District</div>
                            </div>
                            <div class="property-rating">
                                <i class="fas fa-star"></i>
                                <span>4.8</span>
                            </div>
                        </div>
                        <div class="property-stats">
                            <div class="property-stat">
                                <span class="property-stat-label">Occupancy</span>
                                <span class="property-stat-value">95%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 95%"></div>
                            </div>
                            <div class="property-stat">
                                <span class="property-stat-label">Revenue</span>
                                <span class="property-stat-value" style="color: #10b981;">Rp 45.200.000</span>
                            </div>
                        </div>
                    </div>

                    <div class="property-card">
                        <div class="property-header">
                            <div>
                                <div class="property-title">Storage Complex B</div>
                                <div class="property-location">Industrial Zone</div>
                            </div>
                            <div class="property-rating">
                                <i class="fas fa-star"></i>
                                <span>4.6</span>
                            </div>
                        </div>
                        <div class="property-stats">
                            <div class="property-stat">
                                <span class="property-stat-label">Occupancy</span>
                                <span class="property-stat-value">88%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 88%"></div>
                            </div>
                            <div class="property-stat">
                                <span class="property-stat-label">Revenue</span>
                                <span class="property-stat-value" style="color: #10b981;">Rp 38.900.000</span>
                            </div>
                        </div>
                    </div>

                    <div class="property-card">
                        <div class="property-header">
                            <div>
                                <div class="property-title">Warehouse C-05</div>
                                <div class="property-location">Port Area</div>
                            </div>
                            <div class="property-rating">
                                <i class="fas fa-star"></i>
                                <span>4.9</span>
                            </div>
                        </div>
                        <div class="property-stats">
                            <div class="property-stat">
                                <span class="property-stat-label">Occupancy</span>
                                <span class="property-stat-value">92%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 92%"></div>
                            </div>
                            <div class="property-stat">
                                <span class="property-stat-label">Revenue</span>
                                <span class="property-stat-value" style="color: #10b981;">Rp 42.100.000</span>
                            </div>
                        </div>
                    </div>

                    <div class="property-card">
                        <div class="property-header">
                            <div>
                                <div class="property-title">Mini Storage D</div>
                                <div class="property-location">Suburban Area</div>
                            </div>
                            <div class="property-rating">
                                <i class="fas fa-star"></i>
                                <span>4.5</span>
                            </div>
                        </div>
                        <div class="property-stats">
                            <div class="property-stat">
                                <span class="property-stat-label">Occupancy</span>
                                <span class="property-stat-value">78%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 78%"></div>
                            </div>
                            <div class="property-stat">
                                <span class="property-stat-label">Revenue</span>
                                <span class="property-stat-value" style="color: #10b981;">Rp 28.500.000</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Users -->
        <div class="section-card">
            <div class="section-header">
                <h2 class="section-title">Recent Users</h2>
                <button class="btn-secondary">
                    <i class="fas fa-users"></i>
                    View All Users
                </button>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Company</th>
                            <th>Joined</th>
                            <th>Bookings</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center;">
                                    <div class="user-avatar">SM</div>
                                    <span class="user-name">Sarah Mitchell</span>
                                </div>
                            </td>
                            <td class="user-email">sarah.m@acme.com</td>
                            <td class="user-company">Acme Corporation</td>
                            <td class="user-date">2024-01-15</td>
                            <td class="user-bookings">3</td>
                            <td><span class="status-badge verified">verified</span></td>
                            <td>
                                <div class="user-actions">
                                    <button class="icon-btn"><i class="fas fa-edit"></i></button>
                                    <button class="icon-btn"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center;">
                                    <div class="user-avatar">DC</div>
                                    <span class="user-name">David Chen</span>
                                </div>
                            </td>
                            <td class="user-email">david@techsol.com</td>
                            <td class="user-company">Tech Solutions Inc</td>
                            <td class="user-date">2024-01-16</td>
                            <td class="user-bookings">1</td>
                            <td><span class="status-badge pending">pending</span></td>
                            <td>
                                <div class="user-actions">
                                    <button class="icon-btn"><i class="fas fa-edit"></i></button>
                                    <button class="icon-btn"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center;">
                                    <div class="user-avatar">ER</div>
                                    <span class="user-name">Emma Rodriguez</span>
                                </div>
                            </td>
                            <td class="user-email">emma@globallog.com</td>
                            <td class="user-company">Global Logistics</td>
                            <td class="user-date">2024-01-14</td>
                            <td class="user-bookings">5</td>
                            <td><span class="status-badge verified">verified</span></td>
                            <td>
                                <div class="user-actions">
                                    <button class="icon-btn"><i class="fas fa-edit"></i></button>
                                    <button class="icon-btn"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center;">
                                    <div class="user-avatar">JW</div>
                                    <span class="user-name">James Wilson</span>
                                </div>
                            </td>
                            <td class="user-email">james@retail.com</td>
                            <td class="user-company">Retail Plus</td>
                            <td class="user-date">2024-01-13</td>
                            <td class="user-bookings">2</td>
                            <td><span class="status-badge verified">verified</span></td>
                            <td>
                                <div class="user-actions">
                                    <button class="icon-btn"><i class="fas fa-edit"></i></button>
                                    <button class="icon-btn"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="section-card" style="margin-top: 32px;">
            <h2 class="section-title" style="margin-bottom: 16px;">Quick Actions</h2>
            <div class="quick-actions">
                <button class="action-btn primary">
                    <i class="fas fa-plus"></i>
                    Add New Property
                </button>
                <button class="action-btn">
                    <i class="fas fa-calendar-plus"></i>
                    Create Booking
                </button>
                <button class="action-btn">
                    <i class="fas fa-file-alt"></i>
                    Generate Report
                </button>
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')
<script>
    // Animate progress bars on load
    window.addEventListener('load', () => {
        document.querySelectorAll('.progress-fill').forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0';
            setTimeout(() => {
                bar.style.width = width;
            }, 100);
        });
    });

    // Add hover effect to property cards
    document.querySelectorAll('.property-card').forEach(card => {
        card.addEventListener('click', () => {
            console.log('Property card clicked');
        });
    });

    // Quick action buttons
    document.querySelectorAll('.action-btn, .btn-primary, .btn-secondary').forEach(btn => {
        btn.addEventListener('click', function() {
            console.log('Button clicked:', this.textContent.trim());
        });
    });
</script>
@endpush