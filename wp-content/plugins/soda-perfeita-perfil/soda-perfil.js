(function($){
  $(function(){
    const form = $('#pilot-form');
    if (form.length) {
      form.on('submit', function(e){
        e.preventDefault();
        const formData = new FormData(this);
        const dados = {};
        form.find('input, select, textarea').each(function(){
          const el = $(this);
          const name = el.attr('name');
          if (!name) return;
          if (el.attr('type') === 'checkbox') {
            if (el.is(':checked')) {
              dados[name.replace('[]','')] = dados[name.replace('[]','')] || [];
              dados[name.replace('[]','')].push(el.val());
            }
          } else if (el.attr('type') === 'radio') {
            if (el.is(':checked')) {
              dados[name] = el.val();
            }
          } else {
            dados[name] = el.val();
          }
        });

        $.post(SodaPerfil.ajaxUrl, {
          action: 'soda_perfeita_perfil_submit',
          nonce: SodaPerfil.nonce,
          dados
        }).done(function(res){
          if (res.success) {
            alert(res.data.message);
            form[0].reset();
            window.location.reload();
          } else {
            alert(res.data ? res.data.message : 'Erro ao enviar.');
          }
        }).fail(function(){
          alert('Erro de comunicação.');
        });
      });
    }

    const tableEl = $('#soda-perfil-table');
    if (tableEl.length) {
      const fmt = function(val) {
        if (Array.isArray(val)) {
          return val.join(', ');
        }
        if (typeof val === 'string' && val.startsWith('[')) {
          try {
            const parsed = JSON.parse(val);
            if (Array.isArray(parsed)) return parsed.join(', ');
          } catch (e) {
            return val;
          }
        }
        return val || '';
      };

      let currentProfile = null;

      const dt = tableEl.DataTable({
        autoWidth: false,
        responsive: true,
        ajax: {
          url: SodaPerfil.ajaxUrl,
          type: 'POST',
          data: { action: 'soda_perfeita_perfil_list', nonce: SodaPerfil.nonce },
          dataSrc: function(json){ return json.success ? json.data : []; }
        },
        columns: [
          { data: 'id' },
          { data: 'created_at' },
          { data: 'nome_negocio' },
          { data: 'cidade_estado' },
          { data: 'tipo_negocio' },
          { data: 'segmento_clientes' },
          { data: 'atendimento_turnos' },
          { data: 'principal_diferencial' },
          { data: 'experiencia_bebidas' },
          { data: 'delivery' },
          { data: 'coqueteis' },
          { data: 'ticket_medio_drinks' },
          { data: 'mao_obra' },
          { data: 'mao_obra_comentarios' },
          { data: 'gelo_operacao' },
          { data: 'frequencia_eventos' },
          { data: 'expectativas' },
          { data: 'satisfacao_geral' },
          { data: 'maquina_gelo' },
          { data: 'consumo_semanal_gelo' },
          { data: 'valor_gasto_gelo' },
          { data: 'observacoes_finais' },
          {
            data: null,
            orderable: false,
            className: 'text-nowrap',
            render: function(data, type, row){
              return '<div class="d-flex gap-2">'+
                '<button class="btn btn-sm btn-outline-primary btn-view-perfil" data-id="'+row.id+'">Ver / Imprimir</button>'+
                '<button class="btn btn-sm btn-outline-secondary btn-edit-perfil" data-id="'+row.id+'">Editar</button>'+
              '</div>';
            }
          }
        ],
        order: [[1,'desc']]
      });

      const modalEl = document.getElementById('perfilModal');
      const perfilModal = modalEl ? new bootstrap.Modal(modalEl) : null;
      const viewContent = $('#perfilViewContent');
      const editForm = $('#perfilEditForm');
      const btnToggleEdit = $('#btnToggleEdit');
      const btnCancelEdit = $('#btnCancelEdit');
      const btnPrint = $('#btnPrintPerfil');
      const editIdInput = $('#perfilEditId');

            function renderView(data){
        const fields = [
          ["ID", fmt(data.id)],
          ["Criado em", fmt(data.created_at)],
          ["Nome do negocio", fmt(data.nome_negocio)],
          ["Cidade/Estado", fmt(data.cidade_estado)],
          ["Tipo de negocio", fmt(data.tipo_negocio)],
          ["Segmento clientes", fmt(data.segmento_clientes)],
          ["Atendimento / turnos", fmt(data.atendimento_turnos)],
          ["Principal diferencial", fmt(data.principal_diferencial)],
          ["Experiencia bebidas", fmt(data.experiencia_bebidas)],
          ["Delivery", fmt(data.delivery)],
          ["Coqueteis", fmt(data.coqueteis)],
          ["Ticket medio drinks", fmt(data.ticket_medio_drinks)],
          ["Mao de obra", fmt(data.mao_obra)],
          ["Comentarios mao de obra", fmt(data.mao_obra_comentarios)],
          ["Gelo operacao", fmt(data.gelo_operacao)],
          ["Frequencia eventos", fmt(data.frequencia_eventos)],
          ["Expectativas", fmt(data.expectativas)],
          ["Satisfacao", fmt(data.satisfacao_geral)],
          ["Maquina de gelo", fmt(data.maquina_gelo)],
          ["Consumo semanal de gelo", fmt(data.consumo_semanal_gelo)],
          ["Valor gasto com gelo", fmt(data.valor_gasto_gelo)],
          ["Observacoes", fmt(data.observacoes_finais)]
        ];
        const html = fields.map(function(item){
          return '<dt class="col-sm-4">'+item[0]+'</dt><dd class="col-sm-8">'+(item[1] || '-')+'</dd>';
        }).join('');
        viewContent.html(html);
        $("#perfilModalLabel").text('Perfil #' + data.id + ' - ' + (data.nome_negocio || ''));
      }

      function fillEditForm(data){
        editIdInput.val(data.id);
        $('#edit_nome_negocio').val(data.nome_negocio || '');
        $('#edit_cidade_estado').val(data.cidade_estado || '');
        $('#edit_atendimento_turnos').val(data.atendimento_turnos || '');
        $('#edit_delivery').val(data.delivery || '');
        $('#edit_mao_obra').val(data.mao_obra || '');
        $('#edit_satisfacao_geral').val(data.satisfacao_geral || '');
        $('#edit_consumo_semanal_gelo').val(data.consumo_semanal_gelo || '');
        $('#edit_valor_gasto_gelo').val(data.valor_gasto_gelo || '');
        $('#edit_expectativas').val(data.expectativas || '');
        $('#edit_observacoes_finais').val(data.observacoes_finais || '');
      }

      function toggleEdit(showEdit){
        if (showEdit) {
          editForm.removeClass('d-none');
          btnToggleEdit.addClass('d-none');
        } else {
          editForm.addClass('d-none');
          btnToggleEdit.removeClass('d-none');
        }
      }

      function fetchProfile(id, toEdit){
        $.post(SodaPerfil.ajaxUrl, {
          action: 'soda_perfeita_perfil_get',
          nonce: SodaPerfil.nonce,
          id: id
        }).done(function(res){
          if (!res.success) {
            alert(res.data ? res.data.message : 'Erro ao carregar.');
            return;
          }
          currentProfile = res.data;
          renderView(currentProfile);
          fillEditForm(currentProfile);
          toggleEdit(toEdit);
          if (perfilModal) perfilModal.show();
        }).fail(function(){
          alert('Erro de comunicação.');
        });
      }

      tableEl.on('click', '.btn-view-perfil', function(){
        const id = $(this).data('id');
        fetchProfile(id, false);
      });

      tableEl.on('click', '.btn-edit-perfil', function(){
        const id = $(this).data('id');
        fetchProfile(id, true);
      });

      btnToggleEdit.on('click', function(){
        toggleEdit(true);
      });

      btnCancelEdit.on('click', function(){
        toggleEdit(false);
      });

      btnPrint.on('click', function(){
        const content = viewContent.html();
        const w = window.open('', 'print_perfil');
        w.document.write('<html><head><title>Imprimir Perfil</title>');
        w.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">');
        w.document.write('<style>@media print { .hide-print { display:none !important; } }</style>');
        w.document.write('</head><body class="p-4"><h3>'+$('#perfilModalLabel').text()+'</h3><dl class="row">'+content+'</dl></body></html>');
        w.document.close();
        w.focus();
        w.print();
        w.close();
      });

      editForm.on('submit', function(e){
        e.preventDefault();
        const id = editIdInput.val();
        const dados = {
          nome_negocio: $('#edit_nome_negocio').val(),
          cidade_estado: $('#edit_cidade_estado').val(),
          atendimento_turnos: $('#edit_atendimento_turnos').val(),
          delivery: $('#edit_delivery').val(),
          mao_obra: $('#edit_mao_obra').val(),
          satisfacao_geral: $('#edit_satisfacao_geral').val(),
          consumo_semanal_gelo: $('#edit_consumo_semanal_gelo').val(),
          valor_gasto_gelo: $('#edit_valor_gasto_gelo').val(),
          expectativas: $('#edit_expectativas').val(),
          observacoes_finais: $('#edit_observacoes_finais').val()
        };

        if (currentProfile) {
          $.extend(dados, {
            tipo_negocio: currentProfile.tipo_negocio || [],
            segmento_clientes: currentProfile.segmento_clientes || '',
            principal_diferencial: currentProfile.principal_diferencial || '',
            experiencia_bebidas: currentProfile.experiencia_bebidas || '',
            coqueteis: currentProfile.coqueteis || [],
            ticket_medio_drinks: currentProfile.ticket_medio_drinks || '',
            mao_obra_comentarios: currentProfile.mao_obra_comentarios || '',
            gelo_operacao: currentProfile.gelo_operacao || '',
            frequencia_eventos: currentProfile.frequencia_eventos || [],
            maquina_gelo: currentProfile.maquina_gelo || ''
          });
        }

        $.post(SodaPerfil.ajaxUrl, {
          action: 'soda_perfeita_perfil_update',
          nonce: SodaPerfil.nonce,
          id: id,
          dados: dados
        }).done(function(res){
          if (res.success) {
            alert(res.data.message);
            toggleEdit(false);
            dt.ajax.reload(null, false);
            fetchProfile(id, false);
          } else {
            alert(res.data ? res.data.message : 'Erro ao salvar.');
          }
        }).fail(function(){
          alert('Erro de comunicação.');
        });
      });
    }
  });
})(jQuery);

