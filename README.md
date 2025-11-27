# 🚀 FinancePilot
> Seu painel inteligente para controle financeiro, projeções e automações.

## 📌 Sobre o Projeto
O **FinancePilot** é um sistema completo de gestão financeira pessoal/empresarial, com frontend em React e backend em Laravel.  
O objetivo é oferecer uma visualização clara das finanças, com controle de contas, cartões, categorias, transações e projeções personalizadas.

---

## ✨ Funcionalidades

### 📊 Dashboard
- Indicadores financeiros em tempo real  
- Resumo de contas, cartões, receitas e despesas  

### 🏦 Contas Bancárias
- Cadastro de contas  
- Saldo automático  
- Escolha de conta específica nas projeções  

### 💳 Cartões de Crédito
- Gastos separados da conta  
- Controle de limite e fechamento  

### 📁 Categorias
- Categorias personalizadas  
- Organização de despesas e receitas  

### 🔄 Transações
- Adicionar entradas e saídas  
- Filtros de data, categoria e tipo  
- Impacto direto nas projeções  

### 🧮 Projeção Financeira Inteligente
- Vários selects configuráveis  
- Escolha se quer somar ou subtrair cartões, investimentos ou contas específicas  
- Resultado automático atualizado  

### 🔐 Autenticação (opcional no roadmap)
- JWT  
- Sessão persistida  

---

## 🛠️ Tecnologias

### Frontend
- React  
- Vite  
- TailwindCSS  
- Axios  

### Backend
- Laravel  
- MySQL/PostgreSQL  
- Eloquent ORM  
- API REST  

---

## 🚀 Como Rodar o Projeto

### Backend (Laravel)
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
