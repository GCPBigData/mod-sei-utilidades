<?php

/**
 * @author Jaqueline Mendes
 * @since  11/09/2018
 */

require_once dirname(__FILE__) . '/../../SEI.php';

session_start();
SessaoSEI::getInstance()->validarLink();
PaginaSEI::getInstance()->setTipoPagina(InfraPagina::$TIPO_PAGINA_SIMPLES);

$isProcessoConcluido = array_key_exists('is_processo_concluido', $_GET) ? $_GET['is_processo_concluido'] : 0;
$isProcessoAutorizadoConcluir = array_key_exists('hdnIsConcluirProcesso', $_POST) ? $_POST['hdnIsConcluirProcesso'] : 0;

$strParametros = '';
if(isset($_GET['arvore'])){
    PaginaSEI::getInstance()->setBolArvore($_GET['arvore']);
    $strParametros .= '&arvore='.$_GET['arvore'];
}


$idProcedimento  = array_key_exists('id_procedimento', $_GET) ? $_GET['id_procedimento'] : $_POST['hdnIdProcedimento'];
$idProcedimento = trim($idProcedimento);
$urlInicial = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_utl_processo_listar&id_procedimento=' . $idProcedimento);



if ($isProcessoAutorizadoConcluir == 1) {
    $_POST['hdnIsConcluirProcesso'] = 0;
    $isProcessoAutorizadoConcluir = 0;

    $objEntradaConcluirProcessoAPI = new EntradaConcluirProcessoAPI();
    $objEntradaConcluirProcessoAPI->setIdProcedimento($idProcedimento);

    $objSEIRN = new SeiRN();
    $objSEIRN->concluirProcesso($objEntradaConcluirProcessoAPI);

}

if (!is_null($idProcedimento) && $idProcedimento != ''){
    $strParametros .= '&id_procedimento='.$idProcedimento;
}


//Acao �nica
$acaoPrincipal = 'md_utl_processo_listar';

//URL Base
$strUrlPadrao = 'controlador.php?acao=' . $acaoPrincipal;

// Vars
$isParametrizado = null;
$nomeStatus      = '';
$strTitulo       = 'Detalhamento do Processo ';
$arrCtrlVisualizacao = array();
$isUsuarioDuplicado = false;
//Rns
$objRegrasGerais           = new MdUtlRegrasGeraisRN();
$objMdUtlAdmUtlTpCtrlRN    = new MdUtlAdmTpCtrlDesempRN();
$objMdUtlAdmTpCtrlDTO      = new MdUtlAdmTpCtrlDesempDTO();
$objTriagemRN              = new MdUtlTriagemRN();
$objMdUtlControleDsmpRN    = new MdUtlControleDsmpRN();
$objAnaliseRN              = new MdUtlAnaliseRN();
$objMdUtlAdmTpCtrlUndRN    = new MdUtlAdmRelTpCtrlDesempUndRN();
$objMdUtlAdmPrmGrRN        = new MdUtlAdmPrmGrRN();
$objProcedimentoDTO        = $objRegrasGerais->getObjProcedimentoPorId($idProcedimento);


//Preenche Vars Principais
//Tipo de Controle
$idTipoControle = $objMdUtlAdmTpCtrlUndRN->getTipoControleUnidadeLogada();
if(!is_null($idTipoControle)) {
    $isParametrizado = $objMdUtlAdmUtlTpCtrlRN->verificaTipoControlePossuiParametrizacao($idTipoControle);
}

$objMdUtlAdmTpCtrlDTO->setNumIdMdUtlAdmTpCtrlDesemp($idTipoControle);
$objMdUtlAdmTpCtrlDTO->retStrStaFrequencia();
$objMdUtlAdmTpCtrlDTO->setNumMaxRegistrosRetorno(1);
$objDTOTipoControle = $objMdUtlAdmUtlTpCtrlRN->consultar($objMdUtlAdmTpCtrlDTO);

$staFrequencia = $objDTOTipoControle->getStrStaFrequencia();

