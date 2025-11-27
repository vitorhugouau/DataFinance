@extends('layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-tags"></i> Categorias</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal">
        <i class="bi bi-plus-circle"></i> Nova Categoria
    </button>
</div>

<div class="card mb-4">
    <form id="formCategory" class="row g-3 p-3">
        <div class="col-md-4">
            <label class="form-label">Nome da Categoria</label>
            <input name="name" class="form-control" placeholder="Ex: Alimentação" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Tipo</label>
            <select name="type" class="form-select" required>
                <option value="">Selecione...</option>
                <option value="expense">Despesa</option>
                <option value="income">Receita</option>
                <option value="transfer">Transferência</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Categoria Pai (Opcional)</label>
            <select name="parent_id" id="parentCategory" class="form-select">
                <option value="">Nenhuma (Categoria Principal)</option>
            </select>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <div class="w-100">
                <label class="form-label">Cor</label>
                <input type="color" name="color" class="form-control form-control-color" value="#00d4ff" title="Escolha uma cor">
            </div>
        </div>
        <div class="col-md-12 d-flex justify-content-end">
            <button class="btn btn-primary" type="submit">
                <i class="bi bi-check-lg"></i> Criar
            </button>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-body">
        <h5 class="mb-3">Lista de Categorias</h5>
        <div id="categoriesList"></div>
    </div>
</div>

<div class="modal fade" id="categoryModal" tabindex="-1" data-bs-backdrop="false">
    <div class="modal-dialog">
        <form id="formCategoryModal" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Categoria</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="editCategoryId">
                <div class="mb-3">
                    <label class="form-label">Nome</label>
                    <input class="form-control" name="name" id="editCategoryName" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tipo</label>
                    <select class="form-select" name="type" id="editCategoryType" required>
                        <option value="expense">Despesa</option>
                        <option value="income">Receita</option>
                        <option value="transfer">Transferência</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Categoria Pai</label>
                    <select class="form-select" name="parent_id" id="editCategoryParent">
                        <option value="">Nenhuma (Categoria Principal)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Cor</label>
                    <input type="color" name="color" id="editCategoryColor" class="form-control form-control-color" value="#00d4ff">
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
        #formCategory .col-md-4,
        #formCategory .col-md-3,
        #formCategory .col-md-2 {
            margin-bottom: 0.5rem;
        }
        
        .card-body h6 {
            font-size: 1rem;
        }
    }
    
    @media (max-width: 576px) {
        #formCategory .col-md-12 {
            margin-top: 0.5rem;
        }
    }
</style>
<script>
let allCategories = [];

function loadCategories() {
    return axios.get('/api/categories')
        .then(r => {
            allCategories = r.data || [];
            renderCategories();
            updateParentSelects();
        })
        .catch(err => {
            console.error('Error loading categories:', err);
            alert('Erro ao carregar categorias');
        });
}

function updateParentSelects() {
    const selects = ['parentCategory', 'editCategoryParent'];
    selects.forEach(selectId => {
        const select = document.getElementById(selectId);
        if (!select) return;
        
        const currentValue = select.value;
        select.innerHTML = '<option value="">Nenhuma (Categoria Principal)</option>';
        
        allCategories.forEach(cat => {
            if (selectId === 'editCategoryParent' && cat.id == document.getElementById('editCategoryId')?.value) {
                return; // Don't allow self-reference
            }
            select.innerHTML += `<option value="${cat.id}">${cat.name}</option>`;
        });
        
        if (currentValue) {
            select.value = currentValue;
        }
    });
}

function renderCategories() {
    const container = document.getElementById('categoriesList');
    const mainCategories = allCategories.filter(c => !c.parent_id);
    
    if (mainCategories.length === 0) {
        container.innerHTML = '<p class="text-muted text-center">Nenhuma categoria cadastrada</p>';
        return;
    }

    container.innerHTML = mainCategories.map(category => {
        const children = allCategories.filter(c => c.parent_id == category.id);
        const color = category.color || '#6c757d';
        
        let html = `
            <div class="card mb-3" style="border-left: 4px solid ${color}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width: 20px; height: 20px; background: ${color}; border-radius: 4px;"></div>
                            <div>
                                <h6 class="mb-0">${category.name}</h6>
                                <small class="text-muted">${category.type}</small>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-primary" onclick="editCategory(${category.id})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteCategory(${category.id})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
        `;
        
        if (children.length > 0) {
            html += '<div class="mt-3 ps-4 border-start" style="border-color: ' + color + ' !important;">';
            html += '<small class="text-muted d-block mb-2">Subcategorias:</small>';
            html += children.map(child => {
                const childColor = child.color || color;
                return `
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width: 16px; height: 16px; background: ${childColor}; border-radius: 3px;"></div>
                            <span>${child.name}</span>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-outline-primary" onclick="editCategory(${child.id})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteCategory(${child.id})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            }).join('');
            html += '</div>';
        }
        
        html += '</div></div>';
        return html;
    }).join('');
}

function editCategory(id) {
    // Ensure categories are loaded
    if (allCategories.length === 0) {
        loadCategories().then(() => {
            editCategory(id);
        });
        return;
    }
    
    const category = allCategories.find(c => c.id == id);
    if (!category) {
        alert('Categoria não encontrada');
        return;
    }
    
    document.getElementById('editCategoryId').value = category.id;
    document.getElementById('editCategoryName').value = category.name || '';
    document.getElementById('editCategoryType').value = category.type || 'expense';
    document.getElementById('editCategoryColor').value = category.color || '#00d4ff';
    
    // Update parent select before setting value
    updateParentSelects();
    
        // Use setTimeout to ensure select is updated
        setTimeout(() => {
            document.getElementById('editCategoryParent').value = category.parent_id || '';
            
            const modal = new bootstrap.Modal(document.getElementById('categoryModal'), {
                backdrop: false,
                keyboard: true
            });
            modal.show();
        }, 100);
}

function deleteCategory(id) {
    if (!confirm('Tem certeza que deseja excluir esta categoria?')) return;
    
    axios.delete(`/api/categories/${id}`).then(() => {
        loadCategories();
    });
}

document.getElementById('formCategory').onsubmit = function(e) {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(e.target));
    if (!data.parent_id) delete data.parent_id;
    if (!data.color) data.color = '#00d4ff';
    
    axios.post('/api/categories', data)
        .then(() => {
            loadCategories();
            e.target.reset();
            document.getElementById('parentCategory').innerHTML = '<option value="">Nenhuma (Categoria Principal)</option>';
        })
        .catch(err => {
            console.error('Error creating category:', err);
            alert('Erro ao criar categoria: ' + (err.response?.data?.message || err.message));
        });
};

document.getElementById('formCategoryModal').onsubmit = function(e) {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(e.target));
    const id = data.id;
    if (!id) {
        alert('ID da categoria não encontrado');
        return;
    }
    delete data.id;
    if (!data.parent_id) delete data.parent_id;
    if (!data.color) data.color = '#00d4ff';
    
    axios.put(`/api/categories/${id}`, data)
        .then(() => {
            loadCategories();
            const modalElement = document.getElementById('categoryModal');
            const modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (modalInstance) {
                modalInstance.hide();
            }
        })
        .catch(err => {
            console.error('Error updating category:', err);
            alert('Erro ao atualizar categoria: ' + (err.response?.data?.message || err.message));
        });
};

loadCategories();
</script>
@endsection