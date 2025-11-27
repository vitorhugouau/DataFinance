@extends('layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-wallet2"></i> Contas</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#accountModal">
        <i class="bi bi-plus-circle"></i> Nova Conta
    </button>
</div>

<div class="card mb-4">
    <form id="formAccount" class="row g-3 p-3">
        <div class="col-md-4">
            <label class="form-label">Nome da Conta</label>
            <input class="form-control" name="name" placeholder="Ex: Conta Corrente" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Tipo</label>
            <select class="form-select" name="type" required>
                <option value="">Selecione...</option>
                <option value="wallet">Carteira</option>
                <option value="bank">Conta Bancária</option>
                <option value="credit_card">Cartão de Crédito</option>
                <option value="broker">Corretora</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Moeda</label>
            <select class="form-select" name="currency">
                <option value="BRL">BRL</option>
                <option value="USD">USD</option>
                <option value="EUR">EUR</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Saldo Inicial</label>
            <input class="form-control" name="initial_balance" type="number" step="0.01" placeholder="0.00" value="0">
        </div>
        <div class="col-md-1 d-flex align-items-end">
            <button class="btn btn-primary w-100" type="submit">
                <i class="bi bi-check-lg"></i>
            </button>
        </div>
    </form>
</div>

<div class="row g-4" id="accountsGrid">
    <!-- Accounts will be loaded here -->
</div>

<div class="modal fade" id="accountModal" tabindex="-1" data-bs-backdrop="false">
    <div class="modal-dialog">
        <form id="formAccountModal" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Conta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="editAccountId">
                <div class="mb-3">
                    <label class="form-label">Nome</label>
                    <input class="form-control" name="name" id="editAccountName" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tipo</label>
                    <select class="form-select" name="type" id="editAccountType" required>
                        <option value="wallet">Carteira</option>
                        <option value="bank">Conta Bancária</option>
                        <option value="credit_card">Cartão de Crédito</option>
                        <option value="broker">Corretora</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Moeda</label>
                    <select class="form-select" name="currency" id="editAccountCurrency">
                        <option value="BRL">BRL</option>
                        <option value="USD">USD</option>
                        <option value="EUR">EUR</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Saldo Inicial</label>
                    <input class="form-control" name="initial_balance" id="editAccountBalance" type="number" step="0.01">
                </div>
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="active" id="editAccountActive" checked>
                        <label class="form-check-label" for="editAccountActive">Conta Ativa</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<style>
    @media (max-width: 768px) {
        #accountsGrid .col-md-4 {
            margin-bottom: 1rem;
        }
        
        .card-body h3 {
            font-size: 1.5rem;
        }
        
        .card-body h5 {
            font-size: 1rem;
        }
    }
    
    @media (max-width: 576px) {
        #formAccount .col-md-4,
        #formAccount .col-md-3,
        #formAccount .col-md-2,
        #formAccount .col-md-1 {
            margin-bottom: 0.5rem;
        }
        
        .card-body h3 {
            font-size: 1.25rem;
        }
        
        .card-body h5 {
            font-size: 1rem;
        }
        
        .d-flex.gap-2 {
            flex-direction: column;
        }
        
        .d-flex.gap-2 .btn {
            width: 100%;
            margin-bottom: 0.25rem;
        }
    }
</style>
<script>
const typeLabels = {
    wallet: 'Carteira',
    bank: 'Conta Bancária',
    credit_card: 'Cartão de Crédito',
    broker: 'Corretora'
};

const typeIcons = {
    wallet: 'bi-wallet2',
    bank: 'bi-bank',
    credit_card: 'bi-credit-card',
    broker: 'bi-graph-up-arrow'
};

function formatCurrency(value, currency = 'BRL') {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: currency }).format(value);
}

