<?php
/**
 * Created by PhpStorm.
 * User: thamires.zamai
 * Date: 11/01/2019
 * Time: 10:57
 */

require_once dirname(__FILE__) . '/../../SEI.php';

session_start();
SessaoSEI::getInstance()->validarLink();
$arrIdProcedimentoDistrib = array();
if (isset($_SESSION['IDS_PROCEDIMENTOS_DISTRIBUICAO'])) {
    $arrIdProcedimentoDistrib = $_SESSION['IDS_PROCEDIMENTOS_DISTRIBUICAO'];
    unset($_SESSION['IDS_PROCEDIMENTOS_DISTRIBUICAO']);
}


PaginaSEI::getInstance()->salvarCamposPost(array('txtProcessoUtlDist','txtDocumento', 'selFilaUtlDist', 'selTipoProcessoUtlDist','selResponsavelUtlDist', 'selStatusUtlDist'));

$txtProcessoCampo     = array_key_exists('txtProcessoUtlDist', $_POST) ? $_POST['txtProcessoUtlDist'] : PaginaSEI::getInstance()->recuperarCampo('txtProcessoUtlDist');
$txtDocumentoCampo    = array_key_exists('txtDocumentoUtlDist', $_POST) ? $_POST['txtDocumentoUtlDist'] : PaginaSEI::getInstance()->recuperarCampo('txtDocumentoUtlDist');
$selFilaCampo         = array_key_exists('selFilaUtlDist', $_POST) ? $_POST['selFilaUtlDist'] : PaginaSEI::getInstance()->recuperarCampo('selFilaUtlDist');
$selTipoProcessoCampo = array_key_exists('selTipoProcessoUtlDist', $_POST) ? $_POST['selTipoProcessoUtlDist'] : PaginaSEI::getInstance()->recuperarCampo('selTipoProcessoUtlDist');
$selResponsavelCampo  = array_key_exists('selResponsavelUtlDist', $_POST) ? $_POST['selResponsavelUtlDist'] : PaginaSEI::getInstance()->recuperarCampo('selResponsavelUtlDist');
$selStatusCampo       = array_key_exists('selStatusUtlDist', $_POST) ? $_POST['selStatusUtlDist'] : PaginaSEI::getInstance()->recuperarCampo('selStatusUtlDist');
$selAtividadeCampo    = array_key_exists('selAtividadeUtlDist', $_POST) ? $_POST['selAtividadeUtlDist'] : PaginaSEI::getInstance()->recuperarCampo('selAtividadeUtlDist');

$arrPostDados = array('txtProcesso' => $txtProcessoCampo, 'txtDocumento'=> $txtDocumentoCampo, 'selFila' => $selFilaCampo, 'selTipoProcesso'=> $selTipoProcessoCampo, 'selResponsavel' => $selResponsavelCampo, 'selStatus'=> $selStatusCampo);

//Id tipo de controle
$objFilaRN                 = new MdUtlAdmFilaRN();
$objMdUtlAdmFilaPrmGrUsuRN = new MdUtlAdmFilaPrmGrUsuRN();
$objMdUtlAdmTpCtrlUsuRN    = new MdUtlAdmRelTpCtrlDesempUsuRN();
$objMdUtlControleDsmpRN    = new MdUtlControleDsmpRN();
$objMdUtlAdmTpCtrlUndRN    = new MdUtlAdmRelTpCtrlDesempUndRN();
$objMdUtlRelTriagemAtvRN   = new MdUtlRelTriagemAtvRN();
$objMdUtlAdmUtlTpCtrlRN    = new MdUtlAdmTpCtrlDesempRN();
$objMdUtlAdmTpCtrlDTO      = new MdUtlAdmTpCtrlDesempDTO();

$idTipoControle            = $objMdUtlAdmTpCtrlUndRN->getTipoControleUnidadeLogada();
$arrObjsFilaDTO            = $objFilaRN->getFilasTipoControle($idTipoControle);

$idsFilasPermitidas        = InfraArray::converterArrInfraDTO($arrObjsFilaDTO, 'IdMdUtlAdmFila');
$arrObjsFilaUsuDTO         = $objMdUtlAdmFilaPrmGrUsuRN->getPapeisDeUsuario($idsFilasPermitidas);

$idParametro = null;

$idsFilasResponsavel       = $selFilaCampo != '' ? array($selFilaCampo) : $idsFilasPermitidas;
$arrObjsResponsavelDTO     = $objMdUtlAdmFilaPrmGrUsuRN->getResponsavelPorFila($idsFilasResponsavel);

