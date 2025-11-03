<div class="mt-3">
    <div class="card">
        <div class="card-header d-flex">
            <div class="flex-fill">
                <h3>Hay, {{ $auth->name }}</h3>
                <p class="mb-0">Selamat datang di dasbor cashflow Anda.</p>
            </div>
            <div>
                <a href="{{ route('auth.logout') }}" class="btn btn-warning">Keluar</a>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h5 class="mb-0">Statistik Ringkas</h5>
            <small>Berdasarkan filter tanggal yang Anda pilih.</small>
        </div>
        <div class="card-body">
            <div class="row text-center mb-3">
                <div class="col-md-4">
                    <div class="card bg-success-subtle border-success">
                        <div class="card-body">
                            <h6 class="text-success">Total Pemasukan</h6>
                            <h4 class="text-success mb-0">Rp {{ number_format($totalIncome, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-danger-subtle border-danger">
                        <div class="card-body">
                            <h6 class="text-danger">Total Pengeluaran</h6>
                            <h4 class="text-danger mb-0">Rp {{ number_format($totalExpense, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-primary-subtle border-primary">
                        <div class="card-body">
                            <h6 class="text-primary">Saldo Akhir</h6>
                            <h4 class="text-primary mb-0">Rp {{ number_format($balance, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div id="cashflowChart" style="min-height: 350px;">
                <div wire:loading.flex class="justify-content-center align-items-center h-100">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading chart...</span>
                    </div>
                </div>
            </div>

            @push('scripts')
            <!-- ApexCharts JS -->
            <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
            <script>
                let chart = null;

                document.addEventListener('livewire:initialized', function() {
                    initializeChart();

                    Livewire.on('updateChart', () => {
                        if (chart) {
                            chart.destroy();
                        }
                        initializeChart();
                    });
                });

                function initializeChart() {
                    // Clear existing chart if any
                    if (chart) {
                        chart.destroy();
                    }

                    const chartDiv = document.querySelector("#cashflowChart");
                    if (!chartDiv) return;
                    @this.getMonthlyCashflowStats().then(function(data) {
                        const months = data.map(item => item.month);
                        const incomes = data.map(item => item.income);
                        const expenses = data.map(item => item.expense);
                        const balances = data.map(item => item.balance);

                        const saldoLastIndex = balances.length - 1;
                        const options = {
                            series: [
                                {
                                    name: 'Pemasukan',
                                    type: 'line',
                                    data: incomes
                                },
                                {
                                    name: 'Pengeluaran',
                                    type: 'line',
                                    data: expenses
                                },
                                {
                                    name: 'Saldo',
                                    type: 'line',
                                    data: balances
                                }
                            ],
                            chart: {
                                height: 350,
                                type: 'line',
                                toolbar: {
                                    show: true,
                                    tools: {
                                        download: true,
                                        selection: false,
                                        zoom: false,
                                        zoomin: false,
                                        zoomout: false,
                                        pan: false,
                                    }
                                }
                            },
                            dataLabels: {
                                enabled: false
                            },
                            stroke: {
                                width: [2, 2, 3],
                                curve: 'smooth'
                            },
                            markers: {
                                size: [6, 6, 0], // income & expense: all points, saldo: no marker except discrete
                                shape: 'circle',
                                strokeWidth: 2,
                                hover: {
                                    size: 8
                                },
                                discrete: [
                                    {
                                        seriesIndex: 2, // saldo
                                        dataPointIndex: saldoLastIndex,
                                        fillColor: '#17a2b8',
                                        strokeColor: '#17a2b8',
                                        size: 8,
                                        shape: 'circle'
                                    }
                                ]
                            },
                            title: {
                                text: 'Statistik Keuangan Per Bulan',
                                align: 'center'
                            },
                            xaxis: {
                                categories: months,
                            },
                            yaxis: [{
                                axisTicks: {
                                    show: true,
                                },
                                axisBorder: {
                                    show: true,
                                    color: '#008FFB'
                                },
                                labels: {
                                    style: {
                                        colors: '#008FFB',
                                    },
                                    formatter: function (value) {
                                        return value.toLocaleString('id-ID', { 
                                            style: 'currency', 
                                            currency: 'IDR',
                                            minimumFractionDigits: 0,
                                            maximumFractionDigits: 0
                                        });
                                    }
                                },
                                title: {
                                    text: "Jumlah (Rp)",
                                    style: {
                                        color: '#008FFB',
                                    }
                                },
                                tooltip: {
                                    enabled: true
                                }
                            }],
                            tooltip: {
                                y: {
                                    formatter: function (value) {
                                        return value.toLocaleString('id-ID', { 
                                            style: 'currency', 
                                            currency: 'IDR',
                                            minimumFractionDigits: 0,
                                            maximumFractionDigits: 0
                                        });
                                    }
                                }
                            },
                            legend: {
                                horizontalAlign: 'center',
                            },
                            colors: ['#28a745', '#dc3545', '#17a2b8'],
                        };

                        chart = new ApexCharts(document.querySelector("#cashflowChart"), options);
                        chart.render();

                        // Update chart when Livewire updates
                        document.addEventListener('livewire:update', function() {
                            chart.updateOptions({
                                series: [{
                                    name: 'Pemasukan',
                                    type: 'column',
                                    data: incomes
                                }, {
                                    name: 'Pengeluaran',
                                    type: 'column',
                                    data: expenses
                                }, {
                                    name: 'Saldo',
                                    type: 'line',
                                    data: balances
                                }]
                            });
                        });
                    });
                }
            </script>
            @endpush
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <div class="d-flex mb-3">
                <div class="flex-fill">
                    <h3>Riwayat Cashflow</h3>
                </div>
                <div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCashflowModal">
                        <i class="fas fa-plus"></i> Tambah Catatan
                    </button>
                </div>
            </div>

            <div class="card bg-light mb-3">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <label class="form-label">Cari Keterangan</label>
                            <input type="text" class="form-control" placeholder="Cari..." 
                                   wire:model.debounce.300ms="search">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Jenis</label>
                            <select class="form-select" wire:model.live="filterType">
                                <option value="">Semua Jenis</option>
                                <option value="income">Pemasukan</option>
                                <option value="expense">Pengeluaran</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Dari Tanggal</label>
                            <input type="date" class="form-control" wire:model.live="filterDateStart">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Sampai Tanggal</label>
                            <input type="date" class="form-control" wire:model.live="filterDateEnd">
                        </div>
                    </div>
                </div>
            </div>

            <div wire:loading.flex class="justify-content-center my-2">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            
            <div class="table-responsive" wire:loading.remove>
                <table class="table table-striped table-hover">
                    
                    <thead>
                        <tr class="table-light">
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Jumlah</th>
                            <th>Keterangan</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @forelse ($cashflows as $key => $cashflow)
                            <tr>
                                <td>{{ $cashflows->firstItem() + $key }}</td>
                                <td>{{ $cashflow->date->format('d M Y') }}</td>
                                <td>
                                    @if ($cashflow->type == 'income')
                                        <span class="badge bg-success-subtle text-success-emphasis rounded-pill">Pemasukan</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger-emphasis rounded-pill">Pengeluaran</span>
                                    @endif
                                </td>
                                <td class="{{ $cashflow->type == 'income' ? 'text-success' : 'text-danger' }} fw-bold">
                                    {{ $cashflow->type == 'income' ? '+' : '-' }}
                                    Rp {{ number_format($cashflow->amount, 0, ',', '.') }}
                                </td>
                                <td>{{ Str::limit($cashflow->description, 35) }}</td>
                                <td>
                                    <a href="{{ route('app.cashflows.detail', ['cashflow_id' => $cashflow->id]) }}"
                                        class="btn btn-sm btn-info" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button wire:click="prepareEditCashflow({{ $cashflow->id }})" 
                                            class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button wire:click="prepareDeleteCashflow({{ $cashflow->id }})" 
                                            class="btn btn-sm btn-danger" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    <div class="my-3">
                                        <i class="fas fa-folder-open fa-3x text-muted"></i>
                                        <p class="mt-2 text-muted">Belum ada data cashflow yang tersedia.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $cashflows->links() }}
            </div>
            
        </div>
    </div>

    {{-- [MODIFIED] Ganti path include modal dari 'todos' ke 'flowcharts' --}}
    @include('components.modals.cashflows.add')
    @include('components.modals.cashflows.edit')
    @include('components.modals.cashflows.delete')
</div>