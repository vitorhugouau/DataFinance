@extends('layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-graph-up-arrow"></i> Investimentos</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#investmentModal" data-bs-backdrop="false">
        <i class="bi bi-plus-circle"></i> Novo Investimento
    </button>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted d-block mb-2">Total Investido</small>
                    <h3 id="totalInvested" class="mb-0" style="color: var(--accent-primary);">R$ 0,00</h3>
                </div>
                <div>
                    <i class="bi bi-wallet2" style="font-size: 3rem; opacity: 0.3; color: var(--accent-primary);"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted d-block mb-2">Valor Atual</small>
                    <h3 id="currentValue" class="mb-0" style="color: var(--success);">R$ 0,00</h3>
                </div>
                <div>
                    <i class="bi bi-graph-up" style="font-size: 3rem; opacity: 0.3; color: var(--success);"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted d-block mb-2">Lucro/Prejuízo</small>
                    <h3 id="totalProfit" class="mb-0">R$ 0,00</h3>
                </div>
                <div>
                    <i class="bi bi-cash-coin" style="font-size: 3rem; opacity: 0.3; color: var(--accent-secondary);"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h5 class="mb-3">Meus Investimentos</h5>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Nome</th>
                        <th>Quantidade</th>
                        <th>Preço Compra</th>
                        <th>Preço Atual</th>
                        <th>Total Investido</th>
                        <th>Valor Atual</th>
                        <th>Lucro/Prejuízo</th>
                        <th>Juros</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody id="investmentsTable">
                    <tr><td colspan="10" class="text-center">Carregando...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="investmentModal" tabindex="-1" data-bs-backdrop="false">
    <div class="modal-dialog modal-lg">
        <form id="formInvestment" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Novo Investimento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="editInvestmentId">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Conta</label>
                        <select name="account_id" id="investmentAccount" class="form-select" required></select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tipo</label>
                        <select name="type" id="investmentType" class="form-select" required>
                            <option value="">Selecione...</option>
                            <option value="crypto">Criptomoeda</option>
                            <option value="currency">Moeda</option>
                            <option value="stock">Ação</option>
                            <option value="fixed_income">Renda Fixa</option>
                            <option value="savings">Poupança</option>
                            <option value="piggy_bank">Cofrinho</option>
                            <option value="other">Outro</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Símbolo</label>
                        <input name="symbol" id="investmentSymbol" class="form-control" placeholder="Ex: BTC, ETH, USD" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nome</label>
                        <input name="name" id="investmentName" class="form-control" placeholder="Ex: Bitcoin, Ethereum" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Quantidade</label>
                        <input name="amount" id="investmentAmount" class="form-control" type="number" step="0.00000001" min="0.00000001" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Preço de Compra</label>
                        <input name="purchase_price" id="investmentPurchasePrice" class="form-control" type="number" step="0.01" min="0.01" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Data de Compra</label>
                        <input name="purchase_date" id="investmentPurchaseDate" class="form-control" type="date" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Preço Atual (Opcional)</label>
                        <input name="current_price" id="investmentCurrentPrice" class="form-control" type="number" step="0.01" min="0.01">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Taxa de Juros % (Opcional)</label>
                        <input name="interest_rate" id="investmentInterestRate" class="form-control" type="number" step="0.01" min="0" max="100">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tipo de Juros (Opcional)</label>
                        <select name="interest_type" id="investmentInterestType" class="form-select">
                            <option value="">Selecione...</option>
                            <option value="monthly">Mensal</option>
                            <option value="yearly">Anual</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Observações</label>
                        <textarea name="notes" id="investmentNotes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">
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
            font-size: 0.75rem;
        }
        
        .table thead th,
        .table tbody td {
            padding: 0.5rem 0.25rem;
        }
        
        .table tbody td:nth-child(3),
        .table tbody td:nth-child(4),
        .table tbody td:nth-child(5),
        .table tbody td:nth-child(6),
        .table tbody td:nth-child(7) {
            display: none;
        }
        
        .table thead th:nth-child(3),
        .table thead th:nth-child(4),
        .table thead th:nth-child(5),
        .table thead th:nth-child(6),
        .table thead th:nth-child(7) {
            display: none;
        }
        
        .card h3 {
            font-size: 1.5rem;
        }
        
        .modal-dialog {
            margin: 0.5rem;
        }
        
        .modal-body .row .col-md-6,
        .modal-body .row .col-md-4 {
            margin-bottom: 0.5rem;
        }
    }
    
    @media (max-width: 576px) {
        .table {
            font-size: 0.7rem;
        }
        
        .table tbody td:nth-child(8) {
            display: none;
        }
        
        .table thead th:nth-child(8) {
            display: none;
        }
        
        .card h3 {
            font-size: 1.25rem;
        }
        
        .btn {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
        }
        
        h2 {
            font-size: 1.5rem;
        }
    }
