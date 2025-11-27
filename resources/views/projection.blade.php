@extends('layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-calculator"></i> Projeção Financeira</h2>
    <div>
        <input type="month" id="projectionMonth" class="form-control" value="{{ date('Y-m', strtotime('+1 month')) }}" style="max-width: 200px; display: inline-block;">
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted d-block mb-2">Saldo Atual</small>
                    <h3 id="currentBalance" class="mb-0" style="color: var(--accent-primary);">R$ 0,00</h3>
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
                    <small class="text-muted d-block mb-2">Receitas Esperadas</small>
                    <h3 id="expectedIncome" class="mb-0" style="color: var(--success);">R$ 0,00</h3>
                </div>
                <div>
                    <i class="bi bi-arrow-up-circle" style="font-size: 3rem; opacity: 0.3; color: var(--success);"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted d-block mb-2">Saldo Projetado</small>
                    <h3 id="projectedBalance" class="mb-0">R$ 0,00</h3>
                </div>
                <div>
                    <i class="bi bi-graph-up" style="font-size: 3rem; opacity: 0.3; color: var(--accent-secondary);"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4 card-static">
    <div class="card-body">
        <h5 class="mb-3"><i class="bi bi-sliders"></i> Opções de Cálculo</h5>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="subtractCreditCards" onchange="toggleCreditCardSource()">
                    <label class="form-check-label text-white" for="subtractCreditCards">
                        Subtrair Cartões de Crédito
                    </label>
                </div>
                <div id="creditCardSourceWrap" style="display: none; margin-top: 0.5rem;">
                    <label class="form-label small text-white">Subtrair de:</label>
                    <select id="creditCardSource" class="form-select form-select-sm" onchange="calculateProjection()">
                        <option value="projection">Projeção (padrão)</option>
                        <option value="investment">Investimentos</option>
                        <option value="account">Saldo da Conta</option>
                    </select>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="subtractInvestments" onchange="calculateProjection()">
                    <label class="form-check-label text-white" for="subtractInvestments">
                        Subtrair Investimentos
                    </label>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="useAccountBalance" onchange="toggleAccountSelect()">
                    <label class="form-check-label text-white" for="useAccountBalance">
                        Usar Saldo de Conta Específica
                    </label>
                </div>
            </div>

            <div class="col-md-12" id="accountSelectWrap" style="display: none;">
                <label class="form-label">Selecionar Conta</label>
                <select id="selectedAccount" class="form-select" onchange="calculateProjection()">
                    <option value="">Todas as contas</option>
                </select>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
            <div>
                <h5 class="mb-1"><i class="bi bi-link-45deg"></i> Combinações Personalizadas</h5>
                <small class="text-muted">Monte cenários somando ou subtraindo valores para ver o resultado final.</small>
            </div>
            <button class="btn btn-outline-primary" type="button" onclick="addCustomOperation()">
                <i class="bi bi-plus-circle"></i> Adicionar combinação
            </button>
        </div>
        <div id="customOperationsContainer" class="custom-operations"></div>
        <div id="customResults" class="mt-4"></div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
            <div>
                <h5 class="mb-1"><i class="bi bi-calculator"></i> Cálculos Adicionais</h5>
                <small class="text-muted">Use os resultados das combinações personalizadas ou valores específicos para criar novos cálculos.</small>
            </div>
            <button class="btn btn-outline-primary" type="button" onclick="addAdditionalCalculation()">
                <i class="bi bi-plus-circle"></i> Adicionar cálculo
            </button>
        </div>
        <div id="additionalCalculationsContainer" class="additional-calculations"></div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card">
            <h5 class="mb-3"><i class="bi bi-list-check"></i> Selecionar Gastos</h5>
            <div class="mb-3">
                <label class="form-label">Categoria</label>
                <select id="expenseCategory" class="form-select">
                    <option value="">Selecione uma categoria...</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Descrição</label>
                <input type="text" id="expenseDescription" class="form-control" placeholder="Ex: Aluguel, Supermercado...">
            </div>
            <div class="mb-3">
                <label class="form-label">Valor</label>
                <input type="number" id="expenseAmount" class="form-control" step="0.01" min="0.01" placeholder="0.00">
            </div>
            <button class="btn btn-primary w-100" onclick="addExpense()">
                <i class="bi bi-plus-circle"></i> Adicionar Gasto
            </button>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <h5 class="mb-3"><i class="bi bi-cash-stack"></i> Gastos Selecionados</h5>
            <div id="selectedExpenses" class="mb-3">
                <p class="text-muted text-center">Nenhum gasto selecionado</p>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <strong>Total: <span id="totalExpenses">R$ 0,00</span></strong>
                <button class="btn btn-outline-danger btn-sm" onclick="clearExpenses()">
                    <i class="bi bi-trash"></i> Limpar
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-body">
        <h5 class="mb-3"><i class="bi bi-info-circle"></i> Resumo da Projeção</h5>
        <div id="projectionSummary"></div>
    </div>
</div>
@endsection

@section('scripts')
<style>
    @media (max-width: 768px) {
        .card h3 {
            font-size: 1.5rem;
        }

        #projectionMonth {
            max-width: 100% !important;
            width: 100%;
            margin-top: 0.5rem;
        }

        .d-flex.justify-content-between {
            flex-direction: column;
        }
    }

    @media (max-width: 576px) {
        .card h3 {
            font-size: 1.25rem;
        }

        h2 {
            font-size: 1.5rem;
        }
    }

    .custom-operation-row {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 0.75rem;
        padding: 1rem;
        margin-bottom: 1rem;
        position: relative;
        z-index: 2000;
        overflow: visible !important;
    }

    .custom-operation-row label {
        font-size: 0.85rem;
    }
    
    .additional-calculation-row {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        transition: all 0.2s ease;
    }
    
    .additional-calculation-row:hover {
        border-color: var(--accent-primary);
        box-shadow: 0 2px 8px rgba(0, 212, 255, 0.1);
    }

    @media (max-width: 768px) {
        .custom-operation-row {
            padding: 0.75rem;
        }
    }

    .card.card-static {
        transition: none !important;
        transform: none !important;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3) !important;
        border-color: var(--border-color) !important;
    }

    .card.card-static::before,
    .card.card-static:hover::before {
        opacity: 0 !important;
    }

    .card.card-static:hover {
        transform: none !important;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3) !important;
        border-color: var(--border-color) !important;
    }

    /* Modern Multi-Select Styles */
    .modern-multiselect {
        position: relative;
        z-index: 5000;
    }

    #customOperationsContainer {
        position: relative;
        z-index: 1500;
        overflow: visible !important;
    }

    .card-body {
        overflow: visible !important;
        position: relative;
        z-index: 1000;
    }

    .card {
        overflow: visible !important;
        position: relative;
    }

    .modern-multiselect-trigger {
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        transition: all 0.3s ease;
        min-height: 48px;
        position: relative;
    }

    .modern-multiselect-trigger:hover {
        border-color: var(--accent-primary);
        background: var(--bg-secondary);
    }

    .modern-multiselect-trigger.active {
        border-color: var(--accent-primary);
        box-shadow: 0 0 0 0.2rem rgba(var(--accent-primary-rgb), 0.25);
    }

    .modern-multiselect-trigger-text {
        flex: 1;
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        align-items: center;
        min-height: 24px;
    }

    .modern-multiselect-trigger-text.empty {
        color: var(--text-muted);
    }

    .modern-multiselect-trigger-text.empty span {
        display: inline-block;
        line-height: 1.5;
    }

    .modern-multiselect-trigger-icon {
        color: var(--text-muted);
        transition: transform 0.3s ease;
        margin-top: 2px;
        flex-shrink: 0;
    }

    .modern-multiselect-trigger.active .modern-multiselect-trigger-icon {
        transform: rotate(180deg);
    }

    .modern-multiselect-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        margin-top: 0.5rem;
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 0.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        z-index: 20000;
        max-height: 300px;
        overflow-y: auto;
        display: none;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .modern-multiselect-dropdown.show {
        display: block;
        opacity: 1;
    }

    .modern-multiselect-item {
        padding: 0.75rem 1rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        transition: background 0.2s ease;
        border-bottom: 1px solid var(--border-color);
    }

    .modern-multiselect-item:last-child {
        border-bottom: none;
    }

    .modern-multiselect-item:hover {
        background: var(--bg-secondary);
    }

    .modern-multiselect-checkbox {
        width: 20px;
        height: 20px;
        border: 2px solid var(--border-color);
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }

    .modern-multiselect-item.selected .modern-multiselect-checkbox {
        background: var(--accent-primary);
        border-color: var(--accent-primary);
    }

    .modern-multiselect-item.selected .modern-multiselect-checkbox::after {
        content: '✓';
        color: white;
        font-size: 14px;
        font-weight: bold;
    }

    .modern-multiselect-item-label {
        flex: 1;
        font-size: 0.9rem;
    }

    .selected-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: var(--accent-primary);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .selected-chip-remove {
        cursor: pointer;
        font-size: 1rem;
        line-height: 1;
        opacity: 0.8;
        transition: opacity 0.2s;
    }

    .selected-chip-remove:hover {
        opacity: 1;
    }

    @media (max-width: 768px) {
        .modern-multiselect-dropdown {
            max-height: 250px;
        }
    }
