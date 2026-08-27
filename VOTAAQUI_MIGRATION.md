# VotaAqui - Migração para Vue.js

## Resumo da Migração

A homepage do VotaAqui foi migrada com sucesso do Blade para Vue.js, criando uma Single Page Application (SPA) moderna e interativa.

## Estrutura Criada

### 1. Components Vue.js
- **Homepage.vue** (`resources/js/views/pages/votaaqui/Homepage.vue`)
  - Componente principal da homepage
  - Lista os participantes ativos
  - Sistema de votação interativo
  - Design responsivo

### 2. Layout
- **AppVotaAquiLayout.vue** (`resources/js/layout_votaaqui/AppVotaAquiLayout.vue`)
  - Layout específico para a aplicação VotaAqui

### 3. API Controllers
- **ParticipantController** (`app/Http/Controllers/Api/ParticipantController.php`)
  - CRUD completo para participantes
  - Endpoint para listar participantes ativos
  
- **VoteController** (`app/Http/Controllers/Api/VoteController.php`)
  - Sistema de votação
  - Estatísticas de votos
  - Rate limiting (10 votos por hora por IP)

### 4. Models Atualizados
- **Participant.php** - Modelo com relacionamentos e scopes
- **PublicVote.php** - Modelo para votos do público

### 5. Database
- Migrações para todas as tabelas necessárias
- Seeder com 8 participantes de exemplo

## Rotas

### Web Routes
- `/` - Homepage principal do VotaAqui (Vue.js) ⭐ **ROTA PRINCIPAL**
- `/home` - Rota alternativa para a homepage
- `/homepage` - Rota alternativa para a homepage
- `/votaaqui` - Rota específica do VotaAqui
- `/homepage-blade` - Versão original em Blade (para comparação)

### API Routes
- `GET /api/participants` - Lista participantes ativos
- `POST /api/votes` - Registra um voto
- `GET /api/votes/statistics` - Estatísticas de votação
- `GET /api/participants/{id}/votes` - Votos de um participante específico

## Funcionalidades Implementadas

### Frontend (Vue.js)
1. **Lista de Participantes**
   - Exibição em grid responsivo
   - Informações de cada participante (nome, biografia, foto)
   - Loading states e error handling

2. **Sistema de Votação**
   - Modal de confirmação
   - Feedback visual
   - Prevenção de spam

3. **Design Responsivo**
   - Mobile-first
   - Animações suaves
   - Gradientes modernos

4. **Navegação**
   - Scroll suave
   - Botão "voltar ao topo"
   - Menu responsivo

### Backend (Laravel)
1. **API RESTful**
   - Responses padronizadas
   - Error handling
   - Validação de dados

2. **Rate Limiting**
   - 10 votos por hora por IP
   - Controle de spam

3. **Base de Dados**
   - Participantes com todos os campos necessários
   - Sistema de votos públicos
   - Relacionamentos entre modelos

## Como Usar

### Instalação
```bash
# Instalar dependências
composer install
npm install

# Configurar ambiente
cp .env.example .env
php artisan key:generate

# Executar migrações e seeders
php artisan migrate
php artisan db:seed --class=ParticipantSeeder

# Iniciar servidores
php artisan serve
npm run dev
```

### Acesso
- Aplicação principal: http://localhost:8000
- Versão Blade original: http://localhost:8000/homepage-blade

## Tecnologias Utilizadas

### Frontend
- **Vue.js 3** - Framework principal
- **Vue Router** - Roteamento SPA
- **Axios** - HTTP requests
- **PrimeVue** - Componentes UI
- **Bootstrap 5** - Grid system e utilities
- **Vite** - Build tool

### Backend
- **Laravel 11** - Framework PHP
- **MySQL** - Base de dados
- **Eloquent ORM** - Object-Relational Mapping

## Vantagens da Migração

1. **Performance**
   - Carregamento mais rápido após load inicial
   - Navegação sem recarregar a página
   - Cache de componentes

2. **UX/UI**
   - Interações mais fluidas
   - Feedback imediato
   - Animações suaves

3. **Manutenibilidade**
   - Código componentizado
   - Separação clara de responsabilidades
   - Reutilização de componentes

4. **Escalabilidade**
   - Facilidade para adicionar novas funcionalidades
   - API reutilizável para outras plataformas
   - Possibilidade de PWA no futuro

## Próximos Passos

1. **Funcionalidades Adicionais**
   - Dashboard de estatísticas em tempo real
   - Sistema de notificações
   - Chat ao vivo
   - Perfis detalhados dos participantes

2. **Otimizações**
   - Server-side rendering (SSR)
   - Progressive Web App (PWA)
   - Cache otimizado

3. **Integrações**
   - Redes sociais
   - Pagamentos
   - SMS/Email notifications
   - Analytics

## Arquivos Principais

```
resources/
├── js/
│   ├── views/pages/votaaqui/
│   │   └── Homepage.vue
│   ├── layout_votaaqui/
│   │   └── AppVotaAquiLayout.vue
│   ├── router/index.js
│   └── app.js
├── views/
│   └── votaaqui.blade.php
└── css/
    └── votaaqui.css

app/
├── Http/Controllers/Api/
│   ├── ParticipantController.php
│   └── VoteController.php
└── Models/
    ├── Participant.php
    └── PublicVote.php

database/
├── migrations/
└── seeders/
    └── ParticipantSeeder.php
```

A migração está completa e a aplicação está pronta para uso em produção!
