<?php
/**
 * Form Template for Pedido
 * 
 * @package SodaPerfeita
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="soda-form-pedido-wrapper">
    <div class="form-header">
        <h2>Solicitar Pedido de Xarope</h2>
        <p>Preencha os dados abaixo para solicitar seu pedido</p>
    </div>

    <?php if (isset($erro) && $erro): ?>
    <div class="soda-form-error">
        <p><?php echo esc_html($erro); ?></p>
    </div>
    <?php endif; ?>

    <?php if (isset($sucesso) && $sucesso): ?>
    <div class="soda-form-success">
        <p><?php echo esc_html($sucesso); ?></p>
    </div>
    <?php endif; ?>

    <form id="soda-form-pedido" method="post" class="soda-pedido-form">
        <?php wp_nonce_field('soda_solicitar_pedido', 'soda_nonce'); ?>
        
        <div class="form-section">
            <h3>Informações do Pedido</h3>
            
            <div class="form-group">
                <label for="quantidade_garrafas">Quantidade de Garrafas *</label>
                <select name="quantidade_garrafas" id="quantidade_garrafas" required>
                    <option value="">Selecione a quantidade</option>
                    <option value="4">4 garrafas - R$ 180,00</option>
                    <option value="8">8 garrafas - R$ 360,00</option>
                    <option value="12">12 garrafas - R$ 540,00</option>
                    <option value="16">16 garrafas - R$ 720,00</option>
                    <option value="20">20 garrafas - R$ 900,00</option>
                    <option value="24">24 garrafas - R$ 1.080,00</option>
                </select>
                <small>Quantidade mínima: 4 garrafas</small>
            </div>

            <div class="form-group">
                <label for="data_entrega_preferida">Data de Entrega Preferida</label>
                <input type="date" name="data_entrega_preferida" id="data_entrega_preferida" 
                       min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                <small>Sugerimos datas com pelo menos 1 dia de antecedência</small>
            </div>

            <div class="form-group">
                <label for="observacoes">Observações do Pedido</label>
                <textarea name="observacoes" id="observacoes" rows="3" 
                          placeholder="Alguma observação especial sobre o pedido..."></textarea>
            </div>
        </div>

        <div class="form-section">
            <h3>Resumo do Pedido</h3>
            <div class="resumo-pedido">
                <div class="resumo-item">
                    <span>Quantidade:</span>
                    <span id="resumo-quantidade">-</span>
                </div>
                <div class="resumo-item">
                    <span>Preço Unitário:</span>
                    <span id="resumo-unitario">R$ 45,00</span>
                </div>
                <div class="resumo-item total">
                    <span>Valor Total:</span>
                    <span id="resumo-total">R$ 0,00</span>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="button" class="btn btn-secondary" onclick="history.back()">
                Cancelar
            </button>
            <button type="submit" class="btn btn-primary" id="btn-submit">
                <span class="btn-text">Solicitar Pedido</span>
                <span class="btn-loading" style="display: none;">
                    <span class="spinner"></span> Processando...
                </span>
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('soda-form-pedido');
    const quantidadeSelect = document.getElementById('quantidade_garrafas');
    const resumoQuantidade = document.getElementById('resumo-quantidade');
    const resumoTotal = document.getElementById('resumo-total');
    const btnSubmit = document.getElementById('btn-submit');

    // Atualizar resumo quando quantidade mudar
    quantidadeSelect.addEventListener('change', function() {
        const quantidade = parseInt(this.value) || 0;
        const precoUnitario = 45.00;
        const total = quantidade * precoUnitario;

        resumoQuantidade.textContent = quantidade + ' garrafas';
        resumoTotal.textContent = 'R$ ' + total.toFixed(2).replace('.', ',');
    });

    // Submit do formulário
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        if (!quantidadeSelect.value) {
            alert('Por favor, selecione a quantidade de garrafas.');
            return;
        }

        // Mostrar loading
        const btnText = btnSubmit.querySelector('.btn-text');
        const btnLoading = btnSubmit.querySelector('.btn-loading');
        
        btnText.style.display = 'none';
        btnLoading.style.display = 'inline-flex';
        btnSubmit.disabled = true;

        // Enviar via AJAX
        const formData = new FormData(form);

        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.data.redirect_url || window.location.href;
            } else {
                alert(data.data || 'Erro ao processar pedido.');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao processar pedido.');
        })
        .finally(() => {
            btnText.style.display = 'inline-flex';
            btnLoading.style.display = 'none';
            btnSubmit.disabled = false;
        });
    });

    // Configurar data mínima
    const dataInput = document.getElementById('data_entrega_preferida');
    if (dataInput && !dataInput.min) {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        dataInput.min = tomorrow.toISOString().split('T')[0];
    }
});
</script>

<style>
.soda-form-pedido-wrapper {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
}

.form-header {
    text-align: center;
    margin-bottom: 30px;
}

.form-header h2 {
    color: #2c3e50;
    margin-bottom: 10px;
}

.form-section {
    background: white;
    padding: 25px;
    margin-bottom: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.form-section h3 {
    margin-top: 0;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #e0e0e0;
    color: #34495e;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    color: #2c3e50;
}

.form-group select,
.form-group input,
.form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    transition: border-color 0.3s ease;
}

.form-group select:focus,
.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
}

.form-group small {
    display: block;
    margin-top: 5px;
    color: #7f8c8d;
    font-size: 12px;
}

.resumo-pedido {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 6px;
}

.resumo-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #e9ecef;
}

.resumo-item:last-child {
    border-bottom: none;
}

.resumo-item.total {
    font-weight: bold;
    font-size: 18px;
    color: #2c3e50;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 15px;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #e0e0e0;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-primary {
    background: #3498db;
    color: white;
}

.btn-primary:hover:not(:disabled) {
    background: #2980b9;
}

.btn-secondary {
    background: #95a5a6;
    color: white;
}

.btn-secondary:hover:not(:disabled) {
    background: #7f8c8d;
}

.btn-loading {
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.spinner {
    width: 16px;
    height: 16px;
    border: 2px solid transparent;
    border-top: 2px solid currentColor;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.soda-form-error {
    background: #f8d7da;
    color: #721c24;
    padding: 12px 15px;
    border-radius: 4px;
    margin-bottom: 20px;
    border: 1px solid #f5c6cb;
}

.soda-form-success {
    background: #d4edda;
    color: #155724;
    padding: 12px 15px;
    border-radius: 4px;
    margin-bottom: 20px;
    border: 1px solid #c3e6cb;
}

@media (max-width: 768px) {
    .soda-form-pedido-wrapper {
        padding: 15px;
    }
    
    .form-section {
        padding: 20px;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>