@extends('layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-arrow-left-right"></i> Transações</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#txModal">
        <i class="bi bi-plus-circle"></i> Nova Transação
    </button>
</div>

<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Conta</label>
                <select id="filterAccount" class="form-select">
                    <option value="">Todas contas</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Tipo</label>
                <select id="filterType" class="form-select">
                    <option value="">Todos tipos</option>
                    <option value="expense">Despesa</option>
                    <option value="income">Receita</option>
                    <option value="transfer">Transferência</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Mês</label>
                <input id="filterDate" type="month" class="form-control" value="{{ date('Y-m') }}">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-outline-secondary w-100" onclick="loadTransactions()">
                    <i class="bi bi-funnel"></i> Filtrar
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
    <div class="table-responsive">
            <table class="table">
            <thead>
                    <tr>
                        <th>Data</th>
                        <th>Conta</th>
                        <th>Tipo</th>
                        <th>Categoria</th>
                        <th>Descrição</th>
                        <th class="text-end">Valor</th>
                        <th class="text-center">Ações</th>
                    </tr>
            </thead>
                <tbody id="listTransactions">
                    <tr><td colspan="7" class="text-center">Carregando...</td></tr>
                </tbody>
        </table>
        </div>
    </div>
</div>

<div class="modal fade" id="txModal" tabindex="-1" data-bs-backdrop="false">
    <div class="modal-dialog modal-lg">
        <form id="formTx" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Nova Transação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                    <label class="form-label">Tipo</label>
                        <select name="type" id="txType" class="form-select" required>
                        <option value="expense">Despesa</option>
                        <option value="income">Receita</option>
                        <option value="transfer">Transferência</option>
                    </select>
                </div>

                    <div class="col-md-6">
                    <label class="form-label">Conta</label>
                        <select name="account_id" id="txAccount" class="form-select" required></select>
                </div>

                    <div class="col-md-6 d-none" id="relatedAccountWrap">
                        <label class="form-label">Conta Destino</label>
                    <select name="related_account_id" id="txRelated" class="form-select"></select>
                </div>

                    <div class="col-md-6" id="catWrap">
                    <label class="form-label">Categoria</label>
                        <select name="category_id" id="txCategory" class="form-select">
                            <option value="">Sem categoria</option>
                        </select>
                </div>

                    <div class="col-md-6">
                    <label class="form-label">Valor</label>
                        <input name="value" class="form-control" type="number" step="0.01" min="0.01" required>
                </div>

                    <div class="col-md-6">
                    <label class="form-label">Data</label>
                    <input name="date" class="form-control" type="date" value="{{ date('Y-m-d') }}" required>
                </div>

                    <div class="col-12">
                    <label class="form-label">Descrição</label>
                        <input name="description" class="form-control" placeholder="Descrição da transação">
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
                <button class="btn btn-primary" type="submit">
                    <i class="bi bi-check-lg"></i> Salvar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<style>
    @media (max-width: 768px) {
        .table {
            font-size: 0.8rem;
        }
        
        .table thead th,
        .table tbody td {
            padding: 0.5rem 0.25rem;
        }
        
        .table tbody td:first-child {
            min-width: 80px;
        }
        
        .table tbody td:nth-child(2),
        .table tbody td:nth-child(3),
        .table tbody td:nth-child(4) {
            display: none;
        }
    }
    
    @media (max-width: 576px) {
        .table {
            font-size: 0.7rem;
        }
        
        .table tbody td:nth-child(3),
        .table tbody td:nth-child(4) {
            display: none;
        }
        
        .table thead th:nth-child(3),
        .table thead th:nth-child(4) {
            display: none;
        }
        
        .table tbody td:nth-child(2) {
            font-size: 0.8rem;
        }
        
        .table tbody td:nth-child(2) .text-muted {
            display: none;
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }
        
        #filterAccount,
        #filterType,
        #filterDate {
            margin-bottom: 0.5rem;
        }
        
        .card .row.g-3 > div {
            margin-bottom: 0.5rem;
        }
    }
</style>
<script>
let accounts = [], categories = [], transactions = [];

function formatCurrency(value) {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('pt-BR');
}

function loadMeta() {
    axios.get('/api/accounts')
        .then(r => {
            accounts = r.data || [];
            renderAccountOptions();
            renderFilterAccounts();
        })
        .catch(err => {
            console.error('Error loading accounts:', err);
        });
    
    axios.get('/api/categories')
        .then(r => {
            categories = r.data || [];
            renderCategoryOptions();
        })
        .catch(err => {
            console.error('Error loading categories:', err);
        });
}

function renderAccountOptions() {
    const accountSelect = document.getElementById('txAccount');
    const relatedSelect = document.getElementById('txRelated');
    
    accountSelect.innerHTML = '';
    relatedSelect.innerHTML = '';
    
    accounts.filter(a => a.active).forEach(account => {
        const option = `<option value="${account.id}">${account.name}</option>`;
        accountSelect.innerHTML += option;
        relatedSelect.innerHTML += option;
    });
}

