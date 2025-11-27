@extends('layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-credit-card"></i> Cartões de Crédito</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#creditCardModal" data-bs-backdrop="false">
        <i class="bi bi-plus-circle"></i> Novo Cartão
    </button>
</div>

<div class="row g-4" id="creditCardsGrid">
    <!-- Cards will be loaded here -->
</div>

<div class="modal fade" id="creditCardModal" tabindex="-1" data-bs-backdrop="false">
    <div class="modal-dialog">
        <form id="formCreditCard" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="creditCardModalTitle"><i class="bi bi-credit-card"></i> Novo Cartão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="editCreditCardId">
                <div class="mb-3">
                    <label class="form-label">Conta</label>
                    <select name="account_id" id="creditCardAccount" class="form-select" required></select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nome do Cartão</label>
                    <input name="name" id="creditCardName" class="form-control" placeholder="Ex: Nubank, Itaú..." required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Últimos 4 dígitos</label>
                    <input name="last_four_digits" id="creditCardDigits" class="form-control" maxlength="4" pattern="[0-9]{4}" placeholder="1234" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Limite</label>
                    <input name="limit" id="creditCardLimit" class="form-control" type="number" step="0.01" min="0" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Saldo Atual</label>
                    <input name="current_balance" id="creditCardBalance" class="form-control" type="number" step="0.01" min="0" value="0">
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Data de Fechamento</label>
                        <input name="closing_date" id="creditCardClosingDate" class="form-control" type="date" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Data de Vencimento</label>
                        <input name="due_date" id="creditCardDueDate" class="form-control" type="date" required>
                    </div>
                </div>
                <div class="mb-3 mt-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="active" id="creditCardActive" checked>
                        <label class="form-check-label" for="creditCardActive">Cartão Ativo</label>
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

<div class="modal fade" id="expensesModal" tabindex="-1" data-bs-backdrop="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-list"></i> Gastos do Cartão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="currentCardId">

                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="mb-3">Adicionar Novo Gasto</h6>
                        <form id="formExpense" class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nome/Descrição</label>
                                <input name="name" id="expenseName" class="form-control" placeholder="Ex: Supermercado, Restaurante..." required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" id="expenseValueLabel">Valor</label>
                                <input name="value" id="expenseValue" class="form-control" type="number" step="0.01" min="0.01" required>
                                <small class="text-muted" id="expenseValueHelp">Valor total ou mensal</small>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Data</label>
                                <input name="date" id="expenseDate" class="form-control" type="date" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="expenseInstallment" onchange="toggleInstallmentFields()">
                                    <label class="form-check-label" for="expenseInstallment">
                                        <i class="bi bi-calendar-month"></i> Parcelado
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6" id="installmentFields" style="display: none;">
                                <label class="form-label">Número de Parcelas</label>
                                <input name="installments" id="expenseInstallments" class="form-control" type="number" min="2" step="1" placeholder="Ex: 3, 6, 12...">
                            </div>
                            <div class="col-md-6" id="totalValueField" style="display: none;">
                                <label class="form-label">Valor Total</label>
                                <input name="total_value" id="expenseTotalValue" class="form-control" type="number" step="0.01" min="0.01" placeholder="Valor total da compra">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> Adicionar Gasto
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-3">Histórico de Gastos</h6>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Nome</th>
                                        <th class="text-end">Valor Mensal</th>
                                        <th class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="expensesTable">
                                    <tr><td colspan="4" class="text-center">Carregando...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
    @media (max-width: 768px) {
        #creditCardsGrid .col-md-4 {
            margin-bottom: 1rem;
        }

        .card-body h3 {
            font-size: 1.5rem;
        }
    }

    @media (max-width: 576px) {
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

        .table {
            font-size: 0.75rem;
        }

        .table thead th,
        .table tbody td {
            padding: 0.5rem 0.25rem;
        }
    }
</style>
<script>
let accounts = [];
let creditCards = [];

function formatCurrency(value) {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('pt-BR');
}