function loadAccounts() {
    axios.get('/api/accounts')
        .then(r => {
            const accounts = r.data || [];
            const grid = document.getElementById('accountsGrid');
            
            if (accounts.length === 0) {
                grid.innerHTML = '<div class="col-12"><div class="card text-center p-5"><p class="text-muted">Nenhuma conta cadastrada</p></div></div>';
                return;
            }

        grid.innerHTML = accounts.map(account => {
            const balance = account.current_balance || account.initial_balance || 0;
            const balanceClass = balance >= 0 ? 'text-success' : 'text-danger';
            const statusBadge = account.active 
                ? '<span class="badge bg-success">Ativa</span>' 
                : '<span class="badge bg-secondary">Inativa</span>';
            
            return `
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="mb-1">
                                        <i class="bi ${typeIcons[account.type] || 'bi-wallet2'}"></i>
                                        ${account.name}
                                    </h5>
                                    <small class="text-muted">${typeLabels[account.type] || account.type}</small>
                                </div>
                                ${statusBadge}
                            </div>
                            <div class="mb-3">
                                <small class="text-muted d-block">Saldo Atual</small>
                                <h3 class="${balanceClass} mb-0">${formatCurrency(balance, account.currency || 'BRL')}</h3>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-primary flex-fill" onclick="editAccount(${account.id})">
                                    <i class="bi bi-pencil"></i> Editar
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteAccount(${account.id})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
        })
        .catch(err => {
            console.error('Error loading accounts:', err);
            document.getElementById('accountsGrid').innerHTML = '<div class="col-12"><div class="card text-center p-5"><p class="text-danger">Erro ao carregar contas</p></div></div>';
        });
}

function editAccount(id) {
    axios.get(`/api/accounts/${id}`)
        .then(r => {
            const account = r.data;
            document.getElementById('editAccountId').value = account.id;
            document.getElementById('editAccountName').value = account.name || '';
            document.getElementById('editAccountType').value = account.type || 'wallet';
            document.getElementById('editAccountCurrency').value = account.currency || 'BRL';
            document.getElementById('editAccountBalance').value = account.initial_balance || 0;
            document.getElementById('editAccountActive').checked = account.active !== false;
            
        const modal = new bootstrap.Modal(document.getElementById('accountModal'), {
            backdrop: false,
            keyboard: true
        });
        modal.show();
        })
        .catch(err => {
            console.error('Error loading account:', err);
            alert('Erro ao carregar conta. Tente novamente.');
        });
}

function deleteAccount(id) {
    if (!confirm('Tem certeza que deseja excluir esta conta?')) return;
    
    axios.delete(`/api/accounts/${id}`)
        .then(() => {
            loadAccounts();
        })
        .catch(err => {
            console.error('Error deleting account:', err);
            alert('Erro ao excluir conta: ' + (err.response?.data?.message || err.message));
        });
}

document.getElementById('formAccount').onsubmit = function(e) {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(e.target));
    data.initial_balance = parseFloat(data.initial_balance) || 0;
    data.currency = data.currency || 'BRL';
    
    axios.post('/api/accounts', data)
        .then(() => {
            loadAccounts();
            e.target.reset();
        })
        .catch(err => {
            console.error('Error creating account:', err);
            alert('Erro ao criar conta: ' + (err.response?.data?.message || err.message));
        });
};

document.getElementById('formAccountModal').onsubmit = function(e) {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(e.target));
    const id = data.id;
    if (!id) {
        alert('ID da conta não encontrado');
        return;
    }
    delete data.id;
    data.initial_balance = parseFloat(data.initial_balance) || 0;
    data.active = document.getElementById('editAccountActive').checked;
    data.currency = data.currency || 'BRL';
    
    axios.put(`/api/accounts/${id}`, data)
        .then(() => {
            loadAccounts();
            const modalElement = document.getElementById('accountModal');
            const modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (modalInstance) {
                modalInstance.hide();
            }
        })
        .catch(err => {
            console.error('Error updating account:', err);
            alert('Erro ao atualizar conta: ' + (err.response?.data?.message || err.message));
        });
};

loadAccounts();
</script>
@endsection