$isPermiteAcoes = $objMdUtlControleDsmpRN->validaVisualizacaoUsuarioLogado($idTipoControle);

if($isPermiteAcoes){
    $isPermiteAcoes = $objRegrasGerais->validarSituacaoProcesso($idProcedimento);
    if($isPermiteAcoes){
        $isConcluido =  $objRegrasGerais->verificaConclusaoProcesso(array($idProcedimento));
        $isPermiteAcoes = $isConcluido ? false : true;
    }
}

$arrSituacao = MdUtlControleDsmpINT::retornaArrSituacoesControleDsmpCompleto();

//Status
$objControleDsmpDTO    = $objMdUtlControleDsmpRN->getObjControleDsmpAtivo($idProcedimento);
$idStatus             = MdUtlControleDsmpRN::$AGUARDANDO_FILA;
$idFila               = null;
$dthDataHoraAtual     = '';
$strNomeUsuarioAtual  = '';
$strSiglaUsuarioAtual = '';
$strDetalheAtual      = '';
$strStatusAtual       = 0;
$strTipoAcaoAtual     = '';
$idControleDsmp       = 0;


if(!is_null($objControleDsmpDTO)){
    $idStatus         = trim($objControleDsmpDTO->getStrStaAtendimentoDsmp());
    $idFila           = $objControleDsmpDTO->getNumIdMdUtlAdmFila();
    $dthDataHoraAtual = $objControleDsmpDTO->getDthAtual();
    $strNomeUsuarioAtual  = $objControleDsmpDTO->getStrNomeUsuarioAtual();
    $strSiglaUsuarioAtual = $objControleDsmpDTO->getStrSiglaUsuarioAtual();
    $strDetalheAtual  = $objControleDsmpDTO->getStrDetalhe();
    $strStatusAtual   = trim($objControleDsmpDTO->getStrStaAtendimentoDsmp());
    $strTipoAcaoAtual = $objControleDsmpDTO->getStrTipoAcao();
    $idControleDsmp    = $objControleDsmpDTO->getNumIdMdUtlControleDsmp();

    $isUsuarioDuplicado = $objControleDsmpDTO->getNumIdUsuarioDistribuicao() == SessaoSEI::getInstance()->getNumIdUsuario();
}

$nomeStatus       = $arrSituacao[$idStatus];

$isPossuiAnalise       = $objMdUtlControleDsmpRN->verificaTriagemPossuiAnalise($objControleDsmpDTO);
$strNumeroProcedimento = $objProcedimentoDTO->getStrProtocoloProcedimentoFormatado();
$msg107                = MdUtlMensagemINT::getMensagem(MdUtlMensagemINT::$MSG_UTL_107, array($strNumeroProcedimento,  SessaoSEI::getInstance()->getStrSiglaUnidadeAtual()));
$msg92                 = MdUtlMensagemINT::getMensagem(MdUtlMensagemINT::$MSG_UTL_92, array($strNumeroProcedimento));

//Controle de Urls
$idUsuarioDistrb     = !is_null($objControleDsmpDTO) ? $objControleDsmpDTO->getNumIdUsuarioDistribuicao() : null;
$isStatusAjustePrazo = !is_null($objControleDsmpDTO) && ($idStatus == MdUtlControleDsmpRN::$SUSPENSO || $idStatus == MdUtlControleDsmpRN::$INTERROMPIDO);
$idRevisaoAjustePrz  = $isStatusAjustePrazo &&  !is_null($objControleDsmpDTO) ?  $objControleDsmpDTO->getNumIdMdUtlRevisao() : null;

$arrCtrlUrls      = MdUtlControleDsmpINT::retornaUrlsAcessoDsmp($idStatus, $isPossuiAnalise, $idProcedimento, $idFila, $idUsuarioDistrb);

