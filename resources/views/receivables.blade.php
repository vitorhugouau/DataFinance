@extends('layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-cash-coin"></i> A Receber</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#receivableModal" data-bs-backdrop="false">
        <i class="bi bi-plus-circle"></i> Novo Recebível
    </button>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted d-block mb-2">Total a Receber</small>
                    <h3 id="totalReceivable" class="mb-0" style="color: var(--accent-primary);">R$ 0,00</h3>
                </div>
                <div>
                    <i class="bi bi-cash-coin" style="font-size: 3rem; opacity: 0.3; color: var(--accent-primary);"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted d-block mb-2">Recebido</small>
                    <h3 id="totalPaid" class="mb-0" style="color: var(--success);">R$ 0,00</h3>
                </div>
                <div>
                    <i class="bi bi-check-circle" style="font-size: 3rem; opacity: 0.3; color: var(--success);"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted d-block mb-2">Atrasados</small>
                    <h3 id="totalOverdue" class="mb-0" style="color: var(--danger);">R$ 0,00</h3>
                </div>
                <div>
                    <i class="bi bi-exclamation-triangle" style="font-size: 3rem; opacity: 0.3; color: var(--danger);"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h5 class="mb-3">Lista de Recebíveis</h5>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Devedor</th>
                        <th>Descrição</th>
                        <th>Valor</th>
                        <th>Vencimento</th>
                        <th>Status</th>
                        <th>Dias Atraso</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody id="receivablesTable">
                    <tr><td colspan="7" class="text-center">Carregando...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="receivableModal" tabindex="-1" data-bs-backdrop="false">
    <div class="modal-dialog">
        <form id="formReceivable" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-cash-coin"></i> Novo Recebível</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="editReceivableId">
                <div class="mb-3">
                    <label class="form-label">Nome do Devedor</label>
                    <input name="debtor_name" id="receivableDebtorName" class="form-control" placeholder="Ex: João Silva" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Descrição</label>
                    <textarea name="description" id="receivableDescription" class="form-control" rows="2" placeholder="Descrição do que é devido"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Valor</label>
                    <input name="amount" id="receivableAmount" class="form-control" type="number" step="0.01" min="0.01" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Data de Vencimento</label>
                    <input name="due_date" id="receivableDueDate" class="form-control" type="date" required>
                </div>
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="paid" id="receivablePaid" onchange="togglePaidDate()">
                        <label class="form-check-label" for="receivablePaid">Já foi pago</label>
                    </div>
                </div>
                <div class="mb-3" id="paidDateWrap" style="display: none;">
                    <label class="form-label">Data de Pagamento</label>
                    <input name="paid_date" id="receivablePaidDate" class="form-control" type="date">
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
        
        .table thead th,
        .table tbody td {
            padding: 0.5rem 0.25rem;
        }
        
        .table tbody td:nth-child(2),
        .table tbody td:nth-child(5) {
            display: none;
        }
        
        .table thead th:nth-child(2),
        .table thead th:nth-child(5) {
            display: none;
        }
        
        .card h3 {
            font-size: 1.5rem;
        }
    }
    
    @media (max-width: 576px) {
        .table {
            font-size: 0.75rem;
        }
        
        .table tbody td:nth-child(6) {
            display: none;
        }
        
        .table thead th:nth-child(6) {
            display: none;
        }
        
        .card h3 {
            font-size: 1.25rem;
        }
    }
</style>
<script>
let receivables = [];

function formatCurrency(value) {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('pt-BR');
}

function loadReceivables() {
    axios.get('/api/receivables')
        .then(r => {
            receivables = r.data || [];
            renderReceivables();
            calculateTotals();
        })
        .catch(err => {
            console.error('Error loading receivables:', err);
            document.getElementById('receivablesTable').innerHTML = '<tr><td colspan="7" class="text-center text-danger">Erro ao carregar recebíveis</td></tr>';
        });
}

