@extends('layout')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h2 class="mb-0"><i class="bi bi-bullseye"></i> Metas & Objetivos</h2>
        <small class="text-muted">Defina metas financeiras e acompanhe o progresso.</small>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#goalModal" data-bs-backdrop="false" onclick="openGoalModal()">
        <i class="bi bi-plus-circle"></i> Nova Meta
    </button>
</div>

<div class="row g-4 mb-4" id="goalsSummary">
    <div class="col-md-3">
        <div class="card">
            <small class="text-muted d-block mb-2">Metas Ativas</small>
            <h3 id="summaryGoalsCount" class="mb-0">0</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <small class="text-muted d-block mb-2">Valor Objetivo</small>
            <h3 id="summaryTargetAmount" class="mb-0" style="color: var(--accent-secondary);">R$ 0,00</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <small class="text-muted d-block mb-2">Já Poupado</small>
            <h3 id="summarySavedAmount" class="mb-0" style="color: var(--accent-primary);">R$ 0,00</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <small class="text-muted d-block mb-2">Média de Progresso</small>
            <h3 id="summaryAverageProgress" class="mb-0">0%</h3>
        </div>
    </div>
</div>

<div class="row g-4" id="goalsList">
    <div class="col-12">
        <div class="card text-center text-muted">
            <p class="mb-0">Carregando metas...</p>
        </div>
    </div>
</div>

<div class="modal fade" id="goalModal" tabindex="-1" data-bs-backdrop="false">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" id="goalForm">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-bullseye"></i> <span id="goalModalTitle">Nova Meta</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="goalId" name="id">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Título da Meta</label>
                        <input type="text" class="form-control" id="goalTitle" name="title" placeholder="Ex: Viagem, Reserva de emergência..." required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Categoria</label>
                        <input type="text" class="form-control" id="goalCategory" name="category" placeholder="Ex: Viagem">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Valor Objetivo</label>
                        <input type="number" class="form-control" step="0.01" min="0.01" id="goalTarget" name="target_amount" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Já Poupado</label>
                        <input type="number" class="form-control" step="0.01" min="0" id="goalCurrent" name="current_amount" value="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Data Limite</label>
                        <input type="date" class="form-control" id="goalDueDate" name="due_date">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Prioridade</label>
                        <select class="form-select" id="goalPriority" name="priority">
                            <option value="low">Baixa</option>
                            <option value="medium" selected>Média</option>
                            <option value="high">Alta</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="goalStatus" name="status">
                            <option value="active" selected>Ativa</option>
                            <option value="paused">Pausada</option>
                            <option value="completed">Concluída</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label d-block">Cor</label>
                        <input type="color" class="form-control form-control-color" id="goalColor" name="color" value="#0dcaf0">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Descrição</label>
                        <textarea class="form-control" id="goalDescription" name="description" rows="2" placeholder="Detalhes da meta..."></textarea>
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

<div class="modal fade" id="goalProgressModal" tabindex="-1" data-bs-backdrop="false">
    <div class="modal-dialog">
        <form class="modal-content" id="goalProgressForm">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-graph-up"></i> Atualizar Progresso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="progressGoalId">
                <div class="mb-3">
                    <label class="form-label">Valor</label>
                    <input type="number" class="form-control" id="progressAmount" step="0.01" min="0" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Operação</label>
                    <select class="form-select" id="progressOperation">
                        <option value="add">Somar ao valor atual</option>
                        <option value="set">Definir como valor atual</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> Atualizar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<style>
    .goal-card {
        border: 1px solid var(--border-color);
        border-radius: 1.25rem;
        padding: 1.5rem;
        background: var(--bg-card);
        position: relative;
        overflow: hidden;
    }

    .goal-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(13, 202, 240, 0.08), rgba(124, 58, 237, 0.08));
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .goal-card:hover::before {
        opacity: 1;
    }

    .goal-progress {
        height: 8px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.1);
        overflow: hidden;
    }

    .goal-progress-bar {
        height: 100%;
        transition: width 0.4s ease;
        background: linear-gradient(90deg, var(--accent-primary), var(--accent-secondary));
    }

    @media (max-width: 768px) {
        .goal-card {
            padding: 1rem;
        }
    }
</style>
<script>
let goals = [];

function formatCurrency(value) {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value ?? 0);
}

function formatDate(dateString) {
    if (!dateString) return 'Sem prazo';
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-BR', { day: '2-digit', month: 'short', year: 'numeric' });
}

function loadGoals() {
    axios.get('/api/goals')
        .then(r => {
            goals = r.data || [];
            updateGoalsSummary();
            renderGoals();
        })
        .catch(() => {
            document.getElementById('goalsList').innerHTML = `
                <div class="col-12">
                    <div class="card text-center text-danger">Erro ao carregar metas</div>
                </div>
            `;
        });
}

function updateGoalsSummary() {
    const activeGoals = goals.filter(goal => goal.status !== 'completed');
    const totalTarget = activeGoals.reduce((sum, goal) => sum + parseFloat(goal.target_amount ?? 0), 0);
    const totalSaved = activeGoals.reduce((sum, goal) => sum + parseFloat(goal.current_amount ?? 0), 0);
    const avgProgress = activeGoals.length ? activeGoals.reduce((sum, goal) => sum + (goal.progress ?? 0), 0) / activeGoals.length : 0;

    document.getElementById('summaryGoalsCount').innerText = activeGoals.length;
    document.getElementById('summaryTargetAmount').innerText = formatCurrency(totalTarget);
    document.getElementById('summarySavedAmount').innerText = formatCurrency(totalSaved);
    document.getElementById('summaryAverageProgress').innerText = `${avgProgress.toFixed(1)}%`;
}