$arrObjsResponsavelDTO     = !is_null($arrObjsResponsavelDTO) ? InfraArray::distinctArrInfraDTO($arrObjsResponsavelDTO, 'IdUsuario') : null;
$isPermiteAssociacao       = false;
$isPermiteAssociacao       = $objMdUtlControleDsmpRN->validaVisualizacaoUsuarioLogado($idTipoControle);



if (!is_null($idTipoControle)) {
    $objMdUtlAdmUtlTpCtrlRN = new MdUtlAdmTpCtrlDesempRN();
    $isParametrizado = $objMdUtlAdmUtlTpCtrlRN->verificaTipoControlePossuiParametrizacao($idTipoControle);
}

$objMdUtlAdmTpCtrlDTO->setNumIdMdUtlAdmTpCtrlDesemp($idTipoControle);
$objMdUtlAdmTpCtrlDTO->retStrStaFrequencia();
$objMdUtlAdmTpCtrlDTO->setNumMaxRegistrosRetorno(1);
$objDTOTipoControle = $objMdUtlAdmUtlTpCtrlRN->consultar($objMdUtlAdmTpCtrlDTO);

$staFrequencia = !is_null($objDTOTipoControle) ? $objDTOTipoControle->getStrStaFrequencia() : '';


if (!is_null($idTipoControle) && $isParametrizado) {
    $isGestorSipSei = $objMdUtlAdmTpCtrlUsuRN->usuarioLogadoIsGestorSipSei();
    $idsStatusPermitido = $objMdUtlControleDsmpRN->getStatusPermitido($arrObjsFilaUsuDTO, $isGestorSipSei);
//URL Base
    $strUrl = 'controlador.php?acao=md_utl_distrib_usuario_';

//URL das Actions
    $strLinkDistribuir = SessaoSEI::getInstance()->assinarLink($strUrl . 'cadastrar&acao_origem=' . $_GET['acao'] . '&id_tp_controle_desmp=' . $idTipoControle.'&acao_retorno='.$_GET['acao']);
    $strUrlPesquisar = SessaoSEI::getInstance()->assinarLink($strUrl . 'listar&acao_origem=' . $_GET['acao'] . '&id_tipo_controle_utl=' . $idTipoControle);
    $strUrlFechar = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=procedimento_controlar&acao_origem=' . $_GET['acao']);
    $idsFilasPermitidasUsBasico = $isGestorSipSei || count($arrObjsFilaUsuDTO) == 0 ? null : InfraArray::converterArrInfraDTO($arrObjsFilaUsuDTO, 'IdMdUtlAdmFila');

    if ($isGestorSipSei) {
        $selFila = MdUtlAdmFilaINT::montarSelectFilas($selFilaCampo, $arrObjsFilaDTO);
    } else {
        $selFila = count($idsFilasPermitidasUsBasico) > 0 ? $selFila = MdUtlAdmFilaINT::montarSelectFilas($selFilaCampo, $arrObjsFilaDTO, $idsFilasPermitidasUsBasico) : null;
    }

    if($isGestorSipSei){

        $selResponsavel = MdUtlAdmFilaPrmGrUsuINT::montarSelectResponsavel($selResponsavelCampo, $arrObjsResponsavelDTO);
    }else{
        $selResponsavel = '';
    }

    $selStatus = count($idsStatusPermitido) > 0 || $isGestorSipSei ? MdUtlControleDsmpINT::montarSelectStatus($selStatusCampo, false, $idsStatusPermitido) : null;
    $arrObjsTpProcesso = $objMdUtlControleDsmpRN->getTiposProcessoTipoControle($idTipoControle);
    $selTipoProcesso = $isPermiteAssociacao  ? InfraINT::montarSelectArrInfraDTO(null, null, $selTipoProcessoCampo, $arrObjsTpProcesso, 'IdTipoProcedimento', 'NomeProcedimento') : '';


}

$strTitulo = 'Distribui��o';

switch ($_GET['acao']) {

    //region Listar
    case 'md_utl_distrib_usuario_listar':

        break;
    //endregion

    //region Erro
    default:
        throw new InfraException("A��o '" . $_GET['acao'] . "' n�o reconhecida.");
    //endregion
}


//Verifica se � a��o Selecionar
$bolSelecionar = $_GET['acao'] == 'md_utl_adm_fila_selecionar';

$arrComandos[] = '<button type="button" accesskey="P" id="btnPesquisar" onclick="pesquisar()" class="infraButton">
                                        <span class="infraTeclaAtalho">P</span>esquisar</button>';