function renderFilterAccounts() {
    const filterSelect = document.getElementById('filterAccount');
    filterSelect.innerHTML = '<option value="">Todas contas</option>';
    
    accounts.forEach(account => {
        filterSelect.innerHTML += `<option value="${account.id}">${account.name}</option>`;
    });
}

function renderCategoryOptions() {
    const categorySelect = document.getElementById('txCategory');
    categorySelect.innerHTML = '<option value="">Sem categoria</option>';
    
    // Get all categories (including subcategories)
    const allCats = categories.filter(c => c.type === 'expense' || c.type === 'income');
    const mainCats = allCats.filter(c => !c.parent_id);
    
    mainCats.forEach(cat => {
        categorySelect.innerHTML += `<option value="${cat.id}">${cat.name}</option>`;
        const subcats = allCats.filter(c => c.parent_id == cat.id);
        subcats.forEach(subcat => {
            categorySelect.innerHTML += `<option value="${subcat.id}">  └ ${subcat.name}</option>`;
        });
    });
}

function loadTransactions() {
    axios.get('/api/transactions')
        .then(r => {
            transactions = r.data || [];
            applyFilters();
        })
        .catch(err => {
            console.error('Error loading transactions:', err);
            document.getElementById('listTransactions').innerHTML = '<tr><td colspan="7" class="text-center text-danger">Erro ao carregar transações</td></tr>';
        });
}

function applyFilters() {
    let filtered = transactions.slice();
    
    const accountFilter = document.getElementById('filterAccount').value;
    const typeFilter = document.getElementById('filterType').value;
    const dateFilter = document.getElementById('filterDate').value;
    
    if (accountFilter) {
        filtered = filtered.filter(t => t.account_id == accountFilter);
    }
    
    if (typeFilter) {
        filtered = filtered.filter(t => t.type === typeFilter);
    }
    
    if (dateFilter) {
        const [year, month] = dateFilter.split('-');
        filtered = filtered.filter(t => t.date.startsWith(`${year}-${month}`));
        }
    
    // Sort by date descending
    filtered.sort((a, b) => new Date(b.date) - new Date(a.date));
    
    renderTransactions(filtered);
}

function renderTransactions(transactionsList) {
    const tbody = document.getElementById('listTransactions');
    
    if (transactionsList.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Nenhuma transação encontrada</td></tr>';
        return;
    }
    
    tbody.innerHTML = transactionsList.map(t => {
        const typeClass = t.type === 'income' ? 'text-success' : t.type === 'expense' ? 'text-danger' : 'text-warning';
        const typeLabel = t.type === 'income' ? 'Receita' : t.type === 'expense' ? 'Despesa' : 'Transferência';
        const typeIcon = t.type === 'income' ? 'bi-arrow-up-circle' : t.type === 'expense' ? 'bi-arrow-down-circle' : 'bi-arrow-left-right';
        
        return `
            <tr>
                <td>${formatDate(t.date)}</td>
                <td>${t.account?.name || '-'}</td>
                <td><span class="${typeClass}"><i class="bi ${typeIcon}"></i> ${typeLabel}</span></td>
                <td>${t.category?.name || '-'}</td>
                <td><strong>${t.description || 'Sem descrição'}</strong></td>
                <td class="text-end ${typeClass}"><strong>${formatCurrency(parseFloat(t.value))}</strong></td>
                <td class="text-center">
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteTransaction(${t.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

function deleteTransaction(id) {
    if (!confirm('Tem certeza que deseja excluir esta transação?')) return;
    
    axios.delete(`/api/transactions/${id}`)
        .then(() => {
            loadTransactions();
        })
        .catch(err => {
            console.error('Error deleting transaction:', err);
            alert('Erro ao excluir transação: ' + (err.response?.data?.message || err.message));
        });
}

document.getElementById('txType').addEventListener('change', function(e) {
    const isTransfer = e.target.value === 'transfer';
    document.getElementById('relatedAccountWrap').classList.toggle('d-none', !isTransfer);
    document.getElementById('catWrap').classList.toggle('d-none', isTransfer);
    
    // Update category options based on type
    if (!isTransfer) {
        renderCategoryOptions();
    }
});

document.getElementById('formTx').onsubmit = function(e) {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(e.target));
    
    if (!data.category_id) delete data.category_id;
    if (!data.related_account_id) delete data.related_account_id;
    if (!data.description) delete data.description;
    
    axios.post('/api/transactions', data).then(() => {
        loadTransactions();
        const modalElement = document.getElementById('txModal');
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
        }
        e.target.reset();
        document.getElementById('relatedAccountWrap').classList.add('d-none');
        document.getElementById('catWrap').classList.remove('d-none');
    }).catch(err => {
        alert('Erro ao criar transação: ' + (err.response?.data?.message || err.message));
    });
};

// Filter change listeners
document.getElementById('filterAccount').addEventListener('change', loadTransactions);
document.getElementById('filterType').addEventListener('change', loadTransactions);
document.getElementById('filterDate').addEventListener('change', loadTransactions);

loadMeta();
loadTransactions();
</script>
@endsection