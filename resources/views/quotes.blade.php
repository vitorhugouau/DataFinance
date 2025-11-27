@extends('layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-currency-exchange"></i> Cotações</h2>
    <button class="btn btn-primary" onclick="loadQuotes()">
        <i class="bi bi-arrow-clockwise"></i> Atualizar
    </button>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card">
            <h5 class="mb-3"><i class="bi bi-bank"></i> Moedas</h5>
            <div id="currenciesList" class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Moeda</th>
                            <th>Símbolo</th>
                            <th class="text-end">Cotação (BRL)</th>
                        </tr>
                    </thead>
                    <tbody id="currenciesTable">
                        <tr><td colspan="3" class="text-center">Carregando...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <h5 class="mb-3"><i class="bi bi-coin"></i> Criptomoedas</h5>
            <div id="cryptosList" class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Criptomoeda</th>
                            <th>Símbolo</th>
                            <th class="text-end">Preço (BRL)</th>
                        </tr>
                    </thead>
                    <tbody id="cryptosTable">
                        <tr><td colspan="3" class="text-center">Carregando...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h5 class="mb-3"><i class="bi bi-info-circle"></i> Informações</h5>
        <p class="text-muted mb-0">
            <small>
                As cotações são atualizadas automaticamente a cada 5 minutos. 
                Para moedas, usamos a taxa de câmbio em relação ao Real (BRL). 
                Para criptomoedas, os preços são exibidos em Reais (BRL).
            </small>
        </p>
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
        
        .card h5 {
            font-size: 1rem;
        }
    }
    
    @media (max-width: 576px) {
        .table {
            font-size: 0.75rem;
        }
        
        .table tbody td:nth-child(2) {
            display: none;
        }
        
        .table thead th:nth-child(2) {
            display: none;
        }
        
        .row.g-4 > div {
            margin-bottom: 1rem;
        }
    }
</style>
<script>
function formatCurrency(value) {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
}

function formatNumber(value, decimals = 2) {
    return new Intl.NumberFormat('pt-BR', { 
        minimumFractionDigits: decimals, 
        maximumFractionDigits: decimals 
    }).format(value);
}

function loadQuotes() {
    // Load currencies
    axios.get('/api/quotes/currencies')
        .then(r => {
            const currencies = Object.values(r.data || {});
            const tbody = document.getElementById('currenciesTable');
            
            if (currencies.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">Nenhuma cotação disponível</td></tr>';
                return;
            }
            
            tbody.innerHTML = currencies.map(currency => {
                // For currencies, rate is how many BRL = 1 unit of currency
                // So we show: 1 USD = X BRL
                return `
                    <tr>
                        <td><strong>${currency.name}</strong></td>
                        <td><span class="badge bg-primary">${currency.symbol}</span></td>
                        <td class="text-end">
                            <strong>1 ${currency.symbol} = ${formatCurrency(currency.rate)}</strong>
                        </td>
                    </tr>
                `;
            }).join('');
        })
        .catch(err => {
            console.error('Error loading currencies:', err);
            document.getElementById('currenciesTable').innerHTML = '<tr><td colspan="3" class="text-center text-danger">Erro ao carregar cotações</td></tr>';
        });

    // Load cryptocurrencies
    axios.get('/api/quotes/cryptocurrencies')
        .then(r => {
            const cryptos = Object.values(r.data || {});
            const tbody = document.getElementById('cryptosTable');
            
            if (cryptos.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">Nenhuma cotação disponível</td></tr>';
                return;
            }
            
            tbody.innerHTML = cryptos.map(crypto => {
                const price = parseFloat(crypto.price || 0);
                return `
                    <tr>
                        <td><strong>${crypto.name}</strong></td>
                        <td><span class="badge bg-warning text-dark">${crypto.symbol}</span></td>
                        <td class="text-end">
                            <strong>${formatCurrency(price)}</strong>
                        </td>
                    </tr>
                `;
            }).join('');
        })
        .catch(err => {
            console.error('Error loading cryptocurrencies:', err);
            document.getElementById('cryptosTable').innerHTML = '<tr><td colspan="3" class="text-center text-danger">Erro ao carregar cotações</td></tr>';
        });
}

// Auto-refresh every 5 minutes
setInterval(loadQuotes, 300000);

// Initial load
loadQuotes();
</script>
@endsection