if (!is_null($idTipoControle) && $isPermiteAssociacao) {
    //Bot�es de a��o do topo
    $arrComandos[] = '<button type="button" accesskey="i" id="btnAssoFila" onclick="distribuir(true, false, false, false)" class="infraButton">
                                        D<span class="infraTeclaAtalho">i</span>stribuir</button>';
}


$arrComandos[] = '<button type="button" accesskey="c" id="btnFechar" onclick="fechar()" class="infraButton">
                                        Fe<span class="infraTeclaAtalho">c</span>har</button>';
$numRegistros = 0;
if (!is_null($idTipoControle) && $isParametrizado) {

    $objDTOCombo = $objMdUtlControleDsmpRN->getObjDTOParametrizadoDistrib(array($arrObjsFilaUsuDTO, $isGestorSipSei, $idTipoControle, array()));

    //Configura��o da Pagina��o
    if((count($arrObjsFilaDTO) == 0 && !$isGestorSipSei) || !$isPermiteAssociacao){
        $objDTO = null;
    }else {
        $objDTO = $objMdUtlControleDsmpRN->getObjDTOParametrizadoDistrib(array($arrObjsFilaUsuDTO, $isGestorSipSei, $idTipoControle, $arrPostDados));
    }

    if(!is_null($objDTO)) {
        $objDTO->retNumIdMdUtlAdmRelControleDsmp();
        $objDTO->retNumIdMdUtlControleDsmp();
        $objDTO->retNumIdUnidade();
        $objDTO->retStrNomeTipoProcedimento();
        $objDTO->retStrStaAtendimentoDsmp();
        $objDTO->retStrSiglaUnidade();
        $objDTO->retStrProtocoloProcedimentoFormatado();
        $objDTO->retStrNomeFila();
        $objDTO->retNumIdFila();
        $objDTO->retNumUnidadeEsforco();
        $objDTO->retStrNomeUsuarioDistribuicao();
        $objDTO->retDthAtual();
        $objDTO->retStrSiglaUsuarioDistribuicao();
        $objDTO->retNumIdMdUtlAnalise();
        $objDTO->retNumIdMdUtlTriagem();

        if ($selAtividadeCampo != '') {
            $idsTriagem = $objMdUtlControleDsmpRN->pesquisarAtividade($objDTO);

            if (count($idsTriagem) > 0) {
                $objDTO->setNumIdMdUtlTriagem($idsTriagem, InfraDTO::$OPER_IN);
            } else {
                $objDTO = null;
            }
        }
    }
    $count = 0;
    //Combo de Atividade
    if(!is_null($objDTOCombo)) {
        $objDTOCombo->retNumIdMdUtlTriagem();
        $arrObjsCombo = $objMdUtlControleDsmpRN->listarProcessos($objDTOCombo);

        $idTriagemCombo = InfraArray::converterArrInfraDTO($arrObjsCombo, 'IdMdUtlTriagem');
        $idTriagemCombo = MdUtlControleDsmpINT::removeNullsTriagem($idTriagemCombo);

        $arrayObjs = [];
        $count = count($idTriagemCombo);
    }

    if ($count > 0) {

        $arrObjsTriagemAtividade = $objMdUtlRelTriagemAtvRN->getObjsTriagemAtividade($idTriagemCombo);
        $selAtividade = MdUtlAdmAtividadeINT::montarSelectAtividadesTriagem($selAtividadeCampo, $arrObjsTriagemAtividade);

        foreach ($arrObjsTriagemAtividade as $obj) {
            if (array_key_exists($obj->getNumIdMdUtlTriagem(), $arrayObjs)) {
                $arrayObjs[$obj->getNumIdMdUtlTriagem()] = array();
            } else {
                $arrayObjs[$obj->getNumIdMdUtlTriagem()] = $obj->getStrNomeAtividade();
            }
        }

    } else {
        $selAtividade = '';
    }

    //Fim da Combo de Atividade

    if (!is_null($objDTO)) {

        PaginaSEI::getInstance()->prepararOrdenacao($objDTO, 'ProtocoloProcedimentoFormatado', InfraDTO::$TIPO_ORDENACAO_ASC);
        PaginaSEI::getInstance()->prepararPaginacao($objDTO, 200);

        $arrObjs = $objMdUtlControleDsmpRN->listarProcessos($objDTO);
        $numRegistros = count($arrObjs);

        PaginaSEI::getInstance()->processarPaginacao($objDTO);

        if ($numRegistros > 0) {

            //Tabela de resultado.
            $displayNoneCheck = $isPermiteAssociacao ? '' : 'style="display:none"';
            $strResultado .= '<table width="99%" class="infraTable" summary="Processos" id="tbCtrlProcesso">';
            $strResultado .= '<caption class="infraCaption">';
            $strResultado .= PaginaSEI::getInstance()->gerarCaptionTabela('Distribui��o', $numRegistros);
            $strResultado .= '</caption>';


            //Cabe�alho da Tabela
            $strResultado .= '<tr>';
            $strResultado .= '<th ' . $displayNoneCheck . ' class="infraTh utlSelecionarTodos" align="center" width="1%" >' . PaginaSEI::getInstance()->getThCheck() . '</th>';
            $strResultado .= '<th class="infraTh" width="18%">' . PaginaSEI::getInstance()->getThOrdenacao($objDTO, 'Processo', 'ProtocoloProcedimentoFormatado', $arrObjs) . '</th>';
            $strResultado .= '<th class="infraTh" width="19%">' . PaginaSEI::getInstance()->getThOrdenacao($objDTO, 'Atividade', 'IdTipoProcedimento', $arrObjs) . '</th>';

            //ADICIONAR ORDENA��O PARA OS OUTROS CAMPOS

            $strResultado .= '<th class="infraTh" width="13%">' . PaginaSEI::getInstance()->getThOrdenacao($objDTO, 'Fila', 'NomeFila', $arrObjs) . '</th>';
            $strResultado .= '<th class="infraTh" width="10%" style="text-align: left">'. PaginaSEI::getInstance()->getThOrdenacao($objDTO, 'Unidade de Esfor�o', 'UnidadeEsforco', $arrObjs) .' </th>';
            $strResultado .= '<th class="infraTh" width="16%" style="text-align: left">'. PaginaSEI::getInstance()->getThOrdenacao($objDTO, 'Respons�vel', 'NomeUsuarioDistribuicao', $arrObjs) .'</th>';
            $strResultado .= '<th class="infraTh" width="15%">' . PaginaSEI::getInstance()->getThOrdenacao($objDTO, 'Status', 'StaAtendimentoDsmp', $arrObjs) . '</th>';
            $strResultado .= '<th class="infraTh" width="16%">' . PaginaSEI::getInstance()->getThOrdenacao($objDTO, 'Data Registro Status', 'Atual', $arrObjs) . '</th>';

            if($isPermiteAssociacao) {
                $strResultado .= '<th class="infraTh" width="16%"> A��es </th>';
            }

            $strResultado .= '<th class="infraTh" style="display: none">�ltima Fila</th>';
            $strResultado .= '</tr>';


            //Linhas
            $strCssTr = '<tr class="infraTrEscura">';

            for ($i = 0; $i < $numRegistros; $i++) {

                $strId             = $arrObjs[$i]->getDblIdProcedimento();
                $strProcesso       = $arrObjs[$i]->getStrProtocoloProcedimentoFormatado();
                $strFila           = $arrObjs[$i]->getStrNomeFila();
                $strTpProcesso     = $arrObjs[$i]->getNumIdTipoProcedimento();
                $nomeTpProcesso    = $arrObjs[$i]->getStrNomeTipoProcedimento();
                $uniEsforco        = $arrObjs[$i]->getNumUnidadeEsforco();
                $strStatus         = trim($arrObjs[$i]->getStrStaAtendimentoDsmp());
                $numIdControleDsmp = $arrObjs[$i]->getNumIdMdUtlControleDsmp();
                $numIdTriagem      = $arrObjs[$i]->getNumIdMdUtlTriagem();
                $strNomeAtividade  = array_key_exists($numIdTriagem, $arrayObjs) ? $arrayObjs[$numIdTriagem] : '';
                $linkAtvTriagem    = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_utl_atividade_triagem_listar&acao_origem=md_utl_distrib_usuario_listar&id_triagem=' . $numIdTriagem . '');

                if(is_array($strNomeAtividade)){
                    $strNomeAtividade = '<a href="#" onclick="infraAbrirJanela(\'' . $linkAtvTriagem . '\',\'urlAtividadeTriagemMult\',650,500,)" alt="M�ltiplas" title="M�ltiplas" class="ancoraPadraoAzul"> M�ltiplas </a>';;
                }
               

                $arrSituacao       = MdUtlControleDsmpINT::retornaArrSituacoesControleDsmp();
                $linkProcedimento  = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=procedimento_trabalhar&acao_origem=md_utl_distrib_usuario_listar&id_procedimento=' . $strId . '');
                $data              = explode(' ', $arrObjs[$i]->getDthAtual());
                $dataFormatada     = $data[0];
                $bolRegistroAtivo  = true;


                $strCssTr = !$bolRegistroAtivo ? '<tr class="trVermelha ">' : ($strCssTr == '<tr class="infraTrClara ">' ? '<tr class="infraTrEscura ">' : '<tr class="infraTrClara ">');
                $strCssTr = in_array($strId, $arrIdProcedimentoDistrib) ? '<tr class="infraTrAcessada">' : $strCssTr;
                $strResultado .= $strCssTr;

                //Linha Checkbox
                $strResultado .= '<td ' . $displayNoneCheck . ' align="center" valign="top"  >';
                $strResultado .= PaginaSEI::getInstance()->getTrCheck($i, $strId, $strProcesso);
                $strResultado .= '</td>';

                //Linha Nome
                $strResultado .= '<td class="tdIdProcesso" style="display: none">';
                $strResultado .= $strId;
                $strResultado .= '</td>';

                //Linha Nome
                $strResultado .= '<td class="tdNomeProcesso">';
                $strResultado .= '<a href="#" onclick="window.open(\'' . $linkProcedimento . '\')" alt="' . $nomeTpProcesso . '" title="' . $nomeTpProcesso . '" class="ancoraPadraoAzul">' . $strProcesso . '</a>';
                $strResultado .= '</td>';

                //Linha Atividade
                $strResultado .= '<td class="tdNomeAtividade">';
                $strResultado .= $strNomeAtividade;
                $strResultado .= '</td>';

                //Linha Fila Padr�o
                $strResultado .= '<td class="tdFilaProcesso">';
                $strResultado .= PaginaSEI::tratarHTML($strFila);
                $strResultado .= '</td>';

                //Linha Unidade de Esfor�o
                $strResultado .= '<td class="tdUniEsforco">';
                $strResultado .= PaginaSEI::tratarHTML($uniEsforco);
                $strResultado .= '</td>';

                //Linha Respons�vel
                $strResultado .= '<td class="tdResponsavel">';
                $strResultado .= '<a class="ancoraSigla" href="#" alt="' . PaginaSEI::tratarHTML($arrObjs[$i]->getStrNomeUsuarioDistribuicao()) . '" title="' . PaginaSEI::tratarHTML($arrObjs[$i]->getStrNomeUsuarioDistribuicao()) . '">' . PaginaSEI::tratarHTML($arrObjs[$i]->getStrSiglaUsuarioDistribuicao()) . '</a>';
                $strResultado .= '</td>';

                //Linha Fila Status
                $strResultado .= '<td class="tdStatusProcesso">';
                $strResultado .= !is_null($strStatus) ? PaginaSEI::tratarHTML($arrSituacao[$strStatus]) : PaginaSEI::tratarHTML($arrSituacao[0]);
                $strResultado .= '</td>';

                //Linha Data Registro Status
                $strResultado .= '<td class="tdDtRegistroStatus">';
                $strResultado .= PaginaSEI::tratarHTML($dataFormatada);
                $strResultado .= '</td>';

                //Linha A��es
                if($isPermiteAssociacao) {
                    $strResultado .= '<td class="tdAcoes">';
                    $btnDistribuir = '<img src="modulos/utilidades/imagens/distribuir1.png" id="btnDistribuicao" style="margin-left: 30%" onclick="distribuir(false ,\'' . $numIdControleDsmp . '\' ,\'' . $strStatus . '\' ,\'' . $arrObjs[$i]->getNumIdFila() . '\');" title="Distribuir" alt="Distribuir" class="infraImg" />';
                    $strResultado .= $btnDistribuir;
                    $strResultado .= '</td>';
                }

                //Linha Controle Dsmp
                $strResultado .= '<td class="tdIdControleDsmp" style="display: none">';
                $strResultado .= $numIdControleDsmp;
                $strResultado .= '</td>';

                $strResultado .= '</tr>';

            }
            $strResultado .= '</table>';
        }
    }
}


PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(':: ' . PaginaSEI::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo . ' ::');

//Include de estilos CSS
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();
if (0) { ?>
    <style><? }
    ?>
        .bloco {
            position: relative;
            float: left;
            margin-top: 1%;
            width: 90%;
        }

        .clear {
            clear: both;
        }

        .padraoSelect {
            margin-top: 1px;
            height: 21px;
            width: 78%;
        }

        .padraoInput {
            width: 78%;
        }


        textarea {
            resize: none;
            width: 60%;
        }

        select[multiple] {
            width: 61%;
            margin-top: 0.5%;
        }

        img[id^="imgExcluir"] {
            margin-left: -2px;
        }

        div[id^="divOpcoes"] {
            position: absolute;
            width: 1%;
            left: 62%;
            top: 44%;
        }

        img[id^="imgAjuda"] {
            margin-bottom: -4px;
        }

        #divProcesso {
            position: absolute;
            margin-top: 10px;
            width: 17.1%;
        }

        #divDocumento {
            position: absolute;
            margin-left: 14.8%;
            margin-top: 10px;
            width: 14.5%;
        }

        #divFila {
            position: absolute;
            margin-left: 27.5%;
            margin-top: 8px;
            width: 20.5%;
        }

        #divTipoProcesso {
            position: absolute;
            margin-left: 44.5%;
            margin-top: 8px;
            width: 22%;
        }

        #divResponsavel {
            position: absolute;
            margin-left: 62.5%;
            margin-top: 8px;
            width: 23%;
        }

        #divStatus {
            position: absolute;
            margin-left: 81.4%;
            margin-top: 8px;
            width: 18%;
        }

        #divAtividade{
            position: absolute;
            margin-top: 54px;
        }

        #divTotalUnidade{
            margin-top: 12%;
        }

        #spnTotalUnidade{
            font-size: 1.2em;
        }

        <?
        if (0) { ?></style><?
} ?>

