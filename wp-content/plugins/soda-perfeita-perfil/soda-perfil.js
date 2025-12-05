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
          { data: 'delivery' },
          { data: 'mao_obra' },
          { data: 'satisfacao_geral' },
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
          ['ID', data.id],
          ['Criado em', data.created_at],
          ['Nome do negócio', data.nome_negocio],
          ['Cidade/Estado', data.cidade_estado],
          ['Delivery', data.delivery],
          ['Atendimento / turnos', data.atendimento_turnos],
          ['Mão de obra', data.mao_obra],
          ['Satisfação', data.satisfacao_geral],
          ['Expectativas', data.expectativas],
          ['Consumo semanal de gelo', data.consumo_semanal_gelo],
          ['Valor gasto com gelo', data.valor_gasto_gelo],
          ['Observações', data.observacoes_finais]
        ];
        const html = fields.map(function(item){
          return '<dt class="col-sm-4">'+item[0]+'</dt><dd class="col-sm-8">'+(item[1] || '-')+'</dd>';
        }).join('');
        viewContent.html(html);
        $('#perfilModalLabel').text('Perfil #' + data.id + ' - ' + (data.nome_negocio || ''));
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
          renderView(res.data);
          fillEditForm(res.data);
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