//Urls
$strLinkAssociarFila   = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_utl_controle_dsmp_associar&acao_origem=' . $_GET['acao'] . '&id_procedimento=' . $idProcedimento . '&id_tp_controle_desmp=' . $idTipoControle.'&is_detalhamento=1');
$strLinkIniciarTriagem = $arrCtrlUrls['TRIAGEM'];
$strLinkIniciarAnalise = $arrCtrlUrls['ANALISE'];
$strLinkIniciarRevisao = $arrCtrlUrls['REVISAO'];

$strLinkIniciarDistrb  = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_utl_distrib_usuario_cadastrar&acao_retorno=md_utl_processo_listar&acao_origem=md_utl_controle_dsmp_listar&id_procedimento=' . $idProcedimento.'&id_controle_dsmp='.$idControleDsmp.'&status='.$strStatusAtual.'&id_fila='.$idFila);
$strLinkAtribuir  = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_utl_atribuicao_automatica&acao_retorno=md_utl_processo_listar&acao_origem=md_utl_controle_dsmp_listar&id_procedimento=' . $idProcedimento.'&id_controle_dsmp='.$idControleDsmp.'&status='.$strStatusAtual.'&id_fila='.$idFila.'&id_tp_ctrl='.$idTipoControle);
$strUrlFechar          = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=procedimento_trabalhar&acao_retorno=procedimento_controlar&acao_origem=md_utl_controle_dsmp_listar&id_procedimento=' . $idProcedimento);
$strLinkConcluirProcesso =  $isProcessoConcluido == 1  ? SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_utl_processo_listar&id_procedimento=' . $idProcedimento.'&is_concluir_processo'.$isProcessoConcluido) : '';

//Controle de Visualiza��o
$idTipoProcedimento = $objProcedimentoDTO->getNumIdTipoProcedimento();

$isTipoProcessoParametrizado = $objMdUtlAdmPrmGrRN->verificaTipoProcessoParametrizado(array($idTipoProcedimento, $idTipoControle));
$arrCtrlVisualizacao = MdUtlControleDsmpINT::retornaArrVisualizacaoBotao($idStatus, $isPossuiAnalise, $isTipoProcessoParametrizado, $idFila, $idRevisaoAjustePrz);

if (count($arrCtrlVisualizacao) > 0 && $isPermiteAcoes) {
    if($arrCtrlVisualizacao['ASSOCIACAO']) {
        $arrComandos[] = '<button type="button" accesskey="A" id="btnAssoFila" onclick="associarFila()" class="infraButton">
                                    <span class="infraTeclaAtalho">A</span>ssociar � Fila</button>';
    }

    if($arrCtrlVisualizacao['ATRIBUICAO']) {
        if($isUsuarioDuplicado) {
            $arrComandos[] = '<button type="button" accesskey="B" id="btnAtribuicao" onclick="exibirExcessaoDuplicidade()" class="infraButton">
                                    Atri<span class="infraTeclaAtalho">b</span>uir a mim</button>';
        }else {
            $arrComandos[] = '<button type="button" accesskey="B" id="btnAtribuicao" onclick="atribuicaoAutomatica()" class="infraButton">
                                    Atri<span class="infraTeclaAtalho">b</span>uir a mim</button>';
        }
    }

    if($arrCtrlVisualizacao['DISTRIBUICAO']) {
        $arrComandos[] = '<button type="button" accesskey="i" id="btnDistribuicao" onclick="iniciarDistribuicao()" class="infraButton">
                                    D<span class="infraTeclaAtalho">i</span>stribuir</button>';
    }


    if($arrCtrlVisualizacao['TRIAGEM']) {
        $arrComandos[] = '<button type="button" accesskey="T" id="btnIniciarTriagem" onclick="iniciarTriagem()" class="infraButton">
                                    <span class="infraTeclaAtalho">T</span>riagem</button>';
    }

    if($arrCtrlVisualizacao['ANALISE']) {
        $arrComandos[] = '<button type="button" accesskey="N" id="btnAnalise" onclick="iniciarAnalise()" class="infraButton">
                                    A<span class="infraTeclaAtalho">n</span>�lise</button>';
    }

    if($arrCtrlVisualizacao['REVISAO']) {
        $arrComandos[] = '<button type="button" accesskey="R" id="btnRevisao" onclick="iniciarRevisao()" class="infraButton">
                                    <span class="infraTeclaAtalho">R</span>evis�o</button>';
    }

}