function renderReceivables() {
    const tbody = document.getElementById('receivablesTable');
    
    if (receivables.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Nenhum recebível cadastrado</td></tr>';
        return;
    }

    tbody.innerHTML = receivables.map(rec => {
        const isOverdue = rec.is_overdue || false;
        const daysOverdue = rec.days_overdue || 0;
        const statusBadge = rec.paid 
            ? '<span class="badge bg-success">Pago</span>' 
            : isOverdue 
                ? `<span class="badge bg-danger">Atrasado</span>` 
                : '<span class="badge bg-warning">Pendente</span>';
        
        const rowClass = isOverdue ? 'table-danger' : rec.paid ? 'table-success' : '';
        
        return `
            <tr class="${rowClass}">
                <td><strong>${rec.debtor_name}</strong></td>
                <td class="d-none d-md-table-cell">${rec.description || '-'}</td>
                <td class="text-end"><strong>${formatCurrency(parseFloat(rec.amount))}</strong></td>
                <td>${formatDate(rec.due_date)}</td>
                <td class="d-none d-md-table-cell">${statusBadge}</td>
                <td class="d-none d-sm-table-cell">${isOverdue ? `<span class="text-danger">${daysOverdue} dias</span>` : '-'}</td>
                <td class="text-center">
                    ${!rec.paid ? `
                        <button class="btn btn-sm btn-outline-success me-1" onclick="markAsPaid(${rec.id})" title="Marcar como pago">
                            <i class="bi bi-check"></i>
                        </button>
                    ` : ''}
                    <button class="btn btn-sm btn-outline-primary me-1" onclick="editReceivable(${rec.id})">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteReceivable(${rec.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

function calculateTotals() {
    const totalReceivable = receivables
        .filter(r => !r.paid)
        .reduce((sum, r) => sum + parseFloat(r.amount || 0), 0);
    
    const totalPaid = receivables
        .filter(r => r.paid)
        .reduce((sum, r) => sum + parseFloat(r.amount || 0), 0);
    
    const totalOverdue = receivables
        .filter(r => !r.paid && r.is_overdue)
        .reduce((sum, r) => sum + parseFloat(r.amount || 0), 0);
    
    document.getElementById('totalReceivable').innerText = formatCurrency(totalReceivable);
    document.getElementById('totalPaid').innerText = formatCurrency(totalPaid);
    document.getElementById('totalOverdue').innerText = formatCurrency(totalOverdue);
}

function editReceivable(id) {
    axios.get(`/api/receivables/${id}`)
        .then(r => {
            const rec = r.data;
            
            document.getElementById('editReceivableId').value = rec.id;
            document.getElementById('receivableDebtorName').value = rec.debtor_name || '';
            document.getElementById('receivableDescription').value = rec.description || '';
            document.getElementById('receivableAmount').value = rec.amount || 0;
            document.getElementById('receivableDueDate').value = rec.due_date || '';
            document.getElementById('receivablePaid').checked = rec.paid || false;
            document.getElementById('receivablePaidDate').value = rec.paid_date || '';
            
            togglePaidDate();
            
            const modal = new bootstrap.Modal(document.getElementById('receivableModal'), { backdrop: false });
            modal.show();
        })
        .catch(err => {
            console.error('Error loading receivable:', err);
            alert('Erro ao carregar recebível');
        });
}

function markAsPaid(id) {
    if (!confirm('Marcar este recebível como pago?')) return;
    
    axios.post(`/api/receivables/${id}/mark-as-paid`)
        .then(() => {
            loadReceivables();
        })
        .catch(err => {
            console.error('Error marking as paid:', err);
            alert('Erro ao marcar como pago');
        });
}

function deleteReceivable(id) {
    if (!confirm('Tem certeza que deseja excluir este recebível?')) return;
    
    axios.delete(`/api/receivables/${id}`)
        .then(() => {
            loadReceivables();
        })
        .catch(err => {
            console.error('Error deleting receivable:', err);
            alert('Erro ao excluir recebível');
        });
}

function togglePaidDate() {
    const isPaid = document.getElementById('receivablePaid').checked;
    document.getElementById('paidDateWrap').style.display = isPaid ? 'block' : 'none';
    if (isPaid && !document.getElementById('receivablePaidDate').value) {
        document.getElementById('receivablePaidDate').value = new Date().toISOString().split('T')[0];
    }
}

document.getElementById('formReceivable').onsubmit = function(e) {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(e.target));
    const id = data.id;
    
    if (!data.description) delete data.description;
    if (!data.paid_date) delete data.paid_date;
    data.paid = document.getElementById('receivablePaid').checked;
    
    const url = id ? `/api/receivables/${id}` : '/api/receivables';
    const method = id ? 'put' : 'post';
    
    axios[method](url, data)
        .then(() => {
            loadReceivables();
            const modal = bootstrap.Modal.getInstance(document.getElementById('receivableModal'));
            if (modal) modal.hide();
            e.target.reset();
            document.getElementById('editReceivableId').value = '';
            document.getElementById('paidDateWrap').style.display = 'none';
        })
        .catch(err => {
            console.error('Error saving receivable:', err);
            alert('Erro ao salvar recebível: ' + (err.response?.data?.message || err.message));
        });
};

// Set default due date (30 days from now)
const defaultDueDate = new Date();
defaultDueDate.setDate(defaultDueDate.getDate() + 30);
document.getElementById('receivableDueDate').value = defaultDueDate.toISOString().split('T')[0];

loadReceivables();
</script>
@endsection