function renderGoals() {
    const container = document.getElementById('goalsList');
    if (!goals.length) {
        container.innerHTML = `
            <div class="col-12">
                <div class="card text-center text-muted">
                    <p class="mb-0">Nenhuma meta cadastrada ainda.</p>
                </div>
            </div>
        `;
        return;
    }

    container.innerHTML = goals.map(goal => {
        const progress = Math.min(goal.progress ?? 0, 100);
        const remaining = formatCurrency(goal.remaining_amount ?? 0);
        const statusBadge = goal.status === 'completed'
            ? '<span class="badge bg-success">Concluída</span>'
            : goal.status === 'paused'
                ? '<span class="badge bg-warning text-dark">Pausada</span>'
                : '<span class="badge bg-info text-dark">Ativa</span>';
        const dueDate = goal.due_date ? formatDate(goal.due_date) : 'Sem prazo';

        return `
            <div class="col-12 col-md-6 col-xl-4">
                <div class="goal-card" style="border-top: 3px solid ${goal.color || 'var(--accent-primary)'};">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="mb-1">${goal.title}</h5>
                            <small class="text-muted">${goal.category ?? 'Sem categoria'}</small>
                        </div>
                        ${statusBadge}
                    </div>
                    <p class="text-muted">${goal.description ?? ''}</p>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <small class="text-muted">Progresso</small>
                            <small class="fw-bold">${progress}%</small>
                        </div>
                        <div class="goal-progress mt-1">
                            <div class="goal-progress-bar" style="width: ${progress}%; background: ${goal.color || 'var(--accent-primary)'};"></div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <small class="text-muted d-block">Poupado</small>
                            <strong>${formatCurrency(goal.current_amount)}</strong>
                        </div>
                        <div>
                            <small class="text-muted d-block">Falta</small>
                            <strong>${remaining}</strong>
                        </div>
                        <div>
                            <small class="text-muted d-block">Objetivo</small>
                            <strong>${formatCurrency(goal.target_amount)}</strong>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted d-block">Prazo</small>
                            <strong>${dueDate}</strong>
                        </div>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-success" onclick="openProgressModal(${goal.id})">
                                <i class="bi bi-graph-up"></i>
                            </button>
                            <button class="btn btn-outline-primary" onclick="openGoalModal(${goal.id})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-outline-danger" onclick="deleteGoal(${goal.id})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

function openGoalModal(id = null) {
    document.getElementById('goalForm').reset();
    document.getElementById('goalId').value = id ?? '';
    document.getElementById('goalModalTitle').innerText = id ? 'Editar Meta' : 'Nova Meta';
    document.getElementById('goalPriority').value = 'medium';
    document.getElementById('goalStatus').value = 'active';
    document.getElementById('goalColor').value = '#0dcaf0';

    if (id) {
        axios.get(`/api/goals/${id}`)
            .then(r => {
                const goal = r.data;
                document.getElementById('goalTitle').value = goal.title || '';
                document.getElementById('goalCategory').value = goal.category || '';
                document.getElementById('goalTarget').value = goal.target_amount || 0;
                document.getElementById('goalCurrent').value = goal.current_amount || 0;
                document.getElementById('goalDueDate').value = goal.due_date || '';
                document.getElementById('goalPriority').value = goal.priority || 'medium';
                document.getElementById('goalStatus').value = goal.status || 'active';
                document.getElementById('goalColor').value = goal.color || '#0dcaf0';
                document.getElementById('goalDescription').value = goal.description || '';

                new bootstrap.Modal(document.getElementById('goalModal'), { backdrop: false }).show();
            });
    }
}

document.getElementById('goalForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const id = document.getElementById('goalId').value;
    const payload = {
        title: document.getElementById('goalTitle').value,
        category: document.getElementById('goalCategory').value || null,
        target_amount: document.getElementById('goalTarget').value,
        current_amount: document.getElementById('goalCurrent').value || 0,
        due_date: document.getElementById('goalDueDate').value || null,
        priority: document.getElementById('goalPriority').value,
        status: document.getElementById('goalStatus').value,
        color: document.getElementById('goalColor').value,
        description: document.getElementById('goalDescription').value || null,
    };

    const method = id ? 'put' : 'post';
    const url = id ? `/api/goals/${id}` : '/api/goals';

    axios[method](url, payload)
        .then(() => {
            bootstrap.Modal.getInstance(document.getElementById('goalModal'))?.hide();
            loadGoals();
        })
        .catch(err => alert('Erro ao salvar meta: ' + (err.response?.data?.message || err.message)));
});

function deleteGoal(id) {
    if (!confirm('Deseja realmente excluir esta meta?')) return;
    axios.delete(`/api/goals/${id}`)
        .then(loadGoals)
        .catch(err => alert('Erro ao excluir meta: ' + (err.response?.data?.message || err.message)));
}

function openProgressModal(id) {
    const goal = goals.find(g => g.id === id);
    if (!goal) return;
    document.getElementById('progressGoalId').value = id;
    document.getElementById('progressAmount').value = '';
    document.getElementById('progressOperation').value = 'add';
    new bootstrap.Modal(document.getElementById('goalProgressModal'), { backdrop: false }).show();
}

document.getElementById('goalProgressForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const id = document.getElementById('progressGoalId').value;
    const payload = {
        amount: document.getElementById('progressAmount').value,
        operation: document.getElementById('progressOperation').value,
    };

    axios.post(`/api/goals/${id}/progress`, payload)
        .then(() => {
            bootstrap.Modal.getInstance(document.getElementById('goalProgressModal'))?.hide();
            loadGoals();
        })
        .catch(err => alert('Erro ao atualizar progresso: ' + (err.response?.data?.message || err.message)));
});

loadGoals();
</script>
@endsection


