@extends('layouts.dashboard')

@section('header')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<div class="container-fluid py-4 px-4">
    <div class="row g-4">
        <!-- Summary Stats -->
        <div class="col-12">
            <div class="card border rounded-3">
                <div class="card-body p-4">
                    <div class="row">
                        <!-- Financial Stats -->
                                               <!-- Summary Stats -->
                                               <div class="col-md-8">
                                                <h4 class="mb-4">Ringkasan Keuangan</h4>
                                                <div class="row g-4">
                                                    <div class="col-md-4">
                                                        <div class="finance-stat">
                                                            <div class="stat-icon bg-success-subtle">
                                                                <i class="bi bi-graph-up-arrow"></i>
                                                            </div>
                                                            <div class="stat-content">
                                                                <span class="stat-label">Total Pemasukan</span>
                                                                <h4 class="stat-value text-success">Rp <span id="totalPemasukan">0</span></h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="finance-stat">
                                                            <div class="stat-icon bg-danger-subtle">
                                                                <i class="bi bi-graph-down-arrow"></i>
                                                            </div>
                                                            <div class="stat-content">
                                                                <span class="stat-label">Total Pengeluaran</span>
                                                                <h4 class="stat-value text-danger">Rp <span id="totalPengeluaran">0</span></h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="finance-stat">
                                                            <div class="stat-icon bg-primary-subtle">
                                                                <i class="bi bi-wallet2"></i>
                                                            </div>
                                                            <div class="stat-content">
                                                                <span class="stat-label">Laba Bersih</span>
                                                                <h4 class="stat-value text-primary">Rp <span id="totalLaba">0</span></h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                        <!-- Filter Section -->
                        <div class="col-md-4">
                            <div class="filter-card bg-light rounded-4 p-4">
                                <h5 class="fw-bold mb-4">Filter Laporan</h5>
                                <form action="{{ route('laporan.cetak') }}" method="POST" target="_blank">
                                    @csrf
                                    <div class="mb-3">
                                        <select name="tahun" class="form-select form-select-lg rounded-3" required id="tahun">
                                            <option value="">Pilih Tahun</option>
                                            @foreach($availableYears as $year)
                                                <option value="{{ $year }}">{{ $year }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-4">
                                        <select name="bulan" class="form-select form-select-lg rounded-3" id="bulan">
                                            <option value="">Semua Bulan</option>
                                            @foreach($availableMonths as $key => $month)
                                                <option value="{{ $key }}">{{ $month }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-lg w-100 rounded-3">
                                        <i class="bi bi-printer me-2"></i>Cetak Laporan
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Grafik Keuangan</h5>
                    <div class="chart-container">
                        <canvas id="financeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Proporsi Keuangan</h5>
                    <div class="chart-container">
                        <canvas id="pieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let financeChart, pieChart;

function updateCharts(data) {
    // Update summary stats
    document.getElementById('totalPemasukan').textContent =
        new Intl.NumberFormat('id-ID').format(data.summary.total_pemasukan);
    document.getElementById('totalPengeluaran').textContent =
        new Intl.NumberFormat('id-ID').format(data.summary.total_pengeluaran);
    document.getElementById('totalLaba').textContent =
        new Intl.NumberFormat('id-ID').format(data.summary.total_laba);

    // Update line chart
    const ctx = document.getElementById('financeChart').getContext('2d');
    if (financeChart) {
        financeChart.destroy();
    }

    financeChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.chart.labels,
            datasets: [{
                label: 'Pemasukan',
                data: data.chart.pemasukan,
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                fill: true
            }, {
                label: 'Pengeluaran',
                data: data.chart.pengeluaran,
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => 'Rp ' + new Intl.NumberFormat('id-ID').format(value)
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': Rp ' +
                                new Intl.NumberFormat('id-ID').format(context.parsed.y);
                        }
                    }
                }
            }
        }
    });

    // Update pie chart
    const ctxPie = document.getElementById('pieChart').getContext('2d');
    if (pieChart) {
        pieChart.destroy();
    }

    pieChart = new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: ['Pemasukan', 'Pengeluaran'],
            datasets: [{
                data: [data.summary.total_pemasukan, data.summary.total_pengeluaran],
                backgroundColor: ['#28a745', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = context.raw;
                            const percentage = ((value / (data.summary.total_pemasukan + data.summary.total_pengeluaran)) * 100).toFixed(1);
                            return `${context.label}: Rp ${new Intl.NumberFormat('id-ID').format(value)} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

function fetchData() {
    const tahun = document.getElementById('tahun').value;
    const bulan = document.getElementById('bulan').value;

    if (!tahun) {
        alert('Silakan pilih tahun terlebih dahulu');
        return;
    }

    fetch(`/laporan/data?tahun=${tahun}&bulan=${bulan}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.warn('Warning:', data.error);
                return;
            }
            updateCharts(data);
        })
        .catch(error => console.error('Error:', error));
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('tahun').addEventListener('change', fetchData);
    document.getElementById('bulan').addEventListener('change', fetchData);

    // Initialize with current year/month
    document.getElementById('tahun').value = new Date().getFullYear();
    fetchData();
});
</script>
@endpush

<style>

.finance-stat {
    padding: 1.5rem;
    border-radius: 0.5rem;
    background: white;
    box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
}
.stat-card {
    transition: all 0.2s ease;
    background: white;
}


.stat-icon i {
    opacity: 0.7;
}

.filter-card {
    border: 1px solid rgba(0,0,0,0.1);
}

.chart-container {
    position: relative;
    height: 350px;
    width: 100%;
}

.form-select {
    border: 1px solid rgba(0,0,0,0.1);
    padding: 0.75rem 1rem;
}

.form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.btn-lg {
    padding: 0.75rem 1.5rem;
}
</style>
@endsection