<?php PaginaSEI::getInstance()->fecharStyle();

PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
if (0){ ?>
    <script type="text/javascript"><?}?>
        var msg57 = '<?php echo MdUtlMensagemINT::getMensagem(MdUtlMensagemINT::$MSG_UTL_57); ?>';
        var msg58 = '<?php echo MdUtlMensagemINT::getMensagem(MdUtlMensagemINT::$MSG_UTL_58); ?>';
        var msg59 = '<?php echo MdUtlMensagemINT::getMensagem(MdUtlMensagemINT::$MSG_UTL_59); ?>';
        var msg24 = '<?php echo MdUtlMensagemINT::getMensagem(MdUtlMensagemINT::$MSG_UTL_24); ?>';
        var msg25 = '<?php echo MdUtlMensagemINT::getMensagem(MdUtlMensagemINT::$MSG_UTL_25); ?>';
        var totalUnidade = 0;
        var count = 0;

        function inicializar() {

            var urlCtrlProcessos = document.getElementById('hdnUrlControleProcessos').value;
            var idParam = document.getElementById('hdnIdParametroCtrlUtl').value;
            var tpCtrl = document.getElementById('hdnIdTipoControleUtl').value;

            if (tpCtrl == 0) {
                alert(msg24);
                window.location.href = urlCtrlProcessos;
                return false;
            }

            if (idParam == 0) {
                alert(msg25);
                window.location.href = urlCtrlProcessos;
                return false;
            }

            configuraCkeckbox();

            if ('<?= $_GET['acao'] ?>' == 'md_utl_distrib_usuario_selecionar') {
                infraReceberSelecao();
                document.getElementById('btnFecharSelecao').focus();
            } else {
                infraEfeitoTabelas();
            }

            addEnter();   
        }
        

        function addEnter() {
            document.getElementById('txtProcessoUtlDist').addEventListener("keypress", function (evt) {
                addPesquisarEnter(evt);
            });

            document.getElementById('txtDocumentoUtlDist').addEventListener("keypress", function (evt) {
                addPesquisarEnter(evt);
            });
        }

        function addPesquisarEnter(evt) {
            var key_code = evt.keyCode ? evt.keyCode :
                evt.charCode ? evt.charCode :
                    evt.which ? evt.which : void 0;

            if (key_code == 13) {
                pesquisar();
            }

        }

        function distribuir(multiplo, idSelecionado, idStatus, idFila){
            var numeroRegistroTela = '<?= $numRegistros ?>';
            var isValido = true;
            var staFrequencia = '<?=$staFrequencia?>';

            if(numeroRegistroTela == 0){
                alert(msg59);
                return false;
            }
            
            if(staFrequencia == 0){
                alert('A Frequ�ncia de Distribui��o n�o est� parametrizada no Tipo de Controle desta Unidade. Converse com o Gestor da sua �rea!');
                return false;
            }

            if(multiplo){
                isValido = realizarValidacoesFiltro();
            }

            if(isValido) {
                preencherHiddenDistribuicao(multiplo, idSelecionado);
                enviarStatusFila(multiplo, idStatus, idFila);
                document.getElementById('frmTpControleLista').action = '<?=$strLinkDistribuir?>';
                document.getElementById('frmTpControleLista').submit();
            }



        }

        function enviarStatusFila(multiplo, idStatus, idFila){
            var idStatusEnviar = multiplo  ? document.getElementById('selStatusUtlDist').value : idStatus;
            var idFilaEnviar   = multiplo  ?  document.getElementById('selFilaUtlDist').value : idFila;
            document.getElementById('hdnSelStatus').value = idStatusEnviar;
            document.getElementById('hdnSelFila').value = idFilaEnviar;
        }

        function realizarValidacoesFiltro(){

            var numSelecionados = infraNroItensSelecionados();

            var selFila = document.getElementById('selFilaUtlDist').value;
            var selStatus = document.getElementById('selStatusUtlDist').value;

            if(selFila == 0 ){
                alert(msg57);
                return false;
            }

            if(selStatus == 0) {
                alert(msg58);
                return false;
            }

            if (numSelecionados == 0) {
                alert(msg59);
                return false;
            }

            return true;
        }


        function pesquisar() {
            document.getElementById('frmTpControleLista').action = '<?= $strUrlPesquisar ?>';
            document.getElementById('frmTpControleLista').submit();
        }

        function fechar() {
            location.href = "<?= $strUrlFechar ?>";
        }

        function preencherHiddenDistribuicao(multiplo, idSelecionado){
            var json = '';
            var linhas = new Array();

            if(multiplo) {
                var objs = document.getElementsByClassName('infraTrMarcada');

                for (var i = 0; i < objs.length; i++) {
                    var idControleDsmp = $(objs[i]).find('.tdIdControleDsmp').text();
                    linhas.push(idControleDsmp);
                }

            }else{
                linhas.push(idSelecionado);
            }

            if(linhas.length > 0) {
                json = JSON.stringify(linhas);
                document.getElementById('hdnDistribuicao').value = json;
            }
        }

        function configuraCkeckbox(){
                //sele��o unica
                var atributos = document.getElementsByClassName('infraCheckbox');

                for(i = 0; i < atributos.length; i++) {
                    atributos[i].removeAttribute('onclick');
                }

                for (i = 0; i < atributos.length; i++) {
                    atributos[i].addEventListener('click', function (e) {
                        infraSelecionarItens(e.target, 'Infra');
                        getUnidadeEsforco(e.target);
                    });
                }

                //sele��o multipla
                var atributoMult = document.getElementsByClassName('utlSelecionarTodos')[0].children[1];

                atributoMult.removeAttribute('onclick');
                atributoMult.addEventListener('click', function (e) {
                    infraSelecaoMultipla('Infra');
                    getUnidadeEsforcoMultiplo(e);
                });

            setTimeout(controlaChecksInicializacao, '10')
            }

        function controlaChecksInicializacao(){

            var objsMarcados = document.getElementsByClassName('infraTrMarcada');
            var total        = 0;

            if(objsMarcados.length > 0){

                for (var i = 0; i < objsMarcados.length; i++) {
                    var undEsfor = parseInt(objsMarcados[i].children[5].innerText);
                    total += undEsfor;
                }
                document.getElementById('spnTotalUnidade').innerHTML =  total;
            }

        }


        function getUnidadeEsforco(obj) {

            var trPrincipal = obj.parentElement.parentElement;
            var valorUniEsforco = parseInt(trPrincipal.children[5].innerText);
            var totalUnidade = parseInt(document.getElementById('spnTotalUnidade').innerHTML);



            if(obj.checked == true){
                totalUnidade += valorUniEsforco;
            }else{

                console.log(totalUnidade);
                totalUnidade -= valorUniEsforco;
            }

            document.getElementById('spnTotalUnidade').innerHTML =  totalUnidade;

        }

        function getUnidadeEsforcoMultiplo(ev){

            var objs = document.getElementsByClassName('infraTrMarcada');


            if($.trim(ev.target.title) != 'Selecionar Tudo'){
                var somaUniEsforco = 0;
                for (var i = 0; i < objs.length; i++) {
                    somaUniEsforco += parseInt(objs[i].children[5].innerText);
                }

            }else{
                    somaUniEsforco = 0;
            }

            document.getElementById('spnTotalUnidade').innerHTML =  somaUniEsforco;
        }
        

        <?php if (0){ ?>
    </script><? } ?>

