<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4� REGI�O
 *
 * 10/07/2018 - criado por jhon.cast
 *
 * Vers�o do Gerador de C�digo: 1.41.0
 */

try {
    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();

    //////////////////////////////////////////////////////////////////////////////
    //InfraDebug::getInstance()->setBolLigado(false);
    //InfraDebug::getInstance()->setBolDebugInfra(true);
    //InfraDebug::getInstance()->limpar();
    //////////////////////////////////////////////////////////////////////////////

    SessaoSEI::getInstance()->validarLink();

    require_once 'md_utl_adm_prm_gr_cadastro_acoes.php';

} catch (Exception $e) {
    PaginaSEI::getInstance()->processarExcecao($e);
}

$strLinkUsuarioSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=usuario_selecionar&tipo_selecao=2&id_object=objLupaUsuario');
$strLinkAjaxUsuario = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_utl_usuario_auto_completar');
$strLinkAjaxVincUsuFila = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_utl_adm_prm_vinculo_usuario_fila');
$strUrlBuscarNomesUsuario = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_utl_adm_prm_buscar_nome_usuario');

//Tipo Processo
$strLinkTipoProcessoSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=tipo_procedimento_selecionar&tipo_selecao=2&id_object=objLupaTpProcesso');
$strLinkAjaxTipoProcesso = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_utl_adm_tipo_processo_auto_completar');

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(PaginaSEI::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo);
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();

require_once 'md_utl_adm_prm_gr_cadastro_css.php';
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();

