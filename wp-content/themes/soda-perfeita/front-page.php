<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="utf-8">
    <link rel="icon" href="/pod-backups/perfect-soda/build/favicon.ico">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="theme-color" content="#000000">
    <meta name="description" content="Soda Perfeita - A Revolução das Bebidas Artesanais no Seu Negócio">
    <link rel="apple-touch-icon" href="/pod-backups/perfect-soda/build/logo192.png">
    <link rel="manifest" href="/pod-backups/perfect-soda/build/manifest.json">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&amp;family=Inter:wght@300;400;600;700&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Soda Perfeita - Bebidas Artesanais Premium</title>
    <script defer="defer" src="/pod-backups/perfect-soda/build/static/js/main.784926f8.js"></script>
    <link href="css/bootstrap.css" rel="stylesheet">
	<link href="css/App.css" rel="stylesheet">
	<link href="css/css2.css" rel="stylesheet">
	<link href="css/index.css" rel="stylesheet">
	<style>
	@tailwind base;
@tailwind components;
@tailwind utilities;

body {
    margin: 0;
    font-family:
        -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen",
        "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue",
        sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

code {
    font-family:
        source-code-pro, Menlo, Monaco, Consolas, "Courier New", monospace;
}

@layer base {
    :root {
        --background: 0 0% 100%;
        --foreground: 0 0% 3.9%;
        --card: 0 0% 100%;
        --card-foreground: 0 0% 3.9%;
        --popover: 0 0% 100%;
        --popover-foreground: 0 0% 3.9%;
        --primary: 0 0% 9%;
        --primary-foreground: 0 0% 98%;
        --secondary: 0 0% 96.1%;
        --secondary-foreground: 0 0% 9%;
        --muted: 0 0% 96.1%;
        --muted-foreground: 0 0% 45.1%;
        --accent: 0 0% 96.1%;
        --accent-foreground: 0 0% 9%;
        --destructive: 0 84.2% 60.2%;
        --destructive-foreground: 0 0% 98%;
        --border: 0 0% 89.8%;
        --input: 0 0% 89.8%;
        --ring: 0 0% 3.9%;
        --chart-1: 12 76% 61%;
        --chart-2: 173 58% 39%;
        --chart-3: 197 37% 24%;
        --chart-4: 43 74% 66%;
        --chart-5: 27 87% 67%;
        --radius: 0.5rem;
    }
    .dark {
        --background: 0 0% 3.9%;
        --foreground: 0 0% 98%;
        --card: 0 0% 3.9%;
        --card-foreground: 0 0% 98%;
        --popover: 0 0% 3.9%;
        --popover-foreground: 0 0% 98%;
        --primary: 0 0% 98%;
        --primary-foreground: 0 0% 9%;
        --secondary: 0 0% 14.9%;
        --secondary-foreground: 0 0% 98%;
        --muted: 0 0% 14.9%;
        --muted-foreground: 0 0% 63.9%;
        --accent: 0 0% 14.9%;
        --accent-foreground: 0 0% 98%;
        --destructive: 0 62.8% 30.6%;
        --destructive-foreground: 0 0% 98%;
        --border: 0 0% 14.9%;
        --input: 0 0% 14.9%;
        --ring: 0 0% 83.1%;
        --chart-1: 220 70% 50%;
        --chart-2: 160 60% 45%;
        --chart-3: 30 80% 55%;
        --chart-4: 280 65% 60%;
        --chart-5: 340 75% 55%;
    }
}

@layer base {
    * {
        @apply border-border;
    }
    body {
        @apply bg-background text-foreground;
    }
}

@layer base {
    [data-debug-wrapper="true"] {
        display: contents !important;
    }

    [data-debug-wrapper="true"] > * {
        margin-left: inherit;
        margin-right: inherit;
        margin-top: inherit;
        margin-bottom: inherit;
        padding-left: inherit;
        padding-right: inherit;
        padding-top: inherit;
        padding-bottom: inherit;
        column-gap: inherit;
        row-gap: inherit;
        gap: inherit;
        border-left-width: inherit;
        border-right-width: inherit;
        border-top-width: inherit;
        border-bottom-width: inherit;
        border-left-style: inherit;
        border-right-style: inherit;
        border-top-style: inherit;
        border-bottom-style: inherit;
        border-left-color: inherit;
        border-right-color: inherit;
        border-top-color: inherit;
        border-bottom-color: inherit;
    }
}

	</style>
  </head>
  <body>
    <noscript>Você precisa habilitar JavaScript para executar este aplicativo.</noscript>
    <div id="root">
      <div class="App">
        <nav class="navbar navbar-expand-lg navbar-dark fixed-top" data-testid="main-navbar">
          <div class="container">
            <a class="navbar-brand" href="#home" data-testid="brand-logo">
              <i class="fas fa-glass-whiskey me-2"></i>
              <span class="brand-text">Soda Perfeita</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" data-testid="navbar-toggler">
              <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
              <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                  <a class="nav-link" href="#sobre" data-testid="nav-link-sobre">Sobre</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#como-funciona" data-testid="nav-link-como-funciona">Como Funciona</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#beneficios" data-testid="nav-link-beneficios">Benefícios</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#tiers" data-testid="nav-link-tiers">Programas</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link btn-nav-cta" href="#contato" data-testid="nav-link-contato">Fale Conosco</a>
                </li>
              </ul>
            </div>
          </div>
        </nav>
        <section class="hero-section" id="home" data-testid="hero-section">
          <div class="hero-overlay"></div>
          <div class="container">
            <div class="row align-items-center min-vh-100">
              <div class="col-lg-7">
                <div class="hero-content" data-testid="hero-content">
                  <h1 class="hero-title" data-testid="hero-title">A Revolução das Bebidas Artesanais no Seu Negócio</h1>
                  <p class="hero-subtitle" data-testid="hero-subtitle">Uma parceria <strong>Preshh + DVG</strong> para transformar seu foodservice com tecnologia, qualidade e rentabilidade </p>
                  <div class="hero-features" data-testid="hero-features">
                    <div class="hero-feature-item">
                      <i class="fas fa-chart-line"></i>
                      <span>Aumente seu ticket médio</span>
                    </div>
                    <div class="hero-feature-item">
                      <i class="fas fa-sync-alt"></i>
                      <span>Gere recorrência</span>
                    </div>
                    <div class="hero-feature-item">
                      <i class="fas fa-star"></i>
                      <span>Padronização premium</span>
                    </div>
                  </div>
                  <div class="hero-cta-group" data-testid="hero-cta-group">
                    <a href="#contato" class="btn btn-primary btn-lg hero-btn-primary" data-testid="hero-btn-primary">
                      <i class="fas fa-rocket me-2"></i>Quero Saber Mais </a>
                    <a href="#sobre" class="btn btn-outline-light btn-lg hero-btn-secondary" data-testid="hero-btn-secondary">
                      <i class="fas fa-play-circle me-2"></i>Como Funciona </a>
                  </div>
                </div>
              </div>
              <div class="col-lg-5">
                <div class="hero-image-container" data-testid="hero-image">
                  <img alt="Soda Perfeita" class="img-fluid hero-product-img" src="https://images.unsplash.com/photo-1665582313032-29058cee9c94?crop=entropy&amp;cs=srgb&amp;fm=jpg&amp;ixid=M3w3NDQ2MzR8MHwxfHNlYXJjaHwxfHxyZWZyZXNoaW5nJTIwYmV2ZXJhZ2V8ZW58MHx8fHwxNzYyNDYyNTMwfDA&amp;ixlib=rb-4.1.0&amp;q=85">
                  <div class="hero-badge" data-testid="hero-badge">
                    <i class="fas fa-check-circle"></i>
                    <span>Perfeita porque é feita por você</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
        <section class="sobre-section py-5" id="sobre" data-testid="sobre-section">
          <div class="container">
            <div class="row align-items-center">
              <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="sobre-image-wrapper" data-testid="sobre-image">
                  <img alt="Bebidas Artesanais" class="img-fluid rounded-4 shadow-lg" src="https://images.unsplash.com/photo-1637178921831-16034c8c7705?crop=entropy&amp;cs=srgb&amp;fm=jpg&amp;ixid=M3w3NDk1ODF8MHwxfHNlYXJjaHwyfHxkcmluayUyMGJ1YmJsZXN8ZW58MHx8fHwxNzYyNDYyNTMwfDA&amp;ixlib=rb-4.1.0&amp;q=85">
                </div>
              </div>
              <div class="col-lg-6">
                <div class="sobre-content" data-testid="sobre-content">
                  <span class="section-label" data-testid="sobre-label">O QUE É</span>
                  <h2 class="section-title" data-testid="sobre-title">Soda Perfeita</h2>
                  <p class="section-text" data-testid="sobre-text">Uma <strong>solução completa</strong> que combina tecnologia de ponta, insumos premium e suporte consultivo especializado para revolucionar o mercado de bebidas artesanais no foodservice. </p>
                  <p class="section-text">Resultado da parceria estratégica entre <strong>Preshh</strong> (sistemas de gaseificação, treinamento e consultoria) e <strong>DVG</strong> (linha premium de xaropes e rede nacional de distribuidores). </p>
                  <div class="sobre-stats row mt-4" data-testid="sobre-stats">
                    <div class="col-6 col-md-3 mb-3">
                      <div class="stat-card" data-testid="stat-card-1">
                        <i class="fas fa-store stat-icon"></i>
                        <h3 class="stat-number">500+</h3>
                        <p class="stat-label">Estabelecimentos</p>
                      </div>
                    </div>
                    <div class="col-6 col-md-3 mb-3">
                      <div class="stat-card" data-testid="stat-card-2">
                        <i class="fas fa-flask stat-icon"></i>
                        <h3 class="stat-number">20+</h3>
                        <p class="stat-label">Sabores</p>
                      </div>
                    </div>
                    <div class="col-6 col-md-3 mb-3">
                      <div class="stat-card" data-testid="stat-card-3">
                        <i class="fas fa-map-marked-alt stat-icon"></i>
                        <h3 class="stat-number">Nacional</h3>
                        <p class="stat-label">Cobertura</p>
                      </div>
                    </div>
                    <div class="col-6 col-md-3 mb-3">
                      <div class="stat-card" data-testid="stat-card-4">
                        <i class="fas fa-headset stat-icon"></i>
                        <h3 class="stat-number">24/7</h3>
                        <p class="stat-label">Suporte</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
        <section class="para-quem-section py-5" data-testid="para-quem-section">
          <div class="container">
            <div class="text-center mb-5">
              <span class="section-label" data-testid="para-quem-label">PÚBLICO-ALVO</span>
              <h2 class="section-title" data-testid="para-quem-title">Para Quem é a Soda Perfeita?</h2>
              <p class="section-subtitle" data-testid="para-quem-subtitle">Ideal para estabelecimentos do foodservice que buscam inovação e rentabilidade</p>
            </div>
            <div class="row g-4">
              <div class="col-md-6 col-lg-3">
                <div class="publico-card" data-testid="publico-card-1">
                  <div class="publico-icon">
                    <i class="fas fa-utensils"></i>
                  </div>
                  <h4>Restaurantes</h4>
                  <p>Eleve sua carta de bebidas com opções artesanais exclusivas</p>
                </div>
              </div>
              <div class="col-md-6 col-lg-3">
                <div class="publico-card" data-testid="publico-card-2">
                  <div class="publico-icon">
                    <i class="fas fa-beer"></i>
                  </div>
                  <h4>Bares</h4>
                  <p>Amplie seu mix com bebidas personalizadas e de alta margem</p>
                </div>
              </div>
              <div class="col-md-6 col-lg-3">
                <div class="publico-card" data-testid="publico-card-3">
                  <div class="publico-icon">
                    <i class="fas fa-coffee"></i>
                  </div>
                  <h4>Cafeterias</h4>
                  <p>Diversifique seu cardápio com sodas artesanais premium</p>
                </div>
              </div>
              <div class="col-md-6 col-lg-3">
                <div class="publico-card" data-testid="publico-card-4">
                  <div class="publico-icon">
                    <i class="fas fa-hamburger"></i>
                  </div>
                  <h4>Lanchonetes</h4>
                  <p>Substitua refrigerantes por bebidas artesanais lucrativas</p>
                </div>
              </div>
            </div>
            <div class="criterios-box mt-5" data-testid="criterios-box">
              <h3 class="criterios-title" data-testid="criterios-title">
                <i class="fas fa-check-double me-2"></i>Critérios para Entrar no Programa
              </h3>
              <div class="row mt-4">
                <div class="col-md-6 mb-3">
                  <div class="criterio-item" data-testid="criterio-1">
                    <i class="fas fa-building"></i>
                    <span>Foodservice ativo com CNPJ</span>
                  </div>
                </div>
                <div class="col-md-6 mb-3">
                  <div class="criterio-item" data-testid="criterio-2">
                    <i class="fas fa-cogs"></i>
                    <span>Capacidade operacional mínima</span>
                  </div>
                </div>
                <div class="col-md-6 mb-3">
                  <div class="criterio-item" data-testid="criterio-3">
                    <i class="fas fa-chart-bar"></i>
                    <span>Volume mínimo: 4 garrafas/mês</span>
                  </div>
                </div>
                <div class="col-md-6 mb-3">
                  <div class="criterio-item" data-testid="criterio-4">
                    <i class="fas fa-file-signature"></i>
                    <span>Adesão formal ao programa</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
        <section class="como-funciona-section py-5" id="como-funciona" data-testid="como-funciona-section">
          <div class="container">
            <div class="text-center mb-5">
              <span class="section-label" data-testid="cf-label">PROCESSO</span>
              <h2 class="section-title" data-testid="cf-title">Como Funciona?</h2>
              <p class="section-subtitle" data-testid="cf-subtitle">Um processo simples e integrado do cadastro à entrega</p>
            </div>
            <div class="row g-4">
              <div class="col-lg-4">
                <div class="step-card" data-testid="step-card-1">
                  <div class="step-number">01</div>
                  <div class="step-icon">
                    <i class="fas fa-user-plus"></i>
                  </div>
                  <h4 class="step-title">Cadastro e Aprovação</h4>
                  <p class="step-description">Cadastre seu estabelecimento e aguarde a aprovação da Preshh. Assinatura digital do contrato via plataforma.</p>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="step-card" data-testid="step-card-2">
                  <div class="step-number">02</div>
                  <div class="step-icon">
                    <i class="fas fa-graduation-cap"></i>
                  </div>
                  <h4 class="step-title">Onboarding e Treinamento</h4>
                  <p class="step-description">Receba treinamento completo sobre o sistema de gaseificação e preparação das bebidas artesanais.</p>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="step-card" data-testid="step-card-3">
                  <div class="step-number">03</div>
                  <div class="step-icon">
                    <i class="fas fa-rocket"></i>
                  </div>
                  <h4 class="step-title">Ativação e Vendas</h4>
                  <p class="step-description">Sistema instalado e operacional. Comece a vender sodas artesanais e peça xaropes via plataforma.</p>
                </div>
              </div>
            </div>
            <div class="fluxo-container mt-5" data-testid="fluxo-container">
              <h3 class="fluxo-title text-center mb-4" data-testid="fluxo-title">Fluxo Operacional Completo</h3>
              <div class="fluxo-timeline" data-testid="fluxo-timeline">
                <div class="fluxo-item" data-testid="fluxo-item-1">
                  <div class="fluxo-content">
                    <i class="fas fa-clipboard-check"></i>
                    <h5>Cadastro</h5>
                    <p>Cliente ou franqueado registra estabelecimento</p>
                  </div>
                </div>
                <div class="fluxo-arrow">
                  <i class="fas fa-arrow-right"></i>
                </div>
                <div class="fluxo-item" data-testid="fluxo-item-2">
                  <div class="fluxo-content">
                    <i class="fas fa-file-contract"></i>
                    <h5>Contrato</h5>
                    <p>Assinatura digital e aprovação Preshh</p>
                  </div>
                </div>
                <div class="fluxo-arrow">
                  <i class="fas fa-arrow-right"></i>
                </div>
                <div class="fluxo-item" data-testid="fluxo-item-3">
                  <div class="fluxo-content">
                    <i class="fas fa-box"></i>
                    <h5>Instalação</h5>
                    <p>Sistema Preshh instalado e treinamento</p>
                  </div>
                </div>
                <div class="fluxo-arrow">
                  <i class="fas fa-arrow-right"></i>
                </div>
                <div class="fluxo-item" data-testid="fluxo-item-4">
                  <div class="fluxo-content">
                    <i class="fas fa-shopping-cart"></i>
                    <h5>Pedido</h5>
                    <p>Solicitação de xaropes via plataforma</p>
                  </div>
                </div>
                <div class="fluxo-arrow">
                  <i class="fas fa-arrow-right"></i>
                </div>
                <div class="fluxo-item" data-testid="fluxo-item-5">
                  <div class="fluxo-content">
                    <i class="fas fa-truck"></i>
                    <h5>Entrega</h5>
                    <p>Distribuidor DVG fatura e entrega</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
        <section class="plataforma-section py-5" data-testid="plataforma-section">
          <div class="container">
            <div class="text-center mb-5">
              <span class="section-label" data-testid="plataforma-label">TECNOLOGIA</span>
              <h2 class="section-title" data-testid="plataforma-title">Nossa Plataforma Inovadora</h2>
              <p class="section-subtitle" data-testid="plataforma-subtitle">Gestão completa e integrada em um só lugar</p>
            </div>
            <div class="row g-4">
              <div class="col-md-6 col-lg-4">
                <div class="modulo-card" data-testid="modulo-card-1">
                  <div class="modulo-icon-wrapper">
                    <i class="fas fa-users-cog modulo-icon"></i>
                  </div>
                  <h4 class="modulo-title">Gestão de Clientes</h4>
                  <p class="modulo-description">Pipeline comercial visual, controle de status e acompanhamento de contratos em tempo real.</p>
                </div>
              </div>
              <div class="col-md-6 col-lg-4">
                <div class="modulo-card" data-testid="modulo-card-2">
                  <div class="modulo-icon-wrapper">
                    <i class="fas fa-file-signature modulo-icon"></i>
                  </div>
                  <h4 class="modulo-title">Contratos Digitais</h4>
                  <p class="modulo-description">Geração automática, assinatura eletrônica e controle de status com trilha de auditoria completa.</p>
                </div>
              </div>
              <div class="col-md-6 col-lg-4">
                <div class="modulo-card" data-testid="modulo-card-3">
                  <div class="modulo-icon-wrapper">
                    <i class="fas fa-box-open modulo-icon"></i>
                  </div>
                  <h4 class="modulo-title">Pedidos Automatizados</h4>
                  <p class="modulo-description">Sistema centralizado de pedidos com aprovação automática baseada em status financeiro.</p>
                </div>
              </div>
              <div class="col-md-6 col-lg-4">
                <div class="modulo-card" data-testid="modulo-card-4">
                  <div class="modulo-icon-wrapper">
                    <i class="fas fa-wallet modulo-icon"></i>
                  </div>
                  <h4 class="modulo-title">Gestão Financeira</h4>
                  <p class="modulo-description">Controle simplificado de pagamentos com bloqueio automático de inadimplentes.</p>
                </div>
              </div>
              <div class="col-md-6 col-lg-4">
                <div class="modulo-card" data-testid="modulo-card-5">
                  <div class="modulo-icon-wrapper">
                    <i class="fas fa-graduation-cap modulo-icon"></i>
                  </div>
                  <h4 class="modulo-title">Treinamentos</h4>
                  <p class="modulo-description">Cursos online completos para equipes DVG, franqueados Preshh e clientes finais.</p>
                </div>
              </div>
              <div class="col-md-6 col-lg-4">
                <div class="modulo-card" data-testid="modulo-card-6">
                  <div class="modulo-icon-wrapper">
                    <i class="fas fa-chart-pie modulo-icon"></i>
                  </div>
                  <h4 class="modulo-title">Dashboard e KPIs</h4>
                  <p class="modulo-description">Visualização em tempo real de contratos, pedidos, volume e indicadores de performance.</p>
                </div>
              </div>
            </div>
          </div>
        </section>
        <section class="beneficios-section py-5" id="beneficios" data-testid="beneficios-section">
          <div class="container">
            <div class="text-center mb-5">
              <span class="section-label" data-testid="beneficios-label">VANTAGENS</span>
              <h2 class="section-title" data-testid="beneficios-title">Benefícios para o Seu Negócio</h2>
              <p class="section-subtitle" data-testid="beneficios-subtitle">Muito mais que bebidas: uma solução completa de crescimento</p>
            </div>
            <div class="row g-4 align-items-center">
              <div class="col-lg-6">
                <div class="beneficio-group" data-testid="beneficio-group">
                  <div class="beneficio-item" data-testid="beneficio-item-1">
                    <div class="beneficio-icon-circle">
                      <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="beneficio-text">
                      <h4>Margens Superiores</h4>
                      <p>Custo fixo de R$ 45,00 por garrafa garante margens muito superiores aos refrigerantes tradicionais.</p>
                    </div>
                  </div>
                  <div class="beneficio-item" data-testid="beneficio-item-2">
                    <div class="beneficio-icon-circle">
                      <i class="fas fa-infinity"></i>
                    </div>
                    <div class="beneficio-text">
                      <h4>Recorrência Garantida</h4>
                      <p>Sistema de pedidos automatizado facilita recompras regulares de insumos.</p>
                    </div>
                  </div>
                  <div class="beneficio-item" data-testid="beneficio-item-3">
                    <div class="beneficio-icon-circle">
                      <i class="fas fa-medal"></i>
                    </div>
                    <div class="beneficio-text">
                      <h4>Experiência Premium</h4>
                      <p>Bebidas artesanais padronizadas com qualidade profissional em todas as unidades.</p>
                    </div>
                  </div>
                  <div class="beneficio-item" data-testid="beneficio-item-4">
                    <div class="beneficio-icon-circle">
                      <i class="fas fa-headphones"></i>
                    </div>
                    <div class="beneficio-text">
                      <h4>Suporte Completo</h4>
                      <p>Consultoria técnica, treinamento contínuo e suporte operacional 24/7.</p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="beneficio-visual" data-testid="beneficio-visual">
                  <img alt="Benefícios Soda Perfeita" class="img-fluid rounded-4 shadow-lg" src="https://images.unsplash.com/photo-1534260164206-2a3a4a72891d?crop=entropy&amp;cs=srgb&amp;fm=jpg&amp;ixid=M3w3NTY2Njd8MHwxfHNlYXJjaHwxfHxzb2RhJTIwYm90dGxlfGVufDB8fHx8MTc2MjQ2MjUzMXww&amp;ixlib=rb-4.1.0&amp;q=85">
                  <div class="beneficio-highlight" data-testid="beneficio-highlight">
                    <div class="highlight-content">
                      <i class="fas fa-trophy"></i>
                      <h5>+40% de Margem</h5>
                      <p>vs. refrigerantes tradicionais</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
        <section class="tiers-section py-5" id="tiers" data-testid="tiers-section">
          <div class="container">
            <div class="text-center mb-5">
              <span class="section-label" data-testid="tiers-label">MERITOCRACIA</span>
              <h2 class="section-title" data-testid="tiers-title">Programas de Tiers e Recompensas</h2>
              <p class="section-subtitle" data-testid="tiers-subtitle">Quanto melhor sua performance, maiores os benefícios exclusivos</p>
            </div>
            <div class="row g-4">
              <div class="col-lg-4">
                <div class="tier-card tier-basico" data-testid="tier-card-1">
                  <div class="tier-header">
                    <div class="tier-badge">
                      <i class="fas fa-seedling"></i>
                    </div>
                    <h3 class="tier-name">Tier 1 - Básico</h3>
                    <p class="tier-subtitle">Iniciantes no programa</p>
                  </div>
                  <div class="tier-body">
                    <h4 class="tier-section-title">Entregáveis Comuns</h4>
                    <ul class="tier-benefits-list">
                      <li>
                        <i class="fas fa-check"></i>Acesso ao sistema Preshh
                      </li>
                      <li>
                        <i class="fas fa-check"></i>Xaropes DVG a R$ 45/unid
                      </li>
                      <li>
                        <i class="fas fa-check"></i>Dashboard de desempenho
                      </li>
                      <li>
                        <i class="fas fa-check"></i>Treinamento básico
                      </li>
                      <li>
                        <i class="fas fa-check"></i>Suporte técnico padrão
                      </li>
                    </ul>
                    <h4 class="tier-section-title mt-4">Recompensas Exclusivas</h4>
                    <ul class="tier-rewards-list">
                      <li>
                        <i class="fas fa-gift"></i>
                        <strong>4 garrafas</strong> de xarope
                      </li>
                      <li>
                        <i class="fas fa-gift"></i>Material promocional básico
                      </li>
                      <li>
                        <i class="fas fa-gift"></i>Acesso a sabores padrão
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="tier-card tier-performance" data-testid="tier-card-2">
                  <div class="tier-ribbon">Popular</div>
                  <div class="tier-header">
                    <div class="tier-badge">
                      <i class="fas fa-rocket"></i>
                    </div>
                    <h3 class="tier-name">Tier 2 - Performance</h3>
                    <p class="tier-subtitle">Clientes consistentes</p>
                  </div>
                  <div class="tier-body">
                    <h4 class="tier-section-title">Entregáveis Comuns</h4>
                    <ul class="tier-benefits-list">
                      <li>
                        <i class="fas fa-check"></i>Acesso ao sistema Preshh
                      </li>
                      <li>
                        <i class="fas fa-check"></i>Xaropes DVG a R$ 45/unid
                      </li>
                      <li>
                        <i class="fas fa-check"></i>Dashboard de desempenho
                      </li>
                      <li>
                        <i class="fas fa-check"></i>Treinamento avançado
                      </li>
                      <li>
                        <i class="fas fa-check"></i>Suporte prioritário
                      </li>
                    </ul>
                    <h4 class="tier-section-title mt-4">Recompensas Exclusivas</h4>
                    <ul class="tier-rewards-list">
                      <li>
                        <i class="fas fa-gift"></i>
                        <strong>12 garrafas</strong> de xarope
                      </li>
                      <li>
                        <i class="fas fa-gift"></i>Material promocional premium
                      </li>
                      <li>
                        <i class="fas fa-gift"></i>Amostras de novos sabores
                      </li>
                      <li>
                        <i class="fas fa-gift"></i>Prioridade em suporte
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="tier-card tier-excelencia" data-testid="tier-card-3">
                  <div class="tier-header">
                    <div class="tier-badge">
                      <i class="fas fa-crown"></i>
                    </div>
                    <h3 class="tier-name">Tier 3 - Excelência</h3>
                    <p class="tier-subtitle">Alta performance</p>
                  </div>
                  <div class="tier-body">
                    <h4 class="tier-section-title">Entregáveis Comuns</h4>
                    <ul class="tier-benefits-list">
                      <li>
                        <i class="fas fa-check"></i>Acesso ao sistema Preshh
                      </li>
                      <li>
                        <i class="fas fa-check"></i>Xaropes DVG a R$ 45/unid
                      </li>
                      <li>
                        <i class="fas fa-check"></i>Dashboard premium
                      </li>
                      <li>
                        <i class="fas fa-check"></i>Treinamento VIP
                      </li>
                      <li>
                        <i class="fas fa-check"></i>Gerente de conta dedicado
                      </li>
                    </ul>
                    <h4 class="tier-section-title mt-4">Recompensas Exclusivas</h4>
                    <ul class="tier-rewards-list">
                      <li>
                        <i class="fas fa-gift"></i>
                        <strong>25 garrafas</strong> de xarope
                      </li>
                      <li>
                        <i class="fas fa-gift"></i>Subsídio R$ 90/mês equipamento
                      </li>
                      <li>
                        <i class="fas fa-gift"></i>Material personalizado
                      </li>
                      <li>
                        <i class="fas fa-gift"></i>Acesso antecipado novidades
                      </li>
                      <li>
                        <i class="fas fa-gift"></i>Participação em cases
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
        <section class="cta-section py-5" data-testid="cta-section">
          <div class="container">
            <div class="cta-box" data-testid="cta-box">
              <div class="row align-items-center">
                <div class="col-lg-8">
                  <h2 class="cta-title" data-testid="cta-title">
                    <i class="fas fa-lightbulb me-3"></i>Pronto para Revolucionar Seu Negócio?
                  </h2>
                  <p class="cta-text" data-testid="cta-text">Junte-se a centenas de estabelecimentos que já aumentaram seu ticket médio e lucratividade com Soda Perfeita</p>
                </div>
                <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                  <a href="#contato" class="btn btn-light btn-lg cta-button" data-testid="cta-button">
                    <i class="fas fa-paper-plane me-2"></i>Solicitar Demonstração </a>
                </div>
              </div>
            </div>
          </div>
        </section>
        <section class="contato-section py-5" id="contato" data-testid="contato-section">
          <div class="container">
            <div class="text-center mb-5">
              <span class="section-label" data-testid="contato-label">FALE CONOSCO</span>
              <h2 class="section-title" data-testid="contato-title">Entre em Contato</h2>
              <p class="section-subtitle" data-testid="contato-subtitle">Nossa equipe está pronta para ajudar você a começar</p>
            </div>
            <div class="row g-4">
              <div class="col-lg-6">
                <div class="contato-info" data-testid="contato-info">
                  <h3 class="contato-info-title">Informações de Contato</h3>
                  <div class="contato-info-item" data-testid="contato-info-phone">
                    <div class="contato-info-icon">
                      <i class="fas fa-phone"></i>
                    </div>
                    <div>
                      <h5>Telefone</h5>
                      <p>(11) 98765-4321</p>
                    </div>
                  </div>
                  <div class="contato-info-item" data-testid="contato-info-email">
                    <div class="contato-info-icon">
                      <i class="fas fa-envelope"></i>
                    </div>
                    <div>
                      <h5>E-mail</h5>
                      <p>contato@sodaperfeita.com.br</p>
                    </div>
                  </div>
                  <div class="contato-info-item" data-testid="contato-info-hours">
                    <div class="contato-info-icon">
                      <i class="fas fa-clock"></i>
                    </div>
                    <div>
                      <h5>Horário de Atendimento</h5>
                      <p>Segunda a Sexta: 9h às 18h</p>
                    </div>
                  </div>
                  <div class="contato-social mt-4" data-testid="contato-social">
                    <h5 class="mb-3">Redes Sociais</h5>
                    <div class="social-links">
                      <a href="#" class="social-link" data-testid="social-instagram">
                        <i class="fab fa-instagram"></i>
                      </a>
                      <a href="#" class="social-link" data-testid="social-facebook">
                        <i class="fab fa-facebook"></i>
                      </a>
                      <a href="#" class="social-link" data-testid="social-linkedin">
                        <i class="fab fa-linkedin"></i>
                      </a>
                      <a href="#" class="social-link" data-testid="social-whatsapp">
                        <i class="fab fa-whatsapp"></i>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="contato-form-wrapper" data-testid="contato-form">
                  <form class="contato-form">
                    <div class="mb-3">
                      <label for="nome" class="form-label">Nome Completo *</label>
                      <input class="form-control" id="nome" required="" data-testid="form-input-nome" type="text" value="" name="nome">
                    </div>
                    <div class="mb-3">
                      <label for="email" class="form-label">E-mail *</label>
                      <input class="form-control" id="email" required="" data-testid="form-input-email" type="email" value="" name="email">
                    </div>
                    <div class="mb-3">
                      <label for="telefone" class="form-label">Telefone *</label>
                      <input class="form-control" id="telefone" required="" data-testid="form-input-telefone" type="tel" value="" name="telefone">
                    </div>
                    <div class="mb-3">
                      <label for="estabelecimento" class="form-label">Nome do Estabelecimento *</label>
                      <input class="form-control" id="estabelecimento" required="" data-testid="form-input-estabelecimento" type="text" value="" name="estabelecimento">
                    </div>
                    <div class="mb-3">
                      <label for="mensagem" class="form-label">Mensagem</label>
                      <textarea class="form-control" id="mensagem" name="mensagem" rows="4" data-testid="form-input-mensagem"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" data-testid="form-submit-button">
                      <i class="fas fa-paper-plane me-2"></i>Enviar Mensagem </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </section>
        <footer class="footer py-5" data-testid="footer">
          <div class="container">
            <div class="row g-4">
              <div class="col-lg-4">
                <div class="footer-brand" data-testid="footer-brand">
                  <h3 class="footer-logo">
                    <i class="fas fa-glass-whiskey me-2"></i>Soda Perfeita
                  </h3>
                  <p class="footer-tagline">Perfeita porque é feita por você</p>
                  <p class="footer-description">Uma parceria Preshh + DVG para revolucionar o mercado de bebidas artesanais no foodservice brasileiro.</p>
                </div>
              </div>
              <div class="col-lg-2 col-6">
                <h5 class="footer-title">Links Rápidos</h5>
                <ul class="footer-links">
                  <li>
                    <a href="#sobre" data-testid="footer-link-sobre">Sobre</a>
                  </li>
                  <li>
                    <a href="#como-funciona" data-testid="footer-link-como-funciona">Como Funciona</a>
                  </li>
                  <li>
                    <a href="#beneficios" data-testid="footer-link-beneficios">Benefícios</a>
                  </li>
                  <li>
                    <a href="#tiers" data-testid="footer-link-tiers">Programas</a>
                  </li>
                </ul>
              </div>
              <div class="col-lg-3 col-6">
                <h5 class="footer-title">Recursos</h5>
                <ul class="footer-links">
                  <li>
                    <a href="#" data-testid="footer-link-treinamentos">Treinamentos</a>
                  </li>
                  <li>
                    <a href="#" data-testid="footer-link-materiais">Materiais</a>
                  </li>
                  <li>
                    <a href="#" data-testid="footer-link-suporte">Suporte</a>
                  </li>
                  <li>
                    <a href="#" data-testid="footer-link-faq">FAQ</a>
                  </li>
                </ul>
              </div>
              <div class="col-lg-3">
                <h5 class="footer-title">Parceiros</h5>
                <div class="footer-partners" data-testid="footer-partners">
                  <div class="partner-logo" data-testid="partner-preshh">
                    <i class="fas fa-handshake"></i>
                    <span>Preshh</span>
                  </div>
                  <div class="partner-logo" data-testid="partner-dvg">
                    <i class="fas fa-award"></i>
                    <span>DaVinci / Kerry</span>
                  </div>
                </div>
              </div>
            </div>
            <hr class="footer-divider">
            <div class="row">
              <div class="col-md-6">
                <p class="footer-copyright" data-testid="footer-copyright">© 2025 Soda Perfeita. Todos os direitos reservados.</p>
              </div>
              <div class="col-md-6 text-md-end">
                <div class="footer-legal" data-testid="footer-legal">
                  <a href="#" data-testid="footer-link-privacidade">Política de Privacidade</a>
                  <span class="mx-2">|</span>
                  <a href="#" data-testid="footer-link-termos">Termos de Uso</a>
                </div>
              </div>
            </div>
          </div>
        </footer>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>