switch ($_GET['acao']) {

    //region Listar
    case $acaoPrincipal:
        break;
    //endregion

    case 'md_utl_atribuicao_automatica':
        $objMdUtlControleDsmpRN->atribuirDistribuicaoUsuarioLogado();

            header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'].'&id_procedimento='.$idProcedimento));
        die;
    break;

    //region Erro
    default:
        throw new InfraException("A��o '" . $_GET['acao'] . "' n�o reconhecida.");
    //endregion
}


//Tabela hist�rico
$objMdUtlHistControleDsmpRN = new MdUtlHistControleDsmpRN();

$objMdUtlHistControleDsmpDTO = new MdUtlHistControleDsmpDTO();
$objMdUtlHistControleDsmpDTO->setDblIdProcedimento($idProcedimento);
$objMdUtlHistControleDsmpDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
$objMdUtlHistControleDsmpDTO->retStrNomeUsuario();
$objMdUtlHistControleDsmpDTO->retStrSiglaUsuario();
$objMdUtlHistControleDsmpDTO->setOrdDthAtual(InfraDTO::$TIPO_ORDENACAO_DESC);
$objMdUtlHistControleDsmpDTO->retTodos();

PaginaSEI::getInstance()->prepararOrdenacao($objMdUtlHistControleDsmpDTO, 'Atual', InfraDTO::$TIPO_ORDENACAO_DESC);
PaginaSEI::getInstance()->prepararPaginacao($objMdUtlHistControleDsmpDTO, 200);

