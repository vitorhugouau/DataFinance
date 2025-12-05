@extends('layout')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h2 class="mb-0"><i class="bi bi-receipt-cutoff"></i> Gastos Fixos</h2>
        <small class="text-muted">Gerencie assinaturas, contas e despesas recorrentes.</small>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#fixedExpenseModal" data-bs-backdrop="false" onclick="openFixedExpenseModal()">
        <i class="bi bi-plus-circle"></i> Novo Gasto Fixo
    </button>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card">
            <small class="text-muted d-block mb-2">Total Mensal</small>
            <h3 id="totalFixedExpenses" class="mb-0" style="color: var(--accent-primary);">R$ 0,00</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <small class="text-muted d-block mb-2">Próximo Vencimento</small>
            <h3 id="nextDueExpense" class="mb-0">--</h3>
            <small class="text-muted" id="nextDueDateLabel"></small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <small class="text-muted d-block mb-2">Débito Automático</small>
            <h3 id="autoDebitCount" class="mb-0">0 itens</h3>
        </div>
    </div>
</div>

<div class="card">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
        <h5 class="mb-3 mb-md-0"><i class="bi bi-list-ul"></i> Lista de Gastos Fixos</h5>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm" onclick="loadFixedExpenses()">
                <i class="bi bi-arrow-clockwise"></i> Atualizar
            </button>
        </div>
    </div>
    <div class="table-responsive mt-3">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Valor</th>
                    <th>Vencimento</th>
                    <th>Frequência</th>
                    <th>Conta</th>
                    <th>Categoria</th>
                    <th>Status</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody id="fixedExpensesTable">
                <tr>
                    <td colspan="8" class="text-center text-muted">Carregando...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="fixedExpenseModal" tabindex="-1" data-bs-backdrop="false">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" id="fixedExpenseForm">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-receipt-cutoff"></i> <span id="fixedExpenseModalTitle">Novo Gasto Fixo</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="fixedExpenseId" name="id">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nome</label>
                        <input type="text" class="form-control" id="fixedExpenseName" name="name" placeholder="Ex: Aluguel, Netflix..." required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Valor</label>
                        <input type="number" step="0.01" min="0.01" class="form-control" id="fixedExpenseAmount" name="amount" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Data de Vencimento</label>
                        <input type="date" class="form-control" id="fixedExpenseDueDate" name="due_date" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Conta</label>
                        <select class="form-select" id="fixedExpenseAccount" name="account_id">
                            <option value="">Selecione uma conta...</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Categoria</label>
                        <select class="form-select" id="fixedExpenseCategory" name="category_id">
                            <option value="">Selecione uma categoria...</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Frequência</label>
                        <select class="form-select" id="fixedExpenseFrequency" name="frequency" required>
                            <option value="monthly">Mensal</option>
                            <option value="biweekly">Quinzenal</option>
                            <option value="weekly">Semanal</option>
                            <option value="quarterly">Trimestral</option>
                            <option value="yearly">Anual</option>
                            <option value="custom">Outro</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Moeda</label>
                        <select class="form-select" id="fixedExpenseCurrency" name="currency" required>
                            <option value="BRL">BRL</option>
                            <option value="USD">USD</option>
                            <option value="EUR">EUR</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Descrição</label>
                        <textarea class="form-control" id="fixedExpenseDescription" name="description" rows="2" placeholder="Detalhes adicionais..."></textarea>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="fixedExpenseAuto" name="auto_debit">
                            <label class="form-check-label" for="fixedExpenseAuto">Débito automático</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="fixedExpenseActive" name="active" checked>
                            <label class="form-check-label" for="fixedExpenseActive">Gasto ativo</label>
                        </div>
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
            font-size: 0.85rem;
        }

        .table thead {
            display: none;
        }

        .table tbody tr {
            display: block;
            margin-bottom: 1rem;
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            padding: 1rem;
            background: var(--bg-card);
        }

        .table tbody td {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border: none;
        }

        .table tbody td::before {
            content: attr(data-label);
            font-weight: 600;
            color: var(--text-secondary);
        }
    }
