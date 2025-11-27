@extends('layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-speedometer2"></i> Dashboard</h2>
    <div>
        <input type="month" id="filterMonth" class="form-control" value="{{ date('Y-m') }}" style="max-width: 200px; display: inline-block;">
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted d-block mb-2">Patrimônio Total</small>
                    <h3 id="totalAssets" class="mb-0" style="color: var(--accent-primary);">R$ 0,00</h3>
                </div>
                <div>
                    <i class="bi bi-wallet2" style="font-size: 3rem; opacity: 0.3; color: var(--accent-primary);"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted d-block mb-2">Total Gasto (Mês)</small>
                    <h3 id="totalExpenses" class="mb-0" style="color: var(--danger);">R$ 0,00</h3>
                </div>
                <div>
                    <i class="bi bi-arrow-down-circle" style="font-size: 3rem; opacity: 0.3; color: var(--danger);"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted d-block mb-2">Total Recebido (Mês)</small>
                    <h3 id="totalIncome" class="mb-0" style="color: var(--success);">R$ 0,00</h3>
                </div>
                <div>
                    <i class="bi bi-arrow-up-circle" style="font-size: 3rem; opacity: 0.3; color: var(--success);"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted d-block mb-2">Saldo do Mês</small>
                    <h3 id="monthBalance" class="mb-0" style="color: var(--accent-secondary);">R$ 0,00</h3>
                </div>
                <div>
                    <i class="bi bi-graph-up" style="font-size: 3rem; opacity: 0.3; color: var(--accent-secondary);"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card">
            <h5 class="mb-3"><i class="bi bi-pie-chart"></i> Gastos por Categoria</h5>
            <div style="position: relative; height: 300px;">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <h5 class="mb-3"><i class="bi bi-graph-up-arrow"></i> Evolução Mensal</h5>
            <div style="position: relative; height: 300px;">
                <canvas id="evolutionChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-12">
        <div class="card">
            <h5 class="mb-3"><i class="bi bi-clock-history"></i> Últimas Transações</h5>
            <div id="lastTransactions" class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Descrição</th>
                            <th>Conta</th>
                            <th>Categoria</th>
                            <th>Tipo</th>
                            <th class="text-end">Valor</th>
                        </tr>
                    </thead>
                    <tbody id="transactionsTable">
                        <tr><td colspan="6" class="text-center">Carregando...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
    @media (max-width: 768px) {
        .row.g-4 > div {
            margin-bottom: 1rem;
        }
        
        .card h3 {
            font-size: 1.5rem;
        }
        
        .card h5 {
            font-size: 1rem;
        }
        
        #categoryChart,
        #evolutionChart {
            max-height: 250px !important;
        }
    }
    
    @media (max-width: 576px) {
        .row.g-4 > div {
            margin-bottom: 0.75rem;
        }
        
        .card h3 {
            font-size: 1.1rem;
        }
        
        .card h5 {
            font-size: 0.85rem;
        }
        
        .card small {
            font-size: 0.7rem;
        }
        
        #filterMonth {
            max-width: 100% !important;
            width: 100%;
            margin-top: 0.5rem;
        }
        
        .d-flex.justify-content-between {
            flex-direction: column;
        }
        
        .d-flex.justify-content-between h2 {
            margin-bottom: 0.5rem;
            font-size: 1.5rem;
        }
        
        .table {
            font-size: 0.75rem;
        }
        
        .table thead th,
        .table tbody td {
            padding: 0.4rem 0.25rem;
        }
        
        .table tbody td:nth-child(3),
        .table tbody td:nth-child(4) {
            display: none;
        }
        
        .table thead th:nth-child(3),
        .table thead th:nth-child(4) {
            display: none;
        }
        
        .card .d-flex.justify-content-between.align-items-center {
            flex-direction: column;
            text-align: center;
        }
        
        .card .d-flex.justify-content-between.align-items-center > div:first-child {
            margin-bottom: 0.5rem;
        }
        
        .card i[style*="font-size: 3rem"] {
            font-size: 2rem !important;
        }
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
let categoryChart = null;
let evolutionChart = null;
let isLoading = false;
const currentMonth = new Date().toISOString().slice(0, 7);

function formatCurrency(value) {
    if (typeof value !== 'number') {
        value = parseFloat(value) || 0;
    }
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
}

function parseCurrency(text) {
    if (!text) return 0;
    return parseFloat(text.replace(/[^\d,.-]/g, '').replace(',', '.')) || 0;
}