</style>
<script>
    let selectedExpenses = [];
    let categories = [];
    let accounts = [];
    let fixedExpenses = [];
    let investments = [];
    let metricOptions = [{
            value: 'current_balance',
            label: 'Saldo Atual (Contas)'
        },
        {
            value: 'expected_income',
            label: 'Receitas Esperadas'
        },
        {
            value: 'total_expenses',
            label: 'Gastos Selecionados'
        },
        {
            value: 'credit_cards_balance',
            label: 'Cartões de Crédito'
        },
        {
            value: 'investments_total',
            label: 'Investimentos'
        },
        {
            value: 'fixed_expenses_total',
            label: 'Gastos Fixos (Total)'
        },
        {
            value: 'projected_balance',
            label: 'Saldo Projetado'
        },
    ];
    let metricLabels = metricOptions.reduce((acc, option) => {
        acc[option.value] = option.label;
        return acc;
    }, {});
    
    // Load custom operations from localStorage
    function loadCustomOperationsFromStorage() {
        try {
            const saved = localStorage.getItem('projection_custom_operations');
            if (saved) {
                const parsed = JSON.parse(saved);
                if (Array.isArray(parsed)) {
                    return parsed;
                }
            }
        } catch (e) {
            console.error('Error loading custom operations from storage:', e);
        }
        // Default if nothing saved
        return [{
            apply_to: 'investments_total',
            operation: 'subtract',
            values: ['credit_cards_balance'],
        }];
    }
    
    // Save custom operations to localStorage
    function saveCustomOperationsToStorage() {
        try {
            localStorage.setItem('projection_custom_operations', JSON.stringify(customOperations));
        } catch (e) {
            console.error('Error saving custom operations to storage:', e);
        }
    }
    
    let customOperations = loadCustomOperationsFromStorage();
    
    // Additional calculations storage
    function loadAdditionalCalculationsFromStorage() {
        try {
            const saved = localStorage.getItem('projection_additional_calculations');
            if (saved) {
                const parsed = JSON.parse(saved);
                if (Array.isArray(parsed)) {
                    return parsed;
                }
            }
        } catch (e) {
            console.error('Error loading additional calculations from storage:', e);
        }
        return [];
    }
    
    function saveAdditionalCalculationsToStorage() {
        try {
            localStorage.setItem('projection_additional_calculations', JSON.stringify(additionalCalculations));
        } catch (e) {
            console.error('Error saving additional calculations to storage:', e);
        }
    }
    
    let additionalCalculations = loadAdditionalCalculationsFromStorage();
    let savedCustomResults = {}; // Store results from custom operations

    function formatCurrency(value) {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(value);
    }

    function loadCategories() {
        return axios.get('/api/projection/categories')
            .then(r => {
                categories = r.data || [];
                const select = document.getElementById('expenseCategory');
                select.innerHTML = '<option value="">Selecione uma categoria...</option>';

                categories.forEach(cat => {
                    select.innerHTML += `<option value="${cat.id}">${cat.name}</option>`;
                    if (cat.children && cat.children.length > 0) {
                        cat.children.forEach(subcat => {
                            select.innerHTML += `<option value="${subcat.id}">  └ ${subcat.name}</option>`;
                        });
                    }
                });

                // Load fixed expenses and add them to the select
                return loadFixedExpenses();
            })
            .catch(err => {
                console.error('Error loading categories:', err);
                // Still try to load fixed expenses
                return loadFixedExpenses();
            });
    }

    function loadFixedExpenses() {
        return axios.get('/api/projection/fixed-expenses')
            .then(r => {
                fixedExpenses = r.data || [];
                const select = document.getElementById('expenseCategory');

                if (fixedExpenses.length > 0) {
                    select.innerHTML += '<optgroup label="━━━ Gastos Fixos ━━━">';
                    fixedExpenses.forEach(expense => {
                        const displayName = expense.name + (expense.category_name ? ` (${expense.category_name})` : '') + ` - ${formatCurrency(expense.amount)}`;
                        select.innerHTML += `<option value="fixed_expense_${expense.id}">${displayName}</option>`;
                    });
                    select.innerHTML += '</optgroup>';
                }

                // Add individual fixed expenses to metricOptions for custom combinations
                fixedExpenses.forEach(expense => {
                    metricOptions.push({
                        value: `fixed_expense_${expense.id}`,
                        label: `${expense.name} - ${formatCurrency(expense.amount)}`
                    });
                });

                // Update metricLabels
                metricOptions.forEach(opt => {
                    metricLabels[opt.value] = opt.label;
                });

                // Load investments after fixed expenses
                return loadInvestments();
            })
            .catch(err => {
                console.error('Error loading fixed expenses:', err);
                // Still try to load investments
                return loadInvestments();
            });
    }

    function loadInvestments() {
        return axios.get('/api/investments')
            .then(r => {
                investments = r.data || [];

                // Add individual investments to metricOptions for "Aplicar em" select
                investments.forEach(investment => {
                    const investmentLabel = `${investment.name} - ${formatCurrency(investment.total_invested || 0)}`;
                    metricOptions.push({
                        value: `investment_${investment.id}`,
                        label: investmentLabel
                    });
                });

                // Update metricLabels
                metricOptions.forEach(opt => {
                    metricLabels[opt.value] = opt.label;
                });

                // Re-render custom operations after investments are loaded
                renderCustomOperations();
            })
            .catch(err => {
                console.error('Error loading investments:', err);
                // Still render custom operations even if investments fail to load
                renderCustomOperations();
            });
    }

    function loadAccounts() {
        return axios.get('/api/projection/accounts')
            .then(r => {
                accounts = r.data || [];
                const select = document.getElementById('selectedAccount');
                select.innerHTML = '<option value="">Todas as contas</option>';

                accounts.forEach(account => {
                    select.innerHTML += `<option value="${account.id}">${account.name} (${formatCurrency(account.current_balance)})</option>`;
                });
            })
            .catch(err => {
                console.error('Error loading accounts:', err);
            });
    }

    function toggleAccountSelect() {
        const useAccount = document.getElementById('useAccountBalance').checked;
        document.getElementById('accountSelectWrap').style.display = useAccount ? 'block' : 'none';
        if (!useAccount) {
            document.getElementById('selectedAccount').value = '';
        }
        calculateProjection();
    }

    function toggleCreditCardSource() {
        const subtractCards = document.getElementById('subtractCreditCards').checked;
        document.getElementById('creditCardSourceWrap').style.display = subtractCards ? 'block' : 'none';
        calculateProjection();
    }

    function renderCustomOperations() {
        const container = document.getElementById('customOperationsContainer');
        if (!container) {
            return;
        }

        if (customOperations.length === 0) {
            container.innerHTML = '<p class="text-muted mb-0">Nenhuma combinação adicionada. Clique em "Adicionar combinação" para começar.</p>';
            return;
        }

        container.innerHTML = customOperations.map((operation, index) => `
        <div class="custom-operation-row">
            <div class="row g-3 align-items-end">
                <div class="col-md-4 col-12">
                    <label class="form-label">Aplicar em</label>
                    <select class="form-select" onchange="updateCustomOperation(${index}, 'apply_to', this.value)">
                        ${metricOptions.map(opt => `<option value="${opt.value}" ${operation.apply_to === opt.value ? 'selected' : ''}>${opt.label}</option>`).join('')}
                    </select>
                </div>
                <div class="col-md-3 col-12">
                    <label class="form-label">Operação</label>
                    <select class="form-select" onchange="updateCustomOperation(${index}, 'operation', this.value)">
                        <option value="add" ${operation.operation === 'add' ? 'selected' : ''}>Somar (+)</option>
                        <option value="subtract" ${operation.operation === 'subtract' ? 'selected' : ''}>Subtrair (-)</option>
                    </select>
                </div>
                <div class="col-md-4 col-12">
                    <label class="form-label">Com o valor de</label>
                    <div class="modern-multiselect" id="multiselect-${index}">
                        <div class="modern-multiselect-trigger" onclick="toggleMultiselect(${index}, event)">
                            <div class="modern-multiselect-trigger-text ${(operation.values || []).length === 0 ? 'empty' : ''}" id="multiselect-text-${index}">
                                ${(operation.values || []).length === 0
                                    ? '<span>Selecione um ou mais valores...</span>'
                                    : (operation.values || []).map(val => {
                                        const opt = metricOptions.find(o => o.value === val);
                                        return opt ? `<span class="selected-chip">
                                            ${opt.label}
                                            <span class="selected-chip-remove" onclick="event.stopPropagation(); removeMultiselectValue(${index}, '${val}')">×</span>
                                        </span>` : '';
                                    }).join('')
                                }
                            </div>
                            <i class="bi bi-chevron-down modern-multiselect-trigger-icon"></i>
                        </div>
                        <div class="modern-multiselect-dropdown" id="multiselect-dropdown-${index}">
                            ${metricOptions.map(opt => {
                                const selected = (operation.values || []).includes(opt.value);
                                return `
                                    <div class="modern-multiselect-item ${selected ? 'selected' : ''}"
                                         data-value="${opt.value}"
                                         onclick="toggleMultiselectValue(${index}, '${opt.value}', event)">
                                        <div class="modern-multiselect-checkbox"></div>
                                        <div class="modern-multiselect-item-label">${opt.label}</div>
                                    </div>
                                `;
                            }).join('')}
                        </div>
                    </div>
                </div>
                <div class="col-md-1 col-12 text-md-end">
                    <button class="btn btn-outline-danger w-100" type="button" onclick="removeCustomOperation(${index})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `).join('');
    }

    function addCustomOperation() {
        customOperations.push({
            apply_to: 'projected_balance',
            operation: 'subtract',
            values: ['credit_cards_balance'],
        });
        saveCustomOperationsToStorage();
        renderCustomOperations();
        calculateProjection();
    }
    
    function removeCustomOperation(index) {
        if (confirm('Remover esta combinação personalizada?')) {
            customOperations.splice(index, 1);
            saveCustomOperationsToStorage();
            renderCustomOperations();
            calculateProjection();
        }
    }

    function updateCustomOperation(index, field, value) {
        if (!customOperations[index]) {
            return;
        }
        customOperations[index][field] = value;
        saveCustomOperationsToStorage();
        renderCustomOperations();
        calculateProjection();
    }

    function updateCustomOperationValues(index, selectEl) {
        if (!customOperations[index]) {
            return;
        }

        const selectedValues = Array.from(selectEl.selectedOptions).map(opt => opt.value);
        customOperations[index].values = selectedValues.length ? selectedValues : [];
        saveCustomOperationsToStorage();
        renderCustomOperations();
        calculateProjection();
    }

    function toggleMultiselect(index, event) {
        if (event) {
            event.stopPropagation();
        }

        const dropdown = document.getElementById(`multiselect-dropdown-${index}`);
        const trigger = document.querySelector(`#multiselect-${index} .modern-multiselect-trigger`);
        const textEl = document.getElementById(`multiselect-text-${index}`);

        if (!dropdown || !trigger) {
            return;
        }

        const isOpen = dropdown.classList.contains('show');
        const operation = customOperations[index];
        const isEmpty = !operation || !operation.values || operation.values.length === 0;

        // Close all other dropdowns
        document.querySelectorAll('.modern-multiselect-dropdown.show').forEach(el => {
            if (el !== dropdown) {
                el.classList.remove('show');
            }
        });
        document.querySelectorAll('.modern-multiselect-trigger.active').forEach(el => {
            if (el !== trigger) {
                el.classList.remove('active');
            }
        });

        if (!isOpen) {
            dropdown.classList.add('show');
            trigger.classList.add('active');
        } else {
            dropdown.classList.remove('show');
            trigger.classList.remove('active');
        }

        // Reset any inline height styles - let CSS handle it
        if (trigger) {
            trigger.style.minHeight = '';
        }
    }

    function toggleMultiselectValue(index, value, event) {
        if (event) {
            event.stopPropagation();
        }

        if (!customOperations[index]) {
            return;
        }

        const operation = customOperations[index];
        if (!operation.values) {
            operation.values = [];
        }

        const valueIndex = operation.values.indexOf(value);
        if (valueIndex > -1) {
            operation.values.splice(valueIndex, 1);
        } else {
            operation.values.push(value);
        }

        saveCustomOperationsToStorage();
        updateMultiselectDisplay(index);
        calculateProjection();
    }

    function removeMultiselectValue(index, value) {
        if (!customOperations[index]) {
            return;
        }

        const operation = customOperations[index];
        if (!operation.values) {
            operation.values = [];
        }

        const valueIndex = operation.values.indexOf(value);
        if (valueIndex > -1) {
            operation.values.splice(valueIndex, 1);
            saveCustomOperationsToStorage();
            updateMultiselectDisplay(index);
            calculateProjection();
        }
    }

    function calculateDropdownHeight(dropdown) {
        if (!dropdown) {
            return 0;
        }

        // Temporarily show dropdown to measure
        const wasVisible = dropdown.classList.contains('show');
        const originalDisplay = dropdown.style.display;
        const originalOpacity = dropdown.style.opacity;
        const originalVisibility = dropdown.style.visibility;

        dropdown.style.display = 'block';
        dropdown.style.opacity = '0';
        dropdown.style.visibility = 'hidden';
        dropdown.style.position = 'absolute';
        dropdown.style.top = '-9999px';

        const height = dropdown.offsetHeight;

        // Restore original state
        dropdown.style.display = originalDisplay;
        dropdown.style.opacity = originalOpacity;
        dropdown.style.visibility = originalVisibility;
        dropdown.style.position = '';
        dropdown.style.top = '';

        if (wasVisible) {
            dropdown.classList.add('show');
        }

        return height;
    }

    function updateMultiselectDisplay(index) {
        const operation = customOperations[index];
        if (!operation) {
            return;
        }

        const textEl = document.getElementById(`multiselect-text-${index}`);
        const dropdown = document.getElementById(`multiselect-dropdown-${index}`);
        const trigger = document.querySelector(`#multiselect-${index} .modern-multiselect-trigger`);

        if (!textEl || !dropdown) {
            return;
        }

        // Preserve dropdown state
        const wasOpen = dropdown.classList.contains('show');

        // Update trigger text
        if ((operation.values || []).length === 0) {
            textEl.innerHTML = '<span>Selecione um ou mais valores...</span>';
            textEl.classList.add('empty');

            // Reset trigger height to default when empty
            if (trigger) {
                trigger.style.minHeight = '';
            }
        } else {
            textEl.innerHTML = (operation.values || []).map(val => {
                const opt = metricOptions.find(o => o.value === val);
                return opt ? `<span class="selected-chip">
                ${opt.label}
                <span class="selected-chip-remove" onclick="event.stopPropagation(); removeMultiselectValue(${index}, '${val}')">×</span>
            </span>` : '';
            }).join('');
            textEl.classList.remove('empty');

            // Reset trigger height when not empty (let CSS handle it)
            if (trigger) {
                trigger.style.minHeight = '';
            }
        }

        // Update dropdown items
        dropdown.querySelectorAll('.modern-multiselect-item').forEach(item => {
            const itemValue = item.getAttribute('data-value');
            const isSelected = (operation.values || []).includes(itemValue);
            if (isSelected) {
                item.classList.add('selected');
            } else {
                item.classList.remove('selected');
            }
        });

        // Restore dropdown state if it was open
        if (wasOpen) {
            dropdown.classList.add('show');
            if (trigger) {
                trigger.classList.add('active');
            }
        }
    }

    function renderCustomResults(results) {
        const container = document.getElementById('customResults');
        if (!container) {
            return;
        }

        if (!results || results.length === 0) {
            container.innerHTML = '<p class="text-muted mb-0">Nenhum resultado personalizado ainda.</p>';
            return;
        }

        container.innerHTML = `
        <h6 class="text-muted mb-3"><i class="bi bi-activity"></i> Resultados das combinações</h6>
        <div class="row g-3">
            ${results.map(res => {
                const targetLabel = metricLabels[res.apply_to] || res.apply_to;
                const valueKeys = (res.value_keys && res.value_keys.length)
                    ? res.value_keys
                    : (res.value_key ? [res.value_key] : []);
                const valueLabel = valueKeys.map(key => metricLabels[key] || key).join(' + ');
                const operationSymbol = res.operation === 'add' ? '+' : '-';
                const resultColor = res.result >= 0 ? 'var(--success)' : 'var(--danger)';
                const chips = valueKeys.map(key => `<span class="badge bg-dark me-1">${metricLabels[key] || key}</span>`).join('');
                return `
                    <div class="col-md-4 col-12">
                        <div class="p-3 border rounded h-100">
                            <small class="text-muted d-block">${targetLabel}</small>
                            <div class="fw-semibold mb-2">${operationSymbol} ${valueLabel}</div>
                            <div class="small text-muted mb-2">
                                <div>Base: ${formatCurrency(res.base_value ?? 0)}</div>
                                <div>Valor combinado: ${formatCurrency(res.value_amount ?? 0)}</div>
                                <div class="mt-1">${chips}</div>
                            </div>
                            <div class="fs-5 fw-bold" style="color: ${resultColor};">
                                ${formatCurrency(res.result ?? 0)}
                            </div>
                        </div>
                    </div>
                `;
            }).join('')}
        </div>
    `;
    }

    function addExpense() {
        const categoryId = document.getElementById('expenseCategory').value;
        const description = document.getElementById('expenseDescription').value;
        const amount = parseFloat(document.getElementById('expenseAmount').value);

        if (!categoryId || !amount || amount <= 0) {
            alert('Preencha a categoria e o valor corretamente');
            return;
        }

        let categoryName = 'Sem categoria';
        let finalDescription = description || 'Sem descrição';
        let finalAmount = amount;

        // Check if it's a fixed expense
        if (categoryId.startsWith('fixed_expense_')) {
            const fixedExpenseId = parseInt(categoryId.replace('fixed_expense_', ''));
            const fixedExpense = fixedExpenses.find(exp => exp.id === fixedExpenseId);
            
            if (fixedExpense) {
                categoryName = fixedExpense.category_name || 'Gasto Fixo';
                finalDescription = fixedExpense.name;
                finalAmount = fixedExpense.amount;
            }
        } else {
            const category = categories.find(c => c.id == categoryId) ||
                categories.flatMap(c => c.children || []).find(c => c.id == categoryId);
            categoryName = category ? category.name : 'Sem categoria';
        }

        selectedExpenses.push({
            category_id: categoryId.startsWith('fixed_expense_') ? null : categoryId,
            category_name: categoryName,
            description: finalDescription,
            amount: finalAmount
        });

        renderExpenses();
        calculateProjection();

        // Clear form
        document.getElementById('expenseCategory').value = '';
        document.getElementById('expenseDescription').value = '';
        document.getElementById('expenseAmount').value = '';
    }

    function removeExpense(index) {
        selectedExpenses.splice(index, 1);
        renderExpenses();
        calculateProjection();
    }

    function clearExpenses() {
        if (confirm('Limpar todos os gastos selecionados?')) {
            selectedExpenses = [];
            renderExpenses();
            calculateProjection();
        }
    }

    function renderExpenses() {
        const container = document.getElementById('selectedExpenses');

        if (selectedExpenses.length === 0) {
            container.innerHTML = '<p class="text-muted text-center">Nenhum gasto selecionado</p>';
            document.getElementById('totalExpenses').innerText = 'R$ 0,00';
            return;
        }

        const total = selectedExpenses.reduce((sum, exp) => sum + exp.amount, 0);
        document.getElementById('totalExpenses').innerText = formatCurrency(total);

        container.innerHTML = selectedExpenses.map((exp, index) => `
        <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
            <div>
                <strong>${exp.category_name}</strong><br>
                <small class="text-muted">${exp.description}</small>
            </div>
            <div class="text-end">
                <div class="text-danger"><strong>${formatCurrency(exp.amount)}</strong></div>
                <button class="btn btn-sm btn-outline-danger mt-1" onclick="removeExpense(${index})">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        </div>
    `).join('');
    }

    function calculateProjection() {
        const month = document.getElementById('projectionMonth').value;
        const options = [];

        if (document.getElementById('subtractCreditCards').checked) {
            options.push('subtract_credit_cards');
        }

        if (document.getElementById('subtractInvestments').checked) {
            options.push('subtract_investments');
        }

        if (document.getElementById('useAccountBalance').checked) {
            options.push('use_account_balance');
        }

        const accountId = document.getElementById('selectedAccount').value;

        const requestData = {
            expenses: selectedExpenses,
            month: month,
            options: options,
            custom_operations: customOperations,
        };

        if (accountId) {
            requestData.account_id = accountId;
        }

        if (document.getElementById('subtractCreditCards').checked) {
            requestData.credit_card_subtraction_source = document.getElementById('creditCardSource').value;
        }

        axios.post('/api/projection/calculate', requestData)
            .then(r => {
                const data = r.data;

                document.getElementById('currentBalance').innerText = formatCurrency(data.current_balance);
                document.getElementById('expectedIncome').innerText = formatCurrency(data.expected_income);

                const projected = data.projected_balance;
                const projectedEl = document.getElementById('projectedBalance');
                projectedEl.innerText = formatCurrency(projected);
                projectedEl.style.color = projected >= 0 ? 'var(--success)' : 'var(--danger)';

                // Summary
                const summary = document.getElementById('projectionSummary');
                let summaryItems = `
            <div class="row g-3">
                <div class="col-md-3">
                   <div class="p-3 border rounded">
                        <small class="text-muted d-block">Saldo Atual</small>
                        <strong class="text-white">${formatCurrency(data.current_balance)}</strong>
                    </div>


                </div>
                <div class="col-md-3">
                    <div class="p-3 border rounded">
                        <small class="text-muted d-block">+ Receitas Esperadas</small>
                        <strong class="text-success">${formatCurrency(data.expected_income)}</strong>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 border rounded">
                        <small class="text-muted d-block">- Gastos Selecionados</small>
                        <strong class="text-danger">${formatCurrency(data.total_expenses)}</strong>
                    </div>
                </div>
        `;

                if (data.credit_cards_balance > 0) {
                    summaryItems += `
                <div class="col-md-3">
                    <div class="p-3 border rounded">
                        <small class="text-muted d-block">- Cartões de Crédito</small>
                        <strong class="text-danger">${formatCurrency(data.credit_cards_balance)}</strong>
                    </div>
                </div>
            `;
                }

                if (data.investments_total > 0) {
                    summaryItems += `
                <div class="col-md-3">
                    <div class="p-3 border rounded">
                        <small class="text-muted d-block">- Investimentos</small>
                        <strong class="text-warning">${formatCurrency(data.investments_total)}</strong>
                    </div>
                </div>
            `;
                }

                summaryItems += `
                <div class="col-12">
                    <div class="p-3 border rounded" style="background: var(--bg-secondary);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted d-block">Saldo Projetado para ${new Date(month + '-01').toLocaleDateString('pt-BR', { month: 'long', year: 'numeric' })}</small>
                                <h4 class="mb-0" style="color: ${projected >= 0 ? 'var(--success)' : 'var(--danger)'}">
                                    ${formatCurrency(projected)}
                                </h4>
                            </div>
                            <i class="bi ${projected >= 0 ? 'bi-check-circle text-success' : 'bi-exclamation-triangle text-danger'}" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        `;

                summary.innerHTML = summaryItems;
                
                // Save custom results for additional calculations
                savedCustomResults = {};
                if (data.custom_results && data.custom_results.length > 0) {
                    data.custom_results.forEach(result => {
                        savedCustomResults[result.apply_to] = result.result;
                    });
                }
                
                // Also save base metrics
                Object.keys(data).forEach(key => {
                    if (typeof data[key] === 'number' && !['month', 'options'].includes(key) && !Array.isArray(data[key])) {
                        savedCustomResults[key] = data[key];
                    }
                });
                
                renderCustomResults(data.custom_results || []);
                renderAdditionalCalculations();
            })
            .catch(err => {
                console.error('Error calculating projection:', err);
                alert('Erro ao calcular projeção');
            });
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        const clickedInside = event.target.closest('.modern-multiselect');
        if (!clickedInside) {
            document.querySelectorAll('.modern-multiselect-dropdown.show').forEach(el => {
                el.classList.remove('show');
            });
            document.querySelectorAll('.modern-multiselect-trigger.active').forEach(el => {
                el.classList.remove('active');
            });
        }
    });

    document.getElementById('projectionMonth').addEventListener('change', calculateProjection);

    // Auto-fill fields when a fixed expense is selected
    document.getElementById('expenseCategory').addEventListener('change', function() {
        const categoryId = this.value;
        if (categoryId && categoryId.startsWith('fixed_expense_')) {
            const fixedExpenseId = parseInt(categoryId.replace('fixed_expense_', ''));
            const fixedExpense = fixedExpenses.find(exp => exp.id === fixedExpenseId);
            
            if (fixedExpense) {
                document.getElementById('expenseDescription').value = fixedExpense.name;
                document.getElementById('expenseAmount').value = fixedExpense.amount.toFixed(2);
            }
        } else {
            // Clear fields if not a fixed expense
            if (!document.getElementById('expenseDescription').value) {
                document.getElementById('expenseDescription').value = '';
            }
            if (!document.getElementById('expenseAmount').value) {
                document.getElementById('expenseAmount').value = '';
            }
        }
    });

    renderCustomResults([]);
    
    // Additional Calculations Functions
    function addAdditionalCalculation() {
        additionalCalculations.push({
            name: '',
            source_type: 'custom_result', // 'custom_result' or 'custom_value'
            source_key: '',
            custom_value: 0,
            operation: 'add',
            value: 0,
            result: 0
        });
        saveAdditionalCalculationsToStorage();
        renderAdditionalCalculations();
    }
    
    function removeAdditionalCalculation(index) {
        if (confirm('Remover este cálculo adicional?')) {
            additionalCalculations.splice(index, 1);
            saveAdditionalCalculationsToStorage();
            renderAdditionalCalculations();
        }
    }
    
    function updateAdditionalCalculation(index, field, value) {
        if (!additionalCalculations[index]) {
            return;
        }
        additionalCalculations[index][field] = value;
        
        // If source_type changed, reset source_key or custom_value
        if (field === 'source_type') {
            if (value === 'custom_result') {
                additionalCalculations[index].custom_value = 0;
            } else {
                additionalCalculations[index].source_key = '';
            }
        }
        
        saveAdditionalCalculationsToStorage();
        calculateAdditionalCalculation(index);
        renderAdditionalCalculations();
    }
    
    function calculateAdditionalCalculation(index) {
        const calc = additionalCalculations[index];
        if (!calc) {
            return;
        }
        
        let sourceValue = 0;
        
        if (calc.source_type === 'custom_result') {
            sourceValue = savedCustomResults[calc.source_key] || 0;
        } else {
            sourceValue = parseFloat(calc.custom_value) || 0;
        }
        
        const value = parseFloat(calc.value) || 0;
        
        if (calc.operation === 'add') {
            calc.result = sourceValue + value;
        } else {
            calc.result = sourceValue - value;
        }
        
        saveAdditionalCalculationsToStorage();
    }
    
    function renderAdditionalCalculations() {
        const container = document.getElementById('additionalCalculationsContainer');
        if (!container) {
            return;
        }
        
        if (additionalCalculations.length === 0) {
            container.innerHTML = '<p class="text-muted mb-0">Nenhum cálculo adicional adicionado. Clique em "Adicionar cálculo" para começar.</p>';
            return;
        }
        
        // Calculate all results first
        additionalCalculations.forEach((calc, index) => {
            calculateAdditionalCalculation(index);
        });
        
        // Get available custom results for select
        const availableResults = Object.keys(savedCustomResults).map(key => ({
            value: key,
            label: metricLabels[key] || key
        }));
        
        container.innerHTML = additionalCalculations.map((calc, index) => `
            <div class="additional-calculation-row border rounded p-3 mb-3">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3 col-12">
                        <label class="form-label">Nome do Cálculo</label>
                        <input type="text" class="form-control" value="${calc.name || ''}" 
                               placeholder="Ex: Saldo Final" 
                               onchange="updateAdditionalCalculation(${index}, 'name', this.value)">
                    </div>
                    <div class="col-md-3 col-12">
                        <label class="form-label">Fonte</label>
                        <select class="form-select" onchange="updateAdditionalCalculation(${index}, 'source_type', this.value)">
                            <option value="custom_result" ${calc.source_type === 'custom_result' ? 'selected' : ''}>Resultado de Combinação</option>
                            <option value="custom_value" ${calc.source_type === 'custom_value' ? 'selected' : ''}>Valor Personalizado</option>
                        </select>
                    </div>
                    <div class="col-md-3 col-12" id="sourceSelect-${index}">
                        ${calc.source_type === 'custom_result' 
                            ? `<label class="form-label">Selecionar Resultado</label>
                               <select class="form-select" onchange="updateAdditionalCalculation(${index}, 'source_key', this.value)">
                                   <option value="">Selecione...</option>
                                   ${availableResults.map(opt => 
                                       `<option value="${opt.value}" ${calc.source_key === opt.value ? 'selected' : ''}>${opt.label}</option>`
                                   ).join('')}
                               </select>`
                            : `<label class="form-label">Valor Personalizado</label>
                               <input type="number" class="form-control" step="0.01" value="${calc.custom_value || 0}" 
                                      onchange="updateAdditionalCalculation(${index}, 'custom_value', parseFloat(this.value))">`
                        }
                    </div>
                    <div class="col-md-2 col-12">
                        <label class="form-label">Operação</label>
                        <select class="form-select" onchange="updateAdditionalCalculation(${index}, 'operation', this.value)">
                            <option value="add" ${calc.operation === 'add' ? 'selected' : ''}>Somar (+)</option>
                            <option value="subtract" ${calc.operation === 'subtract' ? 'selected' : ''}>Subtrair (-)</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-12">
                        <label class="form-label">Valor</label>
                        <input type="number" class="form-control" step="0.01" value="${calc.value || 0}" 
                               onchange="updateAdditionalCalculation(${index}, 'value', parseFloat(this.value))">
                    </div>
                    <div class="col-md-2 col-12">
                        <label class="form-label">Resultado</label>
                        <div class="form-control bg-light fw-bold text-center" style="color: ${calc.result >= 0 ? 'var(--success)' : 'var(--danger)'};">
                            ${formatCurrency(calc.result || 0)}
                        </div>
                    </div>
                    <div class="col-md-1 col-12 text-md-end">
                        <button class="btn btn-outline-danger w-100" type="button" onclick="removeAdditionalCalculation(${index})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `).join('');
        
        // Update source selects when source_type changes
        additionalCalculations.forEach((calc, index) => {
            const sourceSelect = document.getElementById(`sourceSelect-${index}`);
            if (sourceSelect) {
                const select = sourceSelect.querySelector('select');
                const input = sourceSelect.querySelector('input');
                if (calc.source_type === 'custom_result' && select) {
                    select.onchange = function() {
                        updateAdditionalCalculation(index, 'source_key', this.value);
                    };
                } else if (calc.source_type === 'custom_value' && input) {
                    input.onchange = function() {
                        updateAdditionalCalculation(index, 'custom_value', parseFloat(this.value));
                    };
                }
            }
        });
    }
    
    // Load data and render custom operations after everything is loaded
    Promise.all([
        loadCategories(),
        loadAccounts()
    ]).then(() => {
        // Custom operations will be rendered inside loadFixedExpenses()
        // which is called by loadCategories()
        calculateProjection();
    }).catch(() => {
        // Fallback: render even if there's an error
        renderCustomOperations();
        calculateProjection();
    });
</script>
@endsection