function loadAccounts() {
    axios.get('/api/accounts')
        .then(r => {
            accounts = r.data || [];
            const select = document.getElementById('creditCardAccount');
            select.innerHTML = '<option value="">Selecione...</option>';
            accounts.filter(a => a.active).forEach(account => {
                select.innerHTML += `<option value="${account.id}">${account.name}</option>`;
            });
        })
        .catch(err => console.error('Error loading accounts:', err));
}

function loadCreditCards() {
    axios.get('/api/credit-cards')
        .then(r => {
            creditCards = r.data || [];
            renderCreditCards();
        })
        .catch(err => {
            console.error('Error loading credit cards:', err);
            document.getElementById('creditCardsGrid').innerHTML = '<div class="col-12"><div class="card text-center p-5"><p class="text-danger">Erro ao carregar cartões</p></div></div>';
        });
}

function renderCreditCards() {
    const grid = document.getElementById('creditCardsGrid');

    if (creditCards.length === 0) {
        grid.innerHTML = '<div class="col-12"><div class="card text-center p-5"><p class="text-muted">Nenhum cartão cadastrado</p></div></div>';
        return;
    }

    grid.innerHTML = creditCards.map(card => {
        const usage = parseFloat(card.usage_percentage || 0);
        const usageClass = usage >= 80 ? 'danger' : usage >= 50 ? 'warning' : 'success';
        const available = parseFloat(card.available_limit || 0);
        const statusBadge = card.active
            ? '<span class="badge bg-success">Ativo</span>'
            : '<span class="badge bg-secondary">Inativo</span>';

        return `
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="mb-1">
                                    <i class="bi bi-credit-card"></i>
                                    ${card.name}
                                </h5>
                                <small class="text-muted">**** ${card.last_four_digits}</small>
                            </div>
                            ${statusBadge}
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <small class="text-muted">Limite</small>
                                <strong>${formatCurrency(parseFloat(card.limit))}</strong>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-${usageClass}" role="progressbar"
                                     style="width: ${Math.min(usage, 100)}%"
                                     aria-valuenow="${usage}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <small class="text-muted">${usage.toFixed(1)}% utilizado</small>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">Gasto Atual</small>
                                <strong class="text-danger">${formatCurrency(parseFloat(card.current_balance))}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">Disponível</small>
                                <strong class="text-success">${formatCurrency(available)}</strong>
                            </div>
                        </div>

                        <div class="mb-3 p-2 border rounded" style="background: var(--bg-secondary);">
                            <small class="text-muted d-block">Fechamento</small>
                            <strong>${formatDate(card.closing_date)}</strong>
                            <br>
                            <small class="text-muted d-block mt-1">Vencimento</small>
                            <strong>${formatDate(card.due_date)}</strong>
                        </div>

                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-primary flex-fill" onclick="editCreditCard(${card.id})">
                                <i class="bi bi-pencil"></i> Editar
                            </button>
                            <button class="btn btn-sm btn-outline-success flex-fill" onclick="showExpenses(${card.id})">
                                <i class="bi bi-list"></i> Gastos
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteCreditCard(${card.id})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

function editCreditCard(id) {
    axios.get(`/api/credit-cards/${id}`)
        .then(r => {
            const card = r.data;

            document.getElementById('editCreditCardId').value = card.id;
            document.getElementById('creditCardAccount').value = card.account_id || '';
            document.getElementById('creditCardName').value = card.name || '';
            document.getElementById('creditCardDigits').value = card.last_four_digits || '';
            document.getElementById('creditCardLimit').value = card.limit || 0;
            document.getElementById('creditCardBalance').value = card.current_balance || 0;
            document.getElementById('creditCardClosingDate').value = card.closing_date || '';
            document.getElementById('creditCardDueDate').value = card.due_date || '';
            document.getElementById('creditCardActive').checked = card.active !== false;

            document.getElementById('creditCardModalTitle').innerHTML = '<i class="bi bi-credit-card"></i> Editar Cartão';

            const modal = new bootstrap.Modal(document.getElementById('creditCardModal'), { backdrop: false });
            modal.show();
        })
        .catch(err => {
            console.error('Error loading credit card:', err);
            alert('Erro ao carregar cartão');
        });
}

function toggleInstallmentFields() {
    const isInstallment = document.getElementById('expenseInstallment').checked;
    const installmentFields = document.getElementById('installmentFields');
    const totalValueField = document.getElementById('totalValueField');
    const valueLabel = document.getElementById('expenseValueLabel');
    const valueHelp = document.getElementById('expenseValueHelp');
    const valueInput = document.getElementById('expenseValue');
    const installmentsInput = document.getElementById('expenseInstallments');
    const totalValueInput = document.getElementById('expenseTotalValue');

    if (isInstallment) {
        installmentFields.style.display = 'block';
        totalValueField.style.display = 'block';
        valueLabel.textContent = 'Valor Mensal';
        valueHelp.textContent = 'Será calculado automaticamente se informar valor total e parcelas';
        valueInput.required = false;
        installmentsInput.required = true;
        totalValueInput.required = true;
    } else {
        installmentFields.style.display = 'none';
        totalValueField.style.display = 'none';
        valueLabel.textContent = 'Valor';
        valueHelp.textContent = 'Valor total ou mensal';
        valueInput.required = true;
        installmentsInput.required = false;
        totalValueInput.required = false;
        installmentsInput.value = '';
        totalValueInput.value = '';
    }
}

// Calculate monthly value when total value and installments are provided
document.addEventListener('DOMContentLoaded', function() {
    const totalValueInput = document.getElementById('expenseTotalValue');
    const installmentsInput = document.getElementById('expenseInstallments');
    const valueInput = document.getElementById('expenseValue');

    if (totalValueInput && installmentsInput && valueInput) {
        function calculateMonthlyValue() {
            const isInstallment = document.getElementById('expenseInstallment').checked;
            if (isInstallment) {
                const total = parseFloat(totalValueInput.value) || 0;
                const installments = parseInt(installmentsInput.value) || 1;
                if (total > 0 && installments > 1) {
                    valueInput.value = (total / installments).toFixed(2);
                }
            }
        }

        totalValueInput.addEventListener('input', calculateMonthlyValue);
        installmentsInput.addEventListener('input', calculateMonthlyValue);
    }
});

function showExpenses(id) {
    document.getElementById('currentCardId').value = id;
    loadExpenses(id);

    const modal = new bootstrap.Modal(document.getElementById('expensesModal'), { backdrop: false });
    modal.show();
    
    // Reset installment fields
    document.getElementById('expenseInstallment').checked = false;
    toggleInstallmentFields();
}

function loadExpenses(cardId) {
    axios.get(`/api/credit-cards/${cardId}/expenses`)
        .then(r => {
            const expenses = r.data || [];
            renderExpenses(expenses);
        })
        .catch(err => {
            console.error('Error loading expenses:', err);
            document.getElementById('expensesTable').innerHTML = '<tr><td colspan="4" class="text-center text-danger">Erro ao carregar gastos</td></tr>';
        });
}

function renderExpenses(expenses) {
    const tbody = document.getElementById('expensesTable');

    if (expenses.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Nenhum gasto registrado</td></tr>';
        return;
    }

    tbody.innerHTML = expenses.map(exp => {
        const isInstallment = exp.installments && exp.installments > 1;
        const installmentInfo = isInstallment 
            ? `<br><small class="text-muted">${exp.current_installment}/${exp.installments} parcelas - Total: ${formatCurrency(parseFloat(exp.total_value || exp.value * exp.installments))}</small>`
            : '';
        
        return `
            <tr>
                <td>${formatDate(exp.date)}</td>
                <td>
                    <strong>${exp.name}</strong>
                    ${installmentInfo}
                </td>
                <td class="text-end text-danger">
                    <strong>${formatCurrency(parseFloat(exp.value))}</strong>
                    ${isInstallment ? '<br><small class="text-muted">mensal</small>' : ''}
                </td>
                <td class="text-center">
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteExpense(${exp.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

function deleteExpense(expenseId) {
    if (!confirm('Tem certeza que deseja excluir este gasto?')) return;

    const cardId = document.getElementById('currentCardId').value;

    axios.delete(`/api/credit-cards/${cardId}/expenses/${expenseId}`)
        .then(() => {
            loadExpenses(cardId);
            loadCreditCards(); // Reload cards to update balance
        })
        .catch(err => {
            console.error('Error deleting expense:', err);
            alert('Erro ao excluir gasto');
        });
}

document.getElementById('formExpense').onsubmit = function(e) {
    e.preventDefault();
    const cardId = document.getElementById('currentCardId').value;
    const data = Object.fromEntries(new FormData(e.target));

    axios.post(`/api/credit-cards/${cardId}/expenses`, data)
        .then(() => {
            loadExpenses(cardId);
            loadCreditCards(); // Reload cards to update balance
            e.target.reset();
            document.getElementById('expenseDate').value = new Date().toISOString().split('T')[0];
            document.getElementById('expenseInstallment').checked = false;
            toggleInstallmentFields();
        })
        .catch(err => {
            console.error('Error adding expense:', err);
            alert('Erro ao adicionar gasto: ' + (err.response?.data?.message || err.message));
        });
};

function deleteCreditCard(id) {
    if (!confirm('Tem certeza que deseja excluir este cartão?')) return;

    axios.delete(`/api/credit-cards/${id}`)
        .then(() => {
            loadCreditCards();
        })
        .catch(err => {
            console.error('Error deleting credit card:', err);
            alert('Erro ao excluir cartão');
        });
}

document.getElementById('formCreditCard').onsubmit = function(e) {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(e.target));
    const id = data.id;

    if (!data.current_balance) data.current_balance = 0;
    data.active = document.getElementById('creditCardActive').checked;

    const url = id ? `/api/credit-cards/${id}` : '/api/credit-cards';
    const method = id ? 'put' : 'post';

    axios[method](url, data)
        .then(() => {
            loadCreditCards();
            const modal = bootstrap.Modal.getInstance(document.getElementById('creditCardModal'));
            if (modal) modal.hide();
            e.target.reset();
            document.getElementById('editCreditCardId').value = '';
            document.getElementById('creditCardModalTitle').innerHTML = '<i class="bi bi-credit-card"></i> Novo Cartão';
            // Reset default dates
            const today = new Date();
            const nextMonth = new Date(today.getFullYear(), today.getMonth() + 1, 1);
            document.getElementById('creditCardClosingDate').value = new Date(nextMonth.getFullYear(), nextMonth.getMonth(), 5).toISOString().split('T')[0];
            document.getElementById('creditCardDueDate').value = new Date(nextMonth.getFullYear(), nextMonth.getMonth(), 10).toISOString().split('T')[0];
        })
        .catch(err => {
            console.error('Error saving credit card:', err);
            alert('Erro ao salvar cartão: ' + (err.response?.data?.message || err.message));
        });
};

// Reset modal title when opening for new card
document.getElementById('creditCardModal').addEventListener('show.bs.modal', function() {
    if (!document.getElementById('editCreditCardId').value) {
        document.getElementById('creditCardModalTitle').innerHTML = '<i class="bi bi-credit-card"></i> Novo Cartão';
    }
});

// Set default dates (closing: day 5, due: day 10)
const today = new Date();
const nextMonth = new Date(today.getFullYear(), today.getMonth() + 1, 1);
document.getElementById('creditCardClosingDate').value = new Date(nextMonth.getFullYear(), nextMonth.getMonth(), 5).toISOString().split('T')[0];
document.getElementById('creditCardDueDate').value = new Date(nextMonth.getFullYear(), nextMonth.getMonth(), 10).toISOString().split('T')[0];

loadAccounts();
loadCreditCards();
</script>
@endsection