</style>
<script>
    let fixedExpenses = [];
    let accounts = [];
    let categories = [];

    function formatCurrency(value) {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(value ?? 0);
    }

    function formatDate(dateString) {
        return new Date(dateString).toLocaleDateString('pt-BR', {
            day: '2-digit',
            month: 'short'
        });
    }

    function loadFixedExpenses() {
        axios.get('/api/fixed-expenses')
            .then(r => {
                fixedExpenses = r.data || [];
                renderFixedExpenses();
                updateFixedExpenseCards();
            })
            .catch(() => {
                document.getElementById('fixedExpensesTable').innerHTML = '<tr><td colspan="8" class="text-center text-danger">Erro ao carregar gastos fixos</td></tr>';
            });
    }

    function loadAccounts() {
        axios.get('/api/accounts')
            .then(r => {
                accounts = r.data || [];
                const select = document.getElementById('fixedExpenseAccount');
                if (!select) return;
                select.innerHTML = '<option value=\"\">Selecione uma conta...</option>';
                accounts.forEach(acc => {
                    select.innerHTML += `<option value=\"${acc.id}\">${acc.name}</option>`;
                });
            });
    }

    function loadCategories() {
        axios.get('/api/categories')
            .then(r => {
                categories = r.data || [];
                const select = document.getElementById('fixedExpenseCategory');
                if (!select) return;
                select.innerHTML = '<option value=\"\">Selecione uma categoria...</option>';
                categories.forEach(cat => {
                    select.innerHTML += `<option value=\"${cat.id}\">${cat.name}</option>`;
                    (cat.children || []).forEach(sub => {
                        select.innerHTML += `<option value=\"${sub.id}\">└ ${sub.name}</option>`;
                    });
                });
            });
    }

    function renderFixedExpenses() {
    const tbody = document.getElementById('fixedExpensesTable');
    if (fixedExpenses.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Nenhum gasto fixo cadastrado</td></tr>';
        return;
    }

    tbody.innerHTML = fixedExpenses.map(exp => {
        const statusBadge = exp.active ?
            '<span class="badge bg-success">Ativo</span>' :
            '<span class="badge bg-danger-subtle text-danger">Inativo</span>';

        const overdueClass = exp.is_overdue ? 'text-danger' : '';
        const dueLabel = exp.is_overdue ? 'Atrasado' :
            exp.days_until_due >= 0 ? `${exp.days_until_due} dias` : '--';

        const accountName = exp.account?.name ?? '-';
        const categoryName = exp.category?.name ?? '-';

        // 👉 estilo para células de linha inativa
        const cellStyle = exp.active ? '' : 'style="background-color:#ffe5e5;"';

        return `
            <tr>
                <td ${cellStyle} data-label="Nome">
                    <strong>${exp.name}</strong><br>
                    <small class="text-muted">${exp.description ?? ''}</small>
                </td>
                <td ${cellStyle} data-label="Valor" class="text-end"><strong>${formatCurrency(exp.amount)}</strong></td>
                <td ${cellStyle} data-label="Vencimento" class="${overdueClass}">
                    ${formatDate(exp.due_date)}<br>
                    <small>${dueLabel}</small>
                </td>
                <td ${cellStyle} data-label="Frequência">${frequencyLabel(exp.frequency)}</td>
                <td ${cellStyle} data-label="Conta">${accountName}</td>
                <td ${cellStyle} data-label="Categoria">${categoryName}</td>
                <td ${cellStyle} data-label="Status">
                    ${statusBadge}
                    ${exp.auto_debit ? '<span class="badge bg-info ms-1">Auto</span>' : ''}
                </td>
                <td ${cellStyle} data-label="Ações" class="text-center">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-success" title="Marcar como pago" onclick="markFixedExpensePaid(${exp.id})">
                            <i class="bi bi-check2-circle"></i>
                        </button>
                        <button class="btn btn-outline-primary" onclick="openFixedExpenseModal(${exp.id})">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-outline-danger" onclick="deleteFixedExpense(${exp.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}


    function frequencyLabel(freq) {
        return ({
            weekly: 'Semanal',
            biweekly: 'Quinzenal',
            monthly: 'Mensal',
            quarterly: 'Trimestral',
            yearly: 'Anual',
            custom: 'Personalizada'
        })[freq] ?? 'Mensal';
    }

    function updateFixedExpenseCards() {
        const total = fixedExpenses
            .filter(exp => exp.active)
            .reduce((sum, exp) => sum + parseFloat(exp.amount ?? 0), 0);

        document.getElementById('totalFixedExpenses').innerText = formatCurrency(total);

        const autoDebit = fixedExpenses.filter(exp => exp.auto_debit).length;
        document.getElementById('autoDebitCount').innerText = `${autoDebit} ${autoDebit === 1 ? 'item' : 'itens'}`;

        const activeExpenses = fixedExpenses
            .filter(exp => exp.active && exp.due_date)
            .sort((a, b) => new Date(a.due_date) - new Date(b.due_date));

        if (activeExpenses.length === 0) {
            document.getElementById('nextDueExpense').innerText = '--';
            document.getElementById('nextDueDateLabel').innerText = '';
            return;
        }

        const next = activeExpenses[0];
        document.getElementById('nextDueExpense').innerText = next.name;
        document.getElementById('nextDueDateLabel').innerText =
            `Vence em ${new Date(next.due_date).toLocaleDateString('pt-BR')}`;
    }


    function openFixedExpenseModal(id = null) {
        const modalTitle = document.getElementById('fixedExpenseModalTitle');
        const form = document.getElementById('fixedExpenseForm');
        form.reset();
        document.getElementById('fixedExpenseId').value = id ?? '';
        document.getElementById('fixedExpenseActive').checked = true;
        document.getElementById('fixedExpenseAuto').checked = false;
        setDefaultDueDate();

        if (id) {
            modalTitle.innerText = 'Editar Gasto Fixo';
            axios.get(`/api/fixed-expenses/${id}`)
                .then(r => {
                    const exp = r.data;
                    document.getElementById('fixedExpenseName').value = exp.name || '';
                    document.getElementById('fixedExpenseAmount').value = exp.amount || '';
                    document.getElementById('fixedExpenseDueDate').value = exp.due_date || '';
                    document.getElementById('fixedExpenseDescription').value = exp.description || '';
                    document.getElementById('fixedExpenseFrequency').value = exp.frequency || 'monthly';
                    document.getElementById('fixedExpenseCurrency').value = exp.currency || 'BRL';
                    document.getElementById('fixedExpenseAccount').value = exp.account_id || '';
                    document.getElementById('fixedExpenseCategory').value = exp.category_id || '';
                    document.getElementById('fixedExpenseAuto').checked = !!exp.auto_debit;
                    document.getElementById('fixedExpenseActive').checked = exp.active ?? true;

                    const modal = new bootstrap.Modal(document.getElementById('fixedExpenseModal'), {
                        backdrop: false
                    });
                    modal.show();
                });
        } else {
            modalTitle.innerText = 'Novo Gasto Fixo';
        }
    }

    function setDefaultDueDate() {
        const dueDateInput = document.getElementById('fixedExpenseDueDate');
        if (!dueDateInput.value) {
            const nextMonth = new Date();
            dueDateInput.value = nextMonth.toISOString().substring(0, 10);
        }
    }

    document.getElementById('fixedExpenseForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('fixedExpenseId').value;
        const data = {
            name: document.getElementById('fixedExpenseName').value,
            amount: document.getElementById('fixedExpenseAmount').value,
            due_date: document.getElementById('fixedExpenseDueDate').value,
            account_id: document.getElementById('fixedExpenseAccount').value || null,
            category_id: document.getElementById('fixedExpenseCategory').value || null,
            description: document.getElementById('fixedExpenseDescription').value || null,
            frequency: document.getElementById('fixedExpenseFrequency').value,
            currency: document.getElementById('fixedExpenseCurrency').value,
            auto_debit: document.getElementById('fixedExpenseAuto').checked,
            active: document.getElementById('fixedExpenseActive').checked,
        };

        const method = id ? 'put' : 'post';
        const url = id ? `/api/fixed-expenses/${id}` : '/api/fixed-expenses';

        axios[method](url, data)
            .then(() => {
                loadFixedExpenses();
                bootstrap.Modal.getInstance(document.getElementById('fixedExpenseModal'))?.hide();
            })
            .catch(err => {
                alert('Erro ao salvar gasto: ' + (err.response?.data?.message || err.message));
            });
    });

    function deleteFixedExpense(id) {
        if (!confirm('Deseja realmente excluir este gasto fixo?')) return;
        axios.delete(`/api/fixed-expenses/${id}`)
            .then(loadFixedExpenses)
            .catch(err => alert('Erro ao excluir: ' + (err.response?.data?.message || err.message)));
    }

    function markFixedExpensePaid(id) {
        axios.post(`/api/fixed-expenses/${id}/mark-paid`)
            .then(loadFixedExpenses)
            .catch(err => alert('Erro ao marcar pagamento: ' + (err.response?.data?.message || err.message)));
    }

    loadAccounts();
    loadCategories();
    loadFixedExpenses();
</script>
@endsection