</style>
<script>
let accounts = [];
let investments = [];

function formatCurrency(value) {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
}

function loadAccounts() {
    axios.get('/api/accounts')
        .then(r => {
            accounts = r.data || [];
            const select = document.getElementById('investmentAccount');
            select.innerHTML = '<option value="">Selecione...</option>';
            accounts.filter(a => a.active).forEach(account => {
                select.innerHTML += `<option value="${account.id}">${account.name}</option>`;
            });
        })
        .catch(err => console.error('Error loading accounts:', err));
}

function loadInvestments() {
    axios.get('/api/investments')
        .then(r => {
            investments = r.data || [];
            renderInvestments();
            calculateTotals();
        })
        .catch(err => {
            console.error('Error loading investments:', err);
            document.getElementById('investmentsTable').innerHTML = '<tr><td colspan="10" class="text-center text-danger">Erro ao carregar investimentos</td></tr>';
        });
}

function renderInvestments() {
    const tbody = document.getElementById('investmentsTable');
    
    if (investments.length === 0) {
        tbody.innerHTML = '<tr><td colspan="10" class="text-center text-muted">Nenhum investimento cadastrado</td></tr>';
        return;
    }

            tbody.innerHTML = investments.map(inv => {
                const profit = parseFloat(inv.profit || 0);
                const profitClass = profit >= 0 ? 'text-success' : 'text-danger';
                const profitIcon = profit >= 0 ? 'bi-arrow-up' : 'bi-arrow-down';
                const interestEarned = parseFloat(inv.interest_earned || 0);
                const typeLabels = {
                    'crypto': 'Cripto',
                    'currency': 'Moeda',
                    'stock': 'Ação',
                    'fixed_income': 'Renda Fixa',
                    'savings': 'Poupança',
                    'piggy_bank': 'Cofrinho',
                    'other': 'Outro'
                };
                const typeLabel = typeLabels[inv.type] || inv.type;
                
                return `
                    <tr>
                        <td><span class="badge bg-primary" title="${typeLabel}">${typeLabel}</span></td>
                        <td><strong>${inv.name}</strong><br><small class="text-muted">${inv.symbol}</small><br><small class="d-md-none text-muted">Qtd: ${parseFloat(inv.amount).toLocaleString('pt-BR', { maximumFractionDigits: 4 })}</small></td>
                        <td class="d-none d-md-table-cell">${parseFloat(inv.amount).toLocaleString('pt-BR', { maximumFractionDigits: 8 })}</td>
                        <td class="d-none d-md-table-cell">${formatCurrency(parseFloat(inv.purchase_price))}</td>
                        <td class="d-none d-md-table-cell">${inv.current_price ? formatCurrency(parseFloat(inv.current_price)) : '-'}</td>
                        <td class="d-none d-md-table-cell">${formatCurrency(parseFloat(inv.total_invested))}</td>
                        <td class="d-none d-md-table-cell">${formatCurrency(parseFloat(inv.current_value))}</td>
                        <td class="${profitClass}">
                            <i class="bi ${profitIcon}"></i> ${formatCurrency(profit)}<br>
                            <small>${parseFloat(inv.profit_percentage || 0).toFixed(2)}%</small>
                            <div class="d-md-none mt-1">
                                <small class="text-muted d-block">Investido: ${formatCurrency(parseFloat(inv.total_invested))}</small>
                                <small class="text-muted d-block">Atual: ${formatCurrency(parseFloat(inv.current_value))}</small>
                            </div>
                        </td>
                        <td class="d-none d-lg-table-cell">${interestEarned > 0 ? formatCurrency(interestEarned) : '-'}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary me-1" onclick="editInvestment(${inv.id})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteInvestment(${inv.id})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
}

function calculateTotals() {
    const totalInvested = investments.reduce((sum, inv) => sum + parseFloat(inv.total_invested || 0), 0);
    const currentValue = investments.reduce((sum, inv) => sum + parseFloat(inv.current_value || 0), 0);
    const totalProfit = currentValue - totalInvested;
    
    document.getElementById('totalInvested').innerText = formatCurrency(totalInvested);
    document.getElementById('currentValue').innerText = formatCurrency(currentValue);
    
    const profitEl = document.getElementById('totalProfit');
    profitEl.innerText = formatCurrency(totalProfit);
    profitEl.style.color = totalProfit >= 0 ? 'var(--success)' : 'var(--danger)';
}

function editInvestment(id) {
    const investment = investments.find(inv => inv.id == id);
    if (!investment) return;
    
    document.getElementById('editInvestmentId').value = investment.id;
    document.getElementById('investmentAccount').value = investment.account_id;
    document.getElementById('investmentType').value = investment.type;
    document.getElementById('investmentSymbol').value = investment.symbol;
    document.getElementById('investmentName').value = investment.name;
    document.getElementById('investmentAmount').value = investment.amount;
    document.getElementById('investmentPurchasePrice').value = investment.purchase_price;
    document.getElementById('investmentPurchaseDate').value = investment.purchase_date;
    document.getElementById('investmentCurrentPrice').value = investment.current_price || '';
    document.getElementById('investmentInterestRate').value = investment.interest_rate || '';
    document.getElementById('investmentInterestType').value = investment.interest_type || '';
    document.getElementById('investmentNotes').value = investment.notes || '';
    
    const modal = new bootstrap.Modal(document.getElementById('investmentModal'), { backdrop: false });
    modal.show();
}

function deleteInvestment(id) {
    if (!confirm('Tem certeza que deseja excluir este investimento?')) return;
    
    axios.delete(`/api/investments/${id}`)
        .then(() => {
            loadInvestments();
        })
        .catch(err => {
            console.error('Error deleting investment:', err);
            alert('Erro ao excluir investimento');
        });
}

document.getElementById('formInvestment').onsubmit = function(e) {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(e.target));
    const id = data.id;
    
    if (!data.current_price) delete data.current_price;
    if (!data.interest_rate) delete data.interest_rate;
    if (!data.interest_type) delete data.interest_type;
    if (!data.notes) delete data.notes;
    
    const url = id ? `/api/investments/${id}` : '/api/investments';
    const method = id ? 'put' : 'post';
    
    axios[method](url, data)
        .then(() => {
            loadInvestments();
            const modal = bootstrap.Modal.getInstance(document.getElementById('investmentModal'));
            if (modal) modal.hide();
            e.target.reset();
            document.getElementById('editInvestmentId').value = '';
        })
        .catch(err => {
            console.error('Error saving investment:', err);
            alert('Erro ao salvar investimento: ' + (err.response?.data?.message || err.message));
        });
};

// Set today as default purchase date
document.getElementById('investmentPurchaseDate').value = new Date().toISOString().split('T')[0];

loadAccounts();
loadInvestments();
</script>
@endsection
