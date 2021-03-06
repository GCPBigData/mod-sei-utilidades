<?
try{
    require_once dirname(__FILE__).'/../../SEI.php';
    session_start();

    SessaoSEI::getInstance()->validarLink();

    SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

    $objMdUtlAdmJustContestDTO = new MdUtlAdmJustContestDTO();

    $idTpCtrl = array_key_exists('id_tipo_controle_utl', $_GET) ? $_GET['id_tipo_controle_utl'] : $_POST['hdnIdTpCtrlUtl'];
    $objTpControleUtlRN    = new MdUtlAdmTpCtrlDesempRN();
    $objTipoControleUtlDTO = $objTpControleUtlRN->buscarObjTpControlePorId($idTpCtrl);
    $nomeTpCtrl = !is_null($objTipoControleUtlDTO) ? $objTipoControleUtlDTO->getStrNome() : '';

    $arrComandos = array();

    switch ($_GET['acao']){

        case 'md_utl_adm_just_contest_consultar':
            $strTitulo = 'Consultar Justificativa de Contesta��o.';
            $arrComandos[] = '<button type="button" accesskey="C" name="btnFechar" value="Fechar" onclick="location.href=\''.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&id_tipo_controle_utl='.$idTpCtrl.'&acao_origem='.$_GET['acao'].PaginaSEI::getInstance()->montarAncora($_GET['id_md_utl_adm_just_contest'])).'\';" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
            $objMdUtlAdmJustContestDTO->setNumIdMdUtlAdmJustContest($_GET['id_md_utl_adm_just_contest']);
            $objMdUtlAdmJustContestDTO->setBolExclusaoLogica(false);
            $objMdUtlAdmJustContestDTO->retTodos();
            $objMdUtlAdmJustContestRN = new MdUtlAdmJustContestRN();
            $objMdUtlAdmJustContestDTO = $objMdUtlAdmJustContestRN->consultar($objMdUtlAdmJustContestDTO);
            if ($objMdUtlAdmJustContestDTO===null){
                throw new InfraException("Registro n�o encontrado.");
            }
            break;

        case 'md_utl_adm_just_contest_cadastrar':
            $strTitulo = 'Nova Justificativa de Contesta��o';
            $arrComandos[] = '<button type="submit" accesskey="S" name="sbmCadastrarMdUtlAdmJustContestCadastro" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\''.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'].'&id_tipo_controle_utl='.$idTpCtrl).'\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            $objMdUtlAdmJustContestDTO->setNumIdMdUtlAdmJustContest(null);
            $objMdUtlAdmJustContestDTO->setStrNome($_POST['txtNome']);
            $objMdUtlAdmJustContestDTO->setStrDescricao($_POST['txaDescricao']);
            $objMdUtlAdmJustContestDTO->setStrSinAtivo('S');
            $objMdUtlAdmJustContestDTO->setNumIdMdUtlAdmTpCtrlDesemp($idTpCtrl);

            if (isset($_POST['sbmCadastrarMdUtlAdmJustContestCadastro'])) {
                try{
                    $objMdUtlAdmJustContestRN = new MdUtlAdmJustContestRN();
                    $objMdUtlAdmJustContestDTO = $objMdUtlAdmJustContestRN->cadastrar($objMdUtlAdmJustContestDTO);
                    header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'].'&id_tipo_controle_utl='.$idTpCtrl.'&id_md_utl_adm_just_contest='.$objMdUtlAdmJustContestDTO->getNumIdMdUtlAdmJustContest().PaginaSEI::getInstance()->montarAncora($objMdUtlAdmJustContestDTO->getNumIdMdUtlAdmJustContest())));
                    die;
                }catch(Exception $e){
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
            }
            break;

        case 'md_utl_adm_just_contest_alterar':
            $strTitulo = 'Alterar Justificativa de Contesta��o.';
            $arrComandos[] = '<button type="submit" accesskey="S" name="sbmAlterarMdUtlAdmJustContest" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $strDesabilitar = 'disabled="disabled"';

            if (isset($_GET['id_md_utl_adm_just_contest'])) {
                $objMdUtlAdmJustContestDTO->setNumIdMdUtlAdmJustContest($_GET['id_md_utl_adm_just_contest']);
                $objMdUtlAdmJustContestDTO->retTodos();
                $objMdUtlAdmJustContestDTO->setBolExclusaoLogica(false);
                $objMdUtlAdmJustContestRN = new MdUtlAdmJustContestRN();
                $objMdUtlAdmJustContestDTO = $objMdUtlAdmJustContestRN->consultar($objMdUtlAdmJustContestDTO);
                if ($objMdUtlAdmJustContestDTO == null) {
                    throw new InfraException("Registro n�o encontrado.");
                }
            } else {
                $objMdUtlAdmJustContestDTO->setNumIdMdUtlAdmJustContest($_POST['hdnIdMdUtlAdmJustContest']);
                $objMdUtlAdmJustContestDTO->setStrNome($_POST['txtNome']);
                $objMdUtlAdmJustContestDTO->setStrDescricao($_POST['txaDescricao']);
                $objMdUtlAdmJustContestDTO->setNumIdMdUtlAdmTpCtrlDesemp($idTpCtrl);
            }

            $arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\''.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&id_tipo_controle_utl='.$idTpCtrl.'&acao_origem='.$_GET['acao'].PaginaSEI::getInstance()->montarAncora($objMdUtlAdmJustContestDTO->getNumIdMdUtlAdmJustContest())).'\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            if (isset($_POST['sbmAlterarMdUtlAdmJustContest'])) {
                try{
                    $objMdUtlAdmJustContestRN = new MdUtlAdmJustContestRN();
                    $objMdUtlAdmJustContestRN->alterar($objMdUtlAdmJustContestDTO);
                    PaginaSEI::getInstance()->adicionarMensagem('Justificativa de Contesta��o "'.$objMdUtlAdmJustContestDTO->getNumIdMdUtlAdmJustContest().'" alterado com sucesso.');
                    header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'].'&id_tipo_controle_utl='.$idTpCtrl.PaginaSEI::getInstance()->montarAncora($objMdUtlAdmJustContestDTO->getNumIdMdUtlAdmJustContest())));
                    die;
                }catch(Exception $e){
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
            }
            break;

        default:
            throw new InfraException("A��o '".$_GET['acao']."n�o reconhecida.");
    }

}catch (Exception $e){
    PaginaSEI::getInstance()->processarExcecao($e);
}

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(PaginaSEI::getInstance()->getStrNomeSistema().' - '.$strTitulo);
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();
?>
<?if(0){?><style><?}?>
    #lblNome {position:absolute;left:0%;top:6%;width:40%;}
    #ancAjudaNome{position: absolute;
        left: 75px;
        top: 5%;}
    #txtNome {position:absolute;left:0%;top:45%;width:40%;}

    #lblDescricao {position:absolute;left:0%;top:10%;width:60%;}
    #ancAjudaDesc {position:absolute;left:63px;top:10%;}
    #txaDescricao {position:absolute;left:0%;top:29%;width:60%;resize: none}

    .tamanhoBtnAjuda{
        width: 16px;
        height: 16px;
    }

    <?if(0){?></style><?}?>
<?
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
?>
<?if(0){?><script type="text/javascript"><?}?>

    function inicializar(){
        if ('<?=$_GET['acao']?>'=='md_utl_adm_just_contest_cadastrar'){
            document.getElementById('txtNome').focus();
        } else if ('<?=$_GET['acao']?>'=='md_utl_adm_just_contest_consultar'){
            infraDesabilitarCamposAreaDados();
        }else{
            document.getElementById('btnCancelar').focus();
        }
        infraEfeitoTabelas(true);
    }

    function validarCadastro() {
        if (infraTrim(document.getElementById('txtNome').value)=='') {
            alert(' Informe o Nome da Justificativa.');
            document.getElementById('txtNome').focus();
            return false;
        }

        if (infraTrim(document.getElementById('txaDescricao').value)=='') {
            alert('Informe a Descri��o da Justificativa.');
            document.getElementById('txaDescricao').focus();
            return false;
        }

        return true;
    }

    function OnSubmitForm() {
        return validarCadastro();
    }

    <?if(0){?></script><?}?>
<?
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');
?>
    <form id="sbmCadastrarMdUtlAdmJustContestCadastro" method="post" onsubmit="return OnSubmitForm();" action="<?=SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao'].'&acao_origem='.$_GET['acao'])?>">
        <?
        PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
        PaginaSEI::getInstance()->abrirAreaDados('4.5em');
        ?>
        <label id="lblNome" for="txtNome" accesskey="" class="infraLabelObrigatorio">Justificativa:</label>
        <a href="javascript:void(0);" id="ancAjudaNome" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" <?=PaginaSEI::montarTitleTooltip('Nome da Justificativa de Contesta��o de Revis�o.')?>><img class="tamanhoBtnAjuda" src="<?=PaginaSEI::getInstance()->getDiretorioImagensGlobal()?>/ajuda.gif" class="infraImg"/></a>

        <input type="text" id="txtNome" name="txtNome" maxlength="50" class="infraText"  value="<?=PaginaSEI::tratarHTML($objMdUtlAdmJustContestDTO->getStrNome());?>" onkeypress="return infraMascaraTexto(this,event,50);" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
        <?
        PaginaSEI::getInstance()->fecharAreaDados();
        PaginaSEI::getInstance()->abrirAreaDados('9em');
        ?>
        <label id="lblDescricao" for="txaDescricao" accesskey="" class="infraLabelObrigatorio">Descri��o:</label>
        <a href="javascript:void(0);" id="ancAjudaDesc" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" <?=PaginaSEI::montarTitleTooltip('Texto que descreve a Justificativa de Contesta��o de Revis�o.')?>><img class="tamanhoBtnAjuda" src="<?=PaginaSEI::getInstance()->getDiretorioImagensGlobal()?>/ajuda.gif" class="infraImg"/></a>
        <textarea type="text" id="txaDescricao" rows="3" maxlength="250" name="txaDescricao" class="infraTextArea" onkeypress="return infraMascaraTexto(this,event,250);" maxlength="250" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>"><?=PaginaSEI::tratarHTML($objMdUtlAdmJustContestDTO->getStrDescricao());?></textarea>
        <?
        PaginaSEI::getInstance()->fecharAreaDados();
        ?>

        <input type="hidden" id="hdnIdMdUtlAdmJustContest" name="hdnIdMdUtlAdmJustContest" value="<?=$objMdUtlAdmJustContestDTO->getNumIdMdUtlAdmJustContest();?>" />
        <input type="hidden" name="hdnIdTpCtrlUtl" id="hdnIdTpCtrlUtl" value="<?php echo $idTpCtrl ?>"/>
    </form>
<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>