function loadDashboard(month = currentMonth) {
    if (isLoading) return;
    isLoading = true;

    // Reset values
    document.getElementById('totalAssets').innerText = 'R$ 0,00';
    document.getElementById('totalExpenses').innerText = 'R$ 0,00';
    document.getElementById('totalIncome').innerText = 'R$ 0,00';
    document.getElementById('monthBalance').innerText = 'R$ 0,00';

    let monthlyExpenses = 0;
    let monthlyIncome = 0;

    // Load total assets
    axios.get('/api/reports/total-assets')
        .then(r => {
            document.getElementById('totalAssets').innerText = formatCurrency(r.data.total || 0);
        })
        .catch(err => {
            console.error('Error loading assets:', err);
        });

    // Load monthly expenses
    axios.get(`/api/reports/monthly-expenses?month=${month}`)
        .then(r => {
            monthlyExpenses = r.data.total || 0;
            document.getElementById('totalExpenses').innerText = formatCurrency(monthlyExpenses);
            updateMonthBalance(monthlyIncome, monthlyExpenses);
        })
        .catch(err => {
            console.error('Error loading expenses:', err);
        });

    // Load expenses by category
    axios.get(`/api/reports/expenses-by-category?month=${month}`)
        .then(r => {
            const data = r.data || [];
            
            if (data.length === 0) {
                if (categoryChart) {
                    categoryChart.destroy();
                    categoryChart = null;
                }
                return;
            }

            const labels = data.map(item => item.category_name);
            const values = data.map(item => item.total);
            const colors = data.map(item => item.category_color || '#6c757d');

            if (categoryChart) {
                categoryChart.destroy();
            }

            const ctx = document.getElementById('categoryChart').getContext('2d');
            categoryChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: colors,
                        borderWidth: 2,
                        borderColor: 'var(--bg-primary)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: 'var(--text-primary)',
                                padding: 15,
                                font: { size: 12 }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = formatCurrency(context.parsed);
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        })
        .catch(err => {
            console.error('Error loading category chart:', err);
        });

    // Load monthly evolution
    axios.get('/api/reports/monthly-evolution')
        .then(r => {
            const data = r.data || [];
            
            if (data.length === 0) {
                if (evolutionChart) {
                    evolutionChart.destroy();
                    evolutionChart = null;
                }
                return;
            }

            const months = data.map(item => {
                const [year, month] = item.month.split('-');
                return new Date(year, month - 1).toLocaleDateString('pt-BR', { month: 'short', year: 'numeric' });
            });
            const income = data.map(item => item.income || 0);
            const expense = data.map(item => item.expense || 0);
            const balance = data.map(item => (item.income || 0) - (item.expense || 0));

            if (evolutionChart) {
                evolutionChart.destroy();
            }

            const ctx = document.getElementById('evolutionChart').getContext('2d');
            evolutionChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [
                        {
                            label: 'Receitas',
                            data: income,
                            borderColor: 'var(--success)',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Despesas',
                            data: expense,
                            borderColor: 'var(--danger)',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Saldo',
                            data: balance,
                            borderColor: 'var(--accent-primary)',
                            backgroundColor: 'rgba(0, 212, 255, 0.1)',
                            tension: 0.4,
                            fill: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: {
                                color: 'var(--text-primary)'
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${formatCurrency(context.parsed.y)}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: { color: 'var(--text-secondary)' },
                            grid: { color: 'var(--border-color)' }
                        },
                        y: {
                            ticks: { 
                                color: 'var(--text-secondary)',
                                callback: function(value) {
                                    return formatCurrency(value);
                                }
                            },
                            grid: { color: 'var(--border-color)' }
                        }
                    }
                }
            });
        })
        .catch(err => {
            console.error('Error loading evolution chart:', err);
        });

    // Load transactions for current month
    axios.get('/api/transactions')
        .then(r => {
            const transactions = (r.data || [])
                .filter(t => t.date && t.date.startsWith(month))
                .sort((a, b) => new Date(b.date) - new Date(a.date))
                .slice(0, 10);

            const tbody = document.getElementById('transactionsTable');
            if (transactions.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Nenhuma transação encontrada</td></tr>';
            } else {
                tbody.innerHTML = transactions.map(t => {
                    const typeClass = t.type === 'income' ? 'text-success' : t.type === 'expense' ? 'text-danger' : 'text-warning';
                    const typeLabel = t.type === 'income' ? 'Receita' : t.type === 'expense' ? 'Despesa' : 'Transferência';
                    const date = new Date(t.date).toLocaleDateString('pt-BR');
                    
                    return `
                        <tr>
                            <td>${date}</td>
                            <td><strong>${t.description || 'Sem descrição'}</strong></td>
                            <td>${t.account?.name || '-'}</td>
                            <td>${t.category?.name || '-'}</td>
                            <td><span class="${typeClass}">${typeLabel}</span></td>
                            <td class="text-end ${typeClass}"><strong>${formatCurrency(parseFloat(t.value || 0))}</strong></td>
                        </tr>
                    `;
                }).join('');
            }

            // Calculate monthly income
            monthlyIncome = (r.data || [])
                .filter(t => t.type === 'income' && t.date && t.date.startsWith(month))
                .reduce((sum, t) => sum + parseFloat(t.value || 0), 0);
            
            document.getElementById('totalIncome').innerText = formatCurrency(monthlyIncome);
            updateMonthBalance(monthlyIncome, monthlyExpenses);
        })
        .catch(err => {
            console.error('Error loading transactions:', err);
            document.getElementById('transactionsTable').innerHTML = '<tr><td colspan="6" class="text-center text-danger">Erro ao carregar transações</td></tr>';
        })
        .finally(() => {
            isLoading = false;
        });
}

function updateMonthBalance(income, expense) {
    const balance = income - expense;
    document.getElementById('monthBalance').innerText = formatCurrency(balance);
    document.getElementById('monthBalance').style.color = balance >= 0 ? 'var(--success)' : 'var(--danger)';
}

// Month filter change
document.getElementById('filterMonth').addEventListener('change', function(e) {
    loadDashboard(e.target.value);
});

// Initial load
loadDashboard();
</script>
@endsection