require_once 'md_utl_geral_js.php';
require_once 'md_utl_adm_prm_gr_cadastro_js.php';
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
    <form id="frmMdUtlAdmPrmGrCadastro" method="post" action="<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao']) ?>">
        <?
        PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
        //PaginaSEI::getInstance()->montarAreaValidacao();
        PaginaSEI::getInstance()->abrirAreaDados('36em; overflow:unset;');
        ?>
        <label id="lblCargaPadrao" for="txtCargaPadrao" accesskey="" class="infraLabelObrigatorio" >Carga Padr�o de
            Unidade de Esfor�o:</label>
        <a  id="btnCargaPadrao" <?= PaginaSEI::montarTitleTooltip('Informar a unidade padr�o de esfor�o di�ria.') ?>
           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
            <img id="imgAjudaCargaPadrao" border="0" style="width: 16px;height: 16px;"
                 src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" class="infraImg"/>
        </a>
        <input type="text" id="txtCargaPadrao" name="txtCargaPadrao" onkeypress="return infraMascaraNumero(this, event,6)"
               class="infraText" value="<?= PaginaSEI::tratarHTML($cargaPadrao); ?>"
               tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>

        <label id="lblStaFrequencia" for="selStaFrequencia" accesskey="" class="infraLabelObrigatorio">Frequ�ncia de
            distribui��o:</label>
        <a style="" id="btnlStaFrequencia" <?= PaginaSEI::montarTitleTooltip('Informa a frequ�ncia da distribui��o das tarefas.') ?>
           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
            <img id="imgAjudalStaFrequencia" border="0" style="width: 16px;height: 16px; margin-left: 20px!important;"
                 src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" class="infraImg"/>
        </a>
        <select id="selStaFrequencia" name="selStaFrequencia" class="infraSelect" onchange="montarPeriodo()"
                tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
            <?= $strItensSelStaFrequencia ?>
        </select>
        <!---->

        <label id="lblInicioPeriodo" for="selInicioPeriodo" accesskey="" class="infraLabelObrigatorio">In�cio do Per�odo:</label>
        <a style="" id="btnInicioPeriodo" <?= PaginaSEI::montarTitleTooltip('Informa a frequ�ncia da distribui��o das tarefas.') ?>
           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
            <img id="imgAjudaInicioPeriodo" border="0" style="width: 16px;height: 16px;"
                 src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" class="infraImg"/>
        </a>
        <select id="selInicioPeriodo" name="selInicioPeriodo" class="infraSelect" onchange="montarFimPeriodo()"
                tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
            <?= $strItensSelInicioPeriodo ?>
        </select>

        <label id="lblFimPeriodo" for="selFimPeriodo" accesskey="" class="infraLabelOpcional">Fim do Per�odo:</label>
        <a style="" id="btnFimPeriodo" <?= PaginaSEI::montarTitleTooltip('Informa a frequ�ncia da distribui��o das tarefas.') ?>
           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
            <img id="imgAjudaFimPeriodo" border="0" style="width: 16px;height: 16px;"
                 src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" class="infraImg"/>
        </a>
        <select id="selFimPeriodo" name="selFimPeriodo" class="infraSelect" disabled
                tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
            <?= $strItensSelFimPeriodo ?>
        </select>

        <!---->
        <label id="lblPercentualTeletrabalho" for="txtPercentualTeletrabalho" accesskey="" class="infraLabelOpcional">Percentual
            de Desempenho a Maior para Teletrabalho:</label>
        <a style="" id="btnPercentualTeletrabalho" <?= PaginaSEI::montarTitleTooltip('Informa o percentual de desempenho. Esse valor ser� acrescido para a distribui��o das tarefas de servidor em teletrabalho.') ?>
           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
            <img id="imgAjudaPercentualTeletrabalho" border="0" style="width: 16px;height: 16px;"
                 src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" class="infraImg"/>
        </a>
        <input type="text" id="txtPercentualTeletrabalho" name="txtPercentualTeletrabalho"
               onkeypress="return infraMascaraNumero(this, event,3)" class="infraText"
               onkeyup="return validarPercentual(this,'Percentual de Desempenho')"
               value="<?= PaginaSEI::tratarHTML($percentualTeletrabalho); ?>"
               tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>

        <label id="lblFilaPadrao" for="selFilaPadrao" accesskey="" class="infraLabelOpcional">Fila padr�o:</label>
        <a style="" id="btnFilaPadrao" <?= PaginaSEI::montarTitleTooltip('Informa a fila padr�o em que os processos ser�o inclu�dos assim que chegarem na �rea.') ?>
           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
            <img id="imgAjudaFilaPadrao" border="0" style="width: 16px;height: 16px;"
                 src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" class="infraImg"/>
        </a>
        <select id="selFilaPadrao" name="selFilaPadrao" class="infraSelect"
                tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
            <?= $strFilaPadrao ?>
        </select>

        <label id="lblRetorno" for="selRetorno" accesskey="" class="infraLabelOpcional">Retorno para �ltima Fila:</label>
        <a style="" id="btnRetorno" <?= PaginaSEI::montarTitleTooltip('Quando um processo retorna a uma �rea, o processo vai para a �ltima fila que o tratou nesta �rea.') ?>
           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
            <img id="imgAjudaRetorno" border="0" style="width: 16px;height: 16px;"
                 src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" class="infraImg"/>
        </a>
        <select id="selRetorno" name="selRetorno" class="infraSelect"
                tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
            <?= $strItensSelSinRetono ?>
        </select>


        <div id="divTpProcesso" class="bloco">

            <label id="lblTpProcesso" for="selTpProcesso" accesskey="" class="infraLabelObrigatorio">Tipos de Processos:</label>
            <a  id="btnTpProcesso" <?= PaginaSEI::montarTitleTooltip('Selecionar um ou m�ltiplos tipos de processos que ser�o tratados no tipo de controle. Se o tipo de processo estiver desabilitado, significa que ele esta em uso em outro tipo de controle com mesmo conjunto de unidades.') ?>
               tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                <img id="imgAjudaTpProcesso" border="0" style="width: 16px;height: 16px;margin-bottom: -3px;"
                     src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" class="infraImg"/>
            </a>
            <div class="clear"></div>
            <input type="text" id="txtTpProcesso" name="txtTpProcesso" class="infraText" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />

            <select id="selTpProcesso" name="selTpProcesso" size="4" multiple="multiple" class="infraSelect">
                <?=$strItensSelTpProcesso?>
            </select>
            <div id="divOpcoesTpProcesso">
                <img id="imgLupaTpProcesso" onclick="objLupaTpProcesso.selecionar(700,500);" src="/infra_css/imagens/lupa.gif" alt="Selecionar Unidade" title="Selecionar Unidade" class="infraImg" />
                <br>
                <img id="imgExcluirTpProcesso" onclick="objLupaTpProcesso.remover();" src="/infra_css/imagens/remover.gif" alt="Remover Unidade Selecionada" title="Remover Unidade Selecionada" class="infraImg" />
            </div>
            <input type="hidden" id="hdnIdTpProcesso" name="hdnIdTpProcesso" value="" />

        </div>
            <!-- SPRINT 11 -->
            <div id="blocoRespTacita" >
                <fieldset class="infraFieldset" style="padding-bottom: 3%; margin-top: 25px;" >
                    <legend class="infraLegend" >Resposta T�cita para Solicita��o de Ajuste de Prazo</legend>
                    </br>

                    <!-- Resposta t�cita para dila��o de prazo-->
                    <div>
                        <label id="lblDilacao" for="selDilacao" accesskey="" class="infraLabelObrigatorio">Resposta T�cita para Dila��o de Prazo: </label>
                        <a id="hintDilacao" <?= PaginaSEI::montarTitleTooltip('Informa o Tipo de Resposta T�cita para Solicita��o de Dila��o de Prazo.') ?>
                           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                            <img id="imgDilacao" border="0" style="width: 16px;height: 16px;margin-left: 2px;"
                                 src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" class="infraImg"/>
                        </a>
                        <select id="selDilacao" name="selDilacao" class="infraSelect"
                                tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                            <?=$strItensSelRespDilacao?>
                        </select>
                    </div>
                    <!-- Resposta t�cita para suspens�o de prazo-->
                    <div>
                        <label id="lblSuspensao" for="selSuspensao" accesskey="" class="infraLabelObrigatorio">Resposta T�cita para Suspens�o de Prazo: </label>
                        <a id="hintSuspensao" <?= PaginaSEI::montarTitleTooltip('Informa o Tipo de Resposta T�cita para Solicita��o de Suspens�o de Prazo.') ?>
                           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                            <img id="imgSuspensao" border="0" style="width: 16px;height: 16px;margin-left: 2px;"
                                 src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" class="infraImg"/>
                        </a>
                        <select id="selSuspensao" name="selSuspensao" class="infraSelect"
                                tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                            <?=$strItensSelRespSuspensao?>
                        </select>
                    </div>
                    <!-- Prazo m�ximo de suspens�o de prazo-->
                    <div>
                        <label id="lblPrzSuspensao" for="przSuspensao" accesskey="" class="infraLabelObrigatorio">Prazo m�ximo de Suspens�o: </label>
                        <a id="hintPrzSuspensao" <?= PaginaSEI::montarTitleTooltip('Informa o Prazo M�ximo em dias �teis para Suspens�o de Prazo.') ?>
                           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                            <img id="imgPrzSuspensao" border="0" style="width: 16px;height: 16px;"
                                 src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" class="infraImg"/>
                        </a>
                        <input type="text" id="przSuspensao" name="przSuspensao" utlsomentenumeropaste="true"
                               maxlength="3" onkeypress="return infraMascaraNumero(this, event, 3)" onchange="validarValorDosPrazos(this)"
                               class="infraText" value="<?= PaginaSEI::tratarHTML($numPrzSuspensao); ?>" />
                    </div>
                    <!-- Resposta t�cita para interrup��o de prazo-->
                    <div>
                        <label id="lblInterrupcao" for="selInterrupcao" accesskey="" class="infraLabelObrigatorio">Resposta T�cita para Interrup��o de Prazo: </label>
                        <a id="hintInterrupcao" <?= PaginaSEI::montarTitleTooltip('Informa o Tipo de Resposta T�cita para Solicita��o de Interrup��o de Prazo.') ?>
                           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                            <img id="imgInterrupcao" border="0" style="width: 16px;height: 16px;margin-left: 2px;"
                                 src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" class="infraImg"/>
                        </a>
                        <select id="selInterrupcao" name="selInterrupcao" class="infraSelect"
                                tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                            <?=$strItensSelRespInterrupcao?>
                        </select>
                    </div>
                    <!-- Prazo m�ximo de interrup��o de prazo-->
                    <div>
                        <label id="lblPrzInterrupcao" for="przInterrupcao" accesskey="" class="infraLabelObrigatorio">Prazo m�ximo de Interrup��o: </label>
                        <a id="hintPrzInterrupcao" <?= PaginaSEI::montarTitleTooltip('Informa o Prazo M�ximo em dias �teis para Interrup��o de Prazo.') ?>
                           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                            <img id="imgPrzInterrupcao" border="0" style="width: 16px;height: 16px;"
                                 src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" class="infraImg"/>
                        </a>
                        <input type="text" id="przInterrupcao" name="przInterrupcao" utlsomentenumeropaste="true"
                               maxlength="3" onkeypress="return infraMascaraNumero(this, event, 3)" onchange="validarValorDosPrazos(this)"
                               class="infraText" value="<?= PaginaSEI::tratarHTML($numPrzInterrupcao); ?>" />
                    </div>
                </fieldset>
            </div>

            <!-- FIM SPRINT 11 -->
        <?
        PaginaSEI::getInstance()->fecharAreaDados();
        PaginaSEI::getInstance()->abrirAreaDados($heigthAreaDados.'em; overflow:unset');
        ?>
        <div id="blocoUsuario"  >
        <fieldset class="infraFieldset" style="padding-bottom: 4%" >
            <legend class="infraLegend" >Controle de participantes</legend>
            </br>
        <!--  Usuario Participante -->

            <div id="divUsuario" >
                <label id="lblUsuario" for="selUsuario" accesskey="" class="infraLabelObrigatorio">Usu�rios Participantes:</label>
                <a  id="btnUsuario" <?= PaginaSEI::montarTitleTooltip('Selecionar os participantes que atuar�o no tipo de controle cadastrado.') ?>
                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                    <img id="imgAjudaUsuario" border="0" style="width: 16px;height: 16px;margin-bottom: -2px;"
                         src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" class="infraImg"/>
                </a>
                <div class="clear"></div>
                <input style="width:45%" type="text" id="txtUsuario" name="txtUsuario" class="infraText"  tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />

                <select id="selUsuario" name="selUsuario" size="4" multiple="multiple" class="infraSelect">
                    <?=$strItensSelUsuario?>
                </select>
                <div id="divOpcoesUsuario">
                    <img id="imgLupaUsuario" onclick="objLupaUsuario.selecionar(700,500);" src="/infra_css/imagens/lupa.gif" alt="Selecionar Usuario" title="Selecionar Unidade" class="infraImg" />
                    <br>
                    <img id="imgExcluirUsuario" onclick="objLupaUsuario.remover();" src="/infra_css/imagens/remover.gif" alt="Remover Usuario Selecionado" title="Remover Unidade Selecionada" class="infraImg" />
                </div>
                <input type="hidden" id="hdnIdUsuario" name="hdnIdUsuario" value="" />
            </div>
            <label id="lblTpPresenca" for="selTpPresenca" accesskey="" class="infraLabelObrigatorio">Tipo de Presen�a:</label>
            <a  id="btnTpPresenca" <?= PaginaSEI::montarTitleTooltip('Informar o tipo de presen�a do servidor.') ?>
                tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                <img id="imgAjudaTpPresenca" border="0" style="width: 16px;height: 16px;"
                     src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" class="infraImg"/>
            </a>
            <select id="selTpPresenca" name="selTpPresenca" class="infraSelect" onchange="validarTpPresenca(this.value);"
                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                <?= $strItensSelTpPresenca ?>
            </select>
            <div id="divFtDesemp" style="display: none;" >
                <label id="lblFtDesemp" for="txtFtDesemp" accesskey="" class="infraLabelObrigatorio">Fator de Desempenho Diferenciado:</label>
                <a  id="btnFtDesemp" <?= PaginaSEI::montarTitleTooltip('Informar o percentual esperado de desempenho a maior pelo servidor quando o tipo de presen�a for igual a diferenciado.') ?>
                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                    <img id="imgAjudaFtDesemp" border="0" style="width: 16px;height: 16px;"
                         src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" class="infraImg"/>
                </a>
                <input type="text" id="txtFtDesemp" name="txtFtDesemp" onkeypress="return infraMascaraNumero(this, event)"
                       class="infraText" value="<?= PaginaSEI::tratarHTML($objMdUtlAdmPrmGrDTO->getNumCargaPadrao()); ?>"
                       onkeyup="return validarPercentual(this,'Fator de Desempenho Diferenciado')"
                       tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
            </div>

            <label id="lblTpJornada" for="selTpJornada" accesskey="" class="infraLabelObrigatorio">Tipo de Jornada:</label>
            <a  id="btnTpJornada" <?= PaginaSEI::montarTitleTooltip('Informar a jornada do servidor. Se reduzido deve-se informar o fator de redu��o de desempenho.') ?>
                tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                <img id="imgAjudaTpJornada" border="0" style="width: 16px;height: 16px;"
                     src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" class="infraImg"/>
            </a>
            <select id="selTpJornada" name="selTpJornada" class="infraSelect" onchange="validarTpJornada(this.value);"
                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                <?= $strItensSelTpJornada ?>
            </select>
            <div id="divRedJornada" style="display: none;">
                <label id="lblFtReduc" for="txtFtReduc" accesskey="" class="infraLabelObrigatorio">Fator de Redu��o da Jornada:</label>
                <a  id="btnFtReduc" <?= PaginaSEI::montarTitleTooltip('Informar o percentual de redu��o de jornada para o servidor quando o tipo de jornada for igual a reduzido.') ?>
                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                    <img id="imgAjudaFtReduc" border="0" style="width: 16px;height: 16px;"
                         src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" class="infraImg"/>
                </a>
                <input type="text" id="txtFtReduc" name="txtFtReduc" onkeypress="return infraMascaraNumero(this, event)"
                       class="infraText" value="<?= PaginaSEI::tratarHTML($objMdUtlAdmPrmGrDTO->getNumCargaPadrao()); ?>"
                       onkeyup="return validarPercentual(this,'Fator de Redu��o da Jornada')"
                       tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
            </div>
            <button type="button" class="infraButton" id="btnAdicionar" accesskey="a" onclick="buscarNomeUsuario();"><span class="infraTeclaAtalho">A</span>dicionar</button>

            <table width="99%" class="infraTable" summary="UsuarioParticipante" id="tbUsuario" style="<?php echo $strTbUsuarioPart == '' ? 'display: none' : ''?>">
                <caption class="infraCaption">&nbsp;</caption>
                <tr >
                    <th style="display: none">Id</th>
                    <th class="infraTh"  align="center" >Usu�rio Participante</th> <!--1-->
                    <th class="infraTh"  align="center" >Tipo de Presen�a</th> <!--2-->
                    <th style="display: none"></th>
                    <th class="infraTh" align="center" width="15%">Fator de Desempenho Diferenciado</th> <!--0-->
                    <th class="infraTh"  align="center" >Tipo de jornada</th> <!--3-->
                    <th style="display: none"></th>
                    <th class="infraTh" align="center" width="15%">Fator de redu��o de jornada</th><!--4-->
                    <th style="display: none"></th>
                    <th style="display: none">Nome Usuario hidden</th>

                    <th class="infraTh" align="center" width="0"  >A��es</th><!--5-->

                </tr>
            </table>
        </fieldset>
        </div>
        <input type="hidden" id="hdnTpProcesso" name="hdnTpProcesso" value="<?=$strLupaTpProcesso?>" />
        <input type="hidden" id="hdnUsuario" name="hdnUsuario" value="<?=$_POST['hdnUsuario']?>" />
        <input type="hidden" id="hdnTbUsuario" name="hdnTbUsuario" value="<?=$strTbUsuarioPart?>" />
        <input type="hidden" id="hdnIdTipoControleUtl" name="hdnIdTipoControleUtl" value="<?=$idTipoControleUtl?>" />
        <input type="hidden" id="hdnTbUsuarioRemove" name="hdnTbUsuarioRemove" value=""/>
        <input type="hidden" id="hdnTbUsuarioNovo" name="hdnTbUsuarioNovo" value=""/>
        <?
        PaginaSEI::getInstance()->fecharAreaDados();
        //PaginaSEI::getInstance()->montarAreaDebug();
        PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
        ?>
    </form>
<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