$arrObjsMdUtlHistControleDsmpDTO = $objMdUtlHistControleDsmpRN->listar($objMdUtlHistControleDsmpDTO);
$numRegistros = count($arrObjsMdUtlHistControleDsmpDTO);
PaginaSEI::getInstance()->processarPaginacao($objMdUtlHistControleDsmpDTO);


    if ($numRegistros > 0) {

        $sinPrimeiroStatusHist = $arrObjsMdUtlHistControleDsmpDTO[0]->getStrStaAtendimentoDsmp();
        $sinPrimeiroStatusHist = trim($sinPrimeiroStatusHist);

        if(($strStatusAtual == MdUtlControleDsmpRN::$EM_CORRECAO_ANALISE || $strStatusAtual == MdUtlControleDsmpRN::$EM_CORRECAO_TRIAGEM) && $sinPrimeiroStatusHist == MdUtlControleDsmpRN::$EM_REVISAO){
            $strTipoAcaoAtual = 'Revis�o';
        }


        $strResultado .= '<table width="99%" class="infraTable" summary="Detalhamento" id="tbHistDetalhe">';
        $strResultado .= '<caption class="infraCaption">';
        $strResultado .= PaginaSEI::getInstance()->gerarCaptionTabela($strTitulo, $numRegistros);
        $strResultado .= '</caption>';


        $strResultado .= '<th class="infraTh" width="15%" style="text-align: center">    Data/Hora</th>' . "\n";
        $strResultado .= '<th class="infraTh" width="15%" style="text-align: center">    Usu�rio A��o </th>' . "\n";
        $strResultado .= '<th class="infraTh" width="18%" style="text-align: center">    Tipo de A��o </th>' . "\n";
        $strResultado .= '<th class="infraTh" width="25%" style="text-align: center">    Detalhe</th>' . "\n";
        $strResultado .= '<th class="infraTh" width="25%" style="text-align: center">    Status </th>' . "\n";
        $strResultado .= '</tr>' . "\n";

        $strCssTr = '<tr class="infraTrClara">';


        for ($i = 0; $i < $numRegistros; $i++) {


            $strNomeUsu  = $arrObjsMdUtlHistControleDsmpDTO[$i]->getStrNomeUsuario();
            $strStatus   = trim($arrObjsMdUtlHistControleDsmpDTO[$i]->getStrStaAtendimentoDsmp());
            $data        = $arrObjsMdUtlHistControleDsmpDTO[$i]->getDthAtual();
            $strDetalhe  = $arrObjsMdUtlHistControleDsmpDTO[$i]->getStrDetalhe();
            $strSiglaUsu = $arrObjsMdUtlHistControleDsmpDTO[$i]->getStrSiglaUsuario();
            $strTipoAcao = $arrObjsMdUtlHistControleDsmpDTO[$i]->getStrTipoAcao();
            $dataFormatada = MdUtlHistControleDsmpINT::formatarDataHora($data);

            $idAtendimentoAnterior = 0;
            if($i > 0){
                $posicaoAnterior = $i - 1;
                $idAtendimentoAnterior = $arrObjsMdUtlHistControleDsmpDTO[$posicaoAnterior]->getNumIdAtendimento();
                if($idAtendimentoAnterior != $arrObjsMdUtlHistControleDsmpDTO[$i]->getNumIdAtendimento()){
                    $strCssTr = ($strCssTr == '<tr class="infraTrClara">') ? '<tr class="infraTrEscura">' : '<tr class="infraTrClara">';
                }

                //$strCssTr = ($strCssTr == '<tr class="infraTrClara">' && $idAtendimentoAnterior !=  $arrObjsMdUtlHistControleDsmpDTO[$i]->getNumIdAtendimento()) ? '<tr class="infraTrEscura">' : '<tr class="infraTrClara">';
            }



            $strResultado .= $strCssTr;
            //Linha Data/Hora
            $strResultado .= '<td class="tdDataHora" style="text-align: center">';
            $strResultado .= PaginaSEI::tratarHTML($dataFormatada);
            $strResultado .= '</td>';

            //Linha Usu�rio a��o
            $strResultado .= '<td class="tdNomeUsuario" style="text-align: center">';
            $strResultado .=             '<a class="ancoraSigla" href="javascript:void(0);" alt="' . PaginaSEI::tratarHTML($strNomeUsu) . '" title="' . PaginaSEI::tratarHTML($strNomeUsu) . '">' . PaginaSEI::tratarHTML($strSiglaUsu) . '</a>';
            $strResultado .= '</td>';

            //Linha Tipo de a��o
            $strResultado .= '<td class="tdTipoAcao" style="text-align: center">';
            $strResultado .= PaginaSEI::tratarHTML($strTipoAcao);
            $strResultado .= '</td>';

            //Linha Detalhe
            $strResultado .= '<td class="tdDetalhe" style="text-align: center">';
            $strResultado .= $strDetalhe;
            $strResultado .= '</td>';

            //Linha Fila Status
            $strResultado .= '<td class="tdStatusProcesso" style="text-align: center">';
            $strResultado .= !is_null($strStatus) ? PaginaSEI::tratarHTML($arrSituacao[$strStatus]) : PaginaSEI::tratarHTML($arrSituacao[0]);
            $strResultado .= '</td>';


            $strResultado .= '</tr>';
        }
            $strResultado .= '</table>';
}

$arrComandos[] = '<button type="button" accesskey="C" name="btnFechar" value="Fechar" onclick="window.top.location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=procedimento_trabalhar&acao_origem=' . $_GET['acao'] . '&acao_destino=' . $_GET['acao'] . $strParametros . PaginaSEI::montarAncora($arrStrIdProtocolo)) . '\';" 
class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';


PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(':: ' . PaginaSEI::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo . ' ::');
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();
?>
    #tblSituacaoAtual{
        font-size: 0.97em;
    }

    #tblSituacaoAtual .tdCabecalho{
        width: 80px;
    }

    #tblSituacaoAtual .tdEscopo{
    width: 75%;
    }

    #fldSituacaoAtual{
        width: 65%;
    }
<?php
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript(); ?>
<?php
if(0) {
    ?>
    <script type="javascript">
<?php } ?>

