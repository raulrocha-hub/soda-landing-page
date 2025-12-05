jQuery(function($) {
    function openModal(html) {
        var $modal = $('#soda-cashback-modal');
        $('#soda-cashback-modal-body').html(html);
        $modal.show();
    }

    function closeModal() {
        $('#soda-cashback-modal').hide();
        $('#soda-cashback-modal-body').html('');
    }

    $(document).on('click', '.soda-cashback-view-statement', function(e) {
        e.preventDefault();

        var userId = $(this).data('user-id');

        $.post(
            SodaCashbackWallet.ajax_url,
            {
                action: 'soda_cashback_get_statement',
                nonce: SodaCashbackWallet.nonce,
                user_id: userId
            },
            function(response) {
                if (response.success) {
                    openModal(response.data.html);
                } else {
                    alert(response.data.message || 'Erro ao carregar extrato.');
                }
            }
        );
    });

    $(document).on('click', '.soda-cashback-modal-close, .soda-cashback-modal-backdrop', function() {
        closeModal();
    });
});