<?php PaginaSEI::getInstance()->fecharJavaScript(); ?>


<?php
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
    <form id="frmTpControleLista" method="post"
          action="<?= PaginaSEI::getInstance()->formatarXHTML(
              SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'])
          ) ?>">

        <?php
        PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
        PaginaSEI::getInstance()->abrirAreaDados('auto');
        ?>
        <div class="bloco" id="divProcesso">
            <label id="lblProcesso" for="txtProcessoUtlDist" class="infraLabelOpcional">
                Processo:
            </label>

            <div class="clear"></div>

            <input type="text" id="txtProcessoUtlDist" name="txtProcessoUtlDist" class="inputFila infraText padraoInput"
                   size="30"
                   value="<?php echo $txtProcessoCampo ?>"
                   maxlength="100" tabindex="502"/>
        </div>

        <div class="bloco" id="divDocumento">
            <label id="lblDocumento" for="txtDocumentoUtlDist" accesskey="S" class="infraLabelOpcional">
                Documento SEI:
            </label>

            <div class="clear"></div>

            <input type="text" id="txtDocumentoUtlDist" name="txtDocumentoUtlDist" class="inputFila infraText padraoInput"
                   size="30"
                   value="<?php echo $txtDocumentoCampo ?>"
                   maxlength="100" tabindex="502"/>
        </div>

        <div id="divFila">
            <label id="lblFila" for="selFilaUtlDist" accesskey="" class="infraLabelOpcional">Fila:</label>
            <select id="selFilaUtlDist" name="selFilaUtlDist" class="infraSelect padraoSelect"
                    onchange="pesquisar();"
                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                <?= $selFila ?>
            </select>
        </div>


        <div id="divTipoProcesso">
            <label id="lblTipoProcesso" for="selTipoProcessoUtlDist" accesskey="" class="infraLabelOpcional">Tipo de
                Processo:</label>
            <select id="selTipoProcessoUtlDist" name="selTipoProcessoUtlDist" class="infraSelect padraoSelect"
                    onchange="pesquisar();"
                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                <option value=""></option>
                <?= $selTipoProcesso ?>
            </select>
        </div>

        <div id="divResponsavel">
            <label id="lblResponsavel" for="selResponsavelUtlDist" accesskey="" class="infraLabelOpcional">Respons�vel:</label>
            <select <?php echo !$isGestorSipSei ? 'disabled="disabled' : ''; ?> id="selResponsavelUtlDist" name="selResponsavelUtlDist" class="infraSelect padraoSelect"
                    onchange="pesquisar();"
                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                <?= $selResponsavel ?>
            </select>
        </div>

        <div id="divStatus">
            <label id="lblStatus" for="selStatusUtlDist" accesskey="" class="infraLabelOpcional">Status:</label>
            <select id="selStatusUtlDist" name="selStatusUtlDist" class="infraSelect padraoSelect"
                    onchange="pesquisar();"
                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                <?= $selStatus ?>
            </select>
        </div>

        <div id="divAtividade">
            <label id="lblAtividade" for="selAtividadeUtlDist" accesskey="" class="infraLabelOpcional">Atividade:</label>
            <select id="selAtividadeUtlDist" style="width: 138px;" name="selAtividadeUtlDist" class="infraSelect padraoSelect"
                    onchange="pesquisar();"
                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                <option value=""></option>
                <?=$selAtividade?>
            </select>
        </div>

        <div id="divTotalUnidade">
            <label id="lblTotalUnidade" class="infraLabelOpcional">Total de Unidade de Esfor�o Selecionadas:</label>
            <span id="spnTotalUnidade">0</span>
        </div>

        <input type="hidden" id="hdnSelStatus" name="hdnSelStatus" value=""/>
        <input type="hidden" id="hdnSubmit" name="hdnSubmit" value="<?php echo $vlControlePost; ?>"/>
        <input type="hidden" id="hdnSelFila" name="hdnSelFila" value=""/>
        <input type="hidden" id="hdnIdTipoControleUtl" name="hdnIdTipoControleUtl"
               value="<?php echo is_null($idTipoControle) ? '0' : $idTipoControle; ?>"/>
        <input type="hidden" id="hdnIdParametroCtrlUtl" name="hdnIdParametroCtrlUtl"
               value="<?php echo $isParametrizado ? '1' : '0'; ?>"/>
        <input type="hidden" id="hdnDadosAssociarFila" name="hdnDadosAssociarFila"/>
        <input type="hidden" id="hdnDistribuicao" name="hdnDistribuicao"/>
        <input type="hidden" id="hdnUrlControleProcessos" name="hdnUrlControleProcessos"
               value="<?php echo SessaoSEI::getInstance()->assinarLink('controlador.php?acao=procedimento_controlar&acao_origem=' . $_GET['acao']); ?>"/>
        <?php
        PaginaSEI::getInstance()->fecharAreaDados();
        PaginaSEI::getInstance()->montarAreaTabela($strResultado, $numRegistros);
        PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
        ?>

    </form>

<?php
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();