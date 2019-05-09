$(document).ready(() => {
	$('#documentacao').on('click', () => {
        //$('#pagina').load('documentacao.html')
        $.post('documentacao.html', data => {
            $('#pagina').html(data)
        })
    })

    $('#suporte').on('click', () => {
        //$('#pagina').load('suporte.html')
        $.post('suporte.html', data => {
            $('#pagina').html(data)
        })
    })

    $('#index').on('click', () => {
        window.location.assign("index.html");
    })

    //Ajax
    $.ajax({
        type: 'GET',
        url: 'app/app.php',
        dataType: 'json', 
        success: dados => {
            $('#clientesativos').html(dados.clientesAtivos),
            $('#clientesinativos').html(dados.clientesInativos),
            $('#totalreclamacoes').html(dados.totalReclamaoes),
            $('#totalelogios').html(dados.totalElogios),
            $('#totalsugestoes').html(dados.totalSugestoes)
        },
        erro: erro => {console.log(erro)}
    })


    $('#competencia').on('change', e => {

        let competencia = $(e.target).val()

        $.ajax({
            type: 'GET',
            url: 'app/app.php',
            data: `competencia=${competencia}`, //x-www-form-urlencoded
            dataType: 'json', 
            success: dados => {
                $('#numeroVendas').html(dados.numeroVendas),
                $('#totalVendas').html(dados.totalVendas),
                $('#totalDespesas').html(dados.totalDespesas)
            },
            erro: erro => {console.log(erro)}
        })

        //Método, URL, dados, sucesso, erro
    })
})