var msg25 = '<?php echo MdUtlMensagemINT::getMensagem(MdUtlMensagemINT::$MSG_UTL_25)?>';

function inicializar() {

    var idParam = document.getElementById('hdnIdParametroCtrlUtl').value;
    var isProcessoConcl  = '<?php echo $isProcessoConcluido ?>';
    var msgConclusao = '<?php echo $msg107 ?>';

    if (idParam == 0) {
        alert(msg25);
    }

   if(isProcessoConcl == 1){
       if(confirm(msgConclusao)) {
           document.getElementById('hdnIsConcluirProcesso').value = 1;
           document.getElementById("frmUtlProcessoLista").submit();
       }else{
          window.location.href = '<?=$urlInicial?>';
       }
    }

    if ('<?= $_GET['acao'] ?>' == 'md_utl_processo_listar') {
        infraReceberSelecao();
    } else {
        infraEfeitoTabelas();
    }
}


function associarFila(){
      infraAbrirJanela('<?=$strLinkAssociarFila?>', 'janelaAssinatura', 700, 450, 'location=0,status=1,resizable=1,scrollbars=1');
}

function iniciarTriagem(){
    window.location.href = '<?= $strLinkIniciarTriagem ?>';
}

function iniciarAnalise(){
    window.location.href = '<?= $strLinkIniciarAnalise ?>';
}

function iniciarRevisao(){
    window.location.href = '<?= $strLinkIniciarRevisao ?>';
}

function iniciarDistribuicao(){

    var staFrequencia = '<?=$staFrequencia?>';
    if(staFrequencia == 0){
        alert('A Frequ�ncia de Distribui��o n�o est� parametrizada no Tipo de Controle desta Unidade. Converse com o Gestor da sua �rea!');
        return false;
    }else{
        window.location.href = '<?= $strLinkIniciarDistrb ?>';
    }
}

function atribuicaoAutomatica() {
    if(confirm("Confirma a Distribui��o do Processo em sua carga?")){
        window.location.href = '<?= $strLinkAtribuir ?>';
    }

}

function exibirExcessaoDuplicidade(){
    var msg92 = '<?php echo $msg92 ?>'
    alert(msg92);
}

function fechar() {
    window.location.href = '<?= $strLinkFechar ?>';
}



<?php
if(0) {
?>

</script>
    <?php } ?>

<?php PaginaSEI::getInstance()->fecharJavaScript(); ?>


<?php
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
    <form id="frmUtlProcessoLista" method="post"
          action="<?= PaginaSEI::getInstance()->formatarXHTML(
              SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'])
          ) ?>">

        <?php
         PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
         PaginaSEI::getInstance()->abrirAreaDados('auto');
        ?>
        <div class="bloco" id="divCabecalho">
            <div id="divProcesso" style="font-size: 1.1em;">
            <label id="lblProcesso" for="txtProcesso" class="infraLabelObrigatorio">
                Processo:
            </label>
            <label><?=$strNumeroProcedimento?></label>
            </br>
            </div>
            <?php if(is_null($objControleDsmpDTO)) {
                if($isTipoProcessoParametrizado) {
                    ?>
                    <div style="font-size: 1.1em;">
                        <label id="lblStatus" for="txtStatus" class="infraLabelObrigatorio">
                            Status Atual:
                        </label>
                        <label>
                            <?php echo MdUtlControleDsmpRN::$STR_AGUARDANDO_FILA; ?>
                        </label>
                    </div>
                    <?
                }
            }else{ ?>
                </br>
                <fieldset id="fldSituacaoAtual" class="infraFieldset">
                    <legend class="infraLegend">Situa��o Atual</legend>

                    <!-- Processo -->
                    <table id="tblSituacaoAtual">

                        <tr>
                            <td class="tdCabecalho">
                                <label id="lblProcesso" for="lblProcessoText" class="infraLabelObrigatorio">
                                    Data/Hora:
                                </label>
                            </td>
                            <td class="tdEscopo">
                                <label id="lblProcessoText"><?= MdUtlHistControleDsmpINT::formatarDataHora($dthDataHoraAtual); ?></label>
                            </td>
                        </tr>

                    <!-- Usu�rio A��o -->
                        <tr>
                            <td class="tdCabecalho">
                                <label id="lblUsuarioAtual" for="lblUsuarioAtualText" class="infraLabelObrigatorio">
                                    Usu�rio A��o:
                                </label>
                            </td>
                            <td class="tdEscopo">
                                <label id="lblUsuarioAtualText"> <a class="ancoraSigla" href="javascript:void(0);" alt="<?php echo PaginaSEI::tratarHTML($strNomeUsuarioAtual); ?>" title="<?php echo PaginaSEI::tratarHTML($strNomeUsuarioAtual); ?>"><?php  echo PaginaSEI::tratarHTML($strSiglaUsuarioAtual) ?> </a></label>
                                <br/>
                            </td>
                        </tr>

                    <!-- Tipo de A��o -->
                        <tr>
                            <td class="tdCabecalho">
                                <label id="lblTipoAcao" for="lblTipoAcaoText" class="infraLabelObrigatorio">
                                    Tipo de A��o:
                                </label></td>

                            <td class="tdEscopo">
                                <label id="lblTipoAcaoText"><?= $strTipoAcaoAtual ?></label>
                            </td>
                        </tr>

                    <!-- Detalhe -->
                        <tr>
                            <td class="tdCabecalho">
                                <label id="lblDetalhe" for="lblDetalheText" class="infraLabelObrigatorio">
                                    Detalhe:
                                </label>
                            </td>
                            <td class="tdEscopo">
                                <label id="lblDetalheText"><?= $strDetalheAtual ?></label>
                            </td>
                        </tr>

                    <!-- Status Atual -->
                        <tr>
                            <td class="tdCabecalho">
                                <label id="lblDetalhe" for="lblDetalheText" class="infraLabelObrigatorio">
                                    Status Atual:
                                </label>
                            </td>
                            <td class="tdEscopo">
                                <label id="lblDetalheText"><?= $arrSituacao[$strStatusAtual] ?></label>
                            </td>
                        </tr>

                    </table>

                </fieldset>
           <? } ?>



        </div>


        <?
        PaginaSEI::getInstance()->fecharAreaDados();
        PaginaSEI::getInstance()->montarAreaTabela($strResultado, $numRegistros);
        PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
        ?>

        <input type="hidden" id="hdnIdTipoControleUtl" name="hdnIdTipoControleUtl" value="<?php echo is_null($idTipoControle) ? '0' : $idTipoControle; ?>"/>
        <input type="hidden" id="hdnIdParametroCtrlUtl" name="hdnIdParametroCtrlUtl" value="<?php echo $isParametrizado ? '1' : '0'; ?>"/>
        <input type="hidden" id="hdnProtocoloFormatado" name="hdnProtocoloFormatado" value="<?php echo !is_null($objProcedimentoDTO) ? $objProcedimentoDTO->getStrProtocoloProcedimentoFormatado() : '' ?>"/>
        <input type="hidden" id="hdnNomeFilaAtual" name="hdnNomeFilaAtual" value="<?php echo !is_null($objControleDsmpDTO) ? $objControleDsmpDTO->getStrNomeFila() : '' ?>"/>
        <input type="hidden" id="hdnIdProcedimento" name="hdnIdProcedimento" value="<?php echo $idProcedimento?>"/>
        <input type="hidden" id="hdnIdStatusAtual" name="hdnIdStatusAtual" value="<?php echo !is_null($objControleDsmpDTO) ? $objControleDsmpDTO->getStrStaAtendimentoDsmp() : 0 ?>"/>
        <input type="hidden" id="hdnIsConcluirProcesso" name="hdnIsConcluirProcesso" value="<?php echo $isProcessoAutorizadoConcluir ?>"/>


    </form>

<?php
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();

