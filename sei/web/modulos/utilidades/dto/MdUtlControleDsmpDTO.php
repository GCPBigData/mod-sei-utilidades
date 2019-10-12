<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4� REGI�O
*
* 28/02/2019 - criado por jaqueline.mendes
*
* Vers�o do Gerador de C�digo: 1.41.0
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdUtlControleDsmpDTO extends InfraDTO {

  private $AjustePrazoFK = null;

  public function __construct()
  {
    $this->AjustePrazoFK = InfraDTO::$TIPO_FK_OPCIONAL;
    parent::__construct();
  }


  public function getStrNomeTabela()
  {
    return 'md_utl_controle_dsmp';
  }

  public function montar() {

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdUtlControleDsmp', 'id_md_utl_controle_dsmp');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DBL, 'IdProcedimento', 'id_procedimento');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdUtlAdmFila', 'id_md_utl_adm_fila');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdUnidade', 'id_unidade');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdUsuarioAtual', 'id_usuario');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdUsuarioDistribuicao', 'id_usuario_distribuicao');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdUtlAdmTpCtrlDesemp', 'id_md_utl_adm_tp_ctrl_desemp');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdUtlTriagem', 'id_md_utl_triagem');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdUtlAnalise', 'id_md_utl_analise');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdUtlRevisao', 'id_md_utl_revisao');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'UnidadeEsforco', 'unidade_esforco');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH, 'Atual', 'dth_atual');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdAtendimento', 'id_atendimento');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'TipoAcao', 'tipo_acao');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'Detalhe', 'detalhe');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH, 'PrazoTarefa', 'dth_prazo_tarefa');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'StaAtendimentoDsmp', 'sta_atendimento_dsmp');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdUtlAjustePrazo', 'id_md_utl_ajuste_prazo');

    $this->configurarPK('IdMdUtlControleDsmp',InfraDTO::$TIPO_PK_NATIVA);

    $this->configurarFK('IdMdUtlAdmFila', 'md_utl_adm_fila fila', 'fila.id_md_utl_adm_fila');
    $this->configurarFK('IdUnidade', 'unidade und', 'und.id_unidade');
    $this->configurarFK('IdUsuarioAtual', 'usuario ua', 'ua.id_usuario');
    $this->configurarFK('IdUsuarioDistribuicao', 'usuario ud', 'ud.id_usuario', InfraDTO::$TIPO_FK_OPCIONAL);
    $this->configurarFK('IdMdUtlAdmTpCtrlDesemp', 'md_utl_adm_tp_ctrl_desemp', 'id_md_utl_adm_tp_ctrl_desemp');
    $this->configurarFK('IdProcedimento', 'procedimento proced', 'proced.id_procedimento');
    $this->configurarFK('IdProcedimento', 'protocolo prot', 'prot.id_protocolo');
    $this->configurarFK('IdTpProcedimento', 'tipo_procedimento tp', 'tp.id_tipo_procedimento');
    $this->configurarFK('IdMdUtlTriagem', 'md_utl_triagem tri', 'tri.id_md_utl_triagem', InfraDTO::$TIPO_FK_OPCIONAL);
    $this->configurarFK('IdMdUtlAnalise', 'md_utl_analise anl', 'anl.id_md_utl_analise', InfraDTO::$TIPO_FK_OPCIONAL);
    $this->configurarFK('IdMdUtlRevisao', 'md_utl_revisao rev', 'rev.id_md_utl_revisao', InfraDTO::$TIPO_FK_OPCIONAL);

    $this->configurarFK('IdMdUtlAdmFilaEncTriagem', 'md_utl_adm_fila filatri', 'filatri.id_md_utl_adm_fila', InfraDTO::$TIPO_FK_OPCIONAL);
    $this->configurarFK('IdMdUtlAdmFilaEncAnalise', 'md_utl_adm_fila filaanl', 'filaanl.id_md_utl_adm_fila', InfraDTO::$TIPO_FK_OPCIONAL);

    $this->configurarFK('IdMdUtlAjustePrazo', 'md_utl_ajuste_prazo mdap', 'mdap.id_md_utl_ajuste_prazo', $this->AjustePrazoFK);
    $this->configurarFK('IdMdUtlAdmJustPrazo', 'md_utl_adm_just_prazo majp', 'majp.id_md_utl_adm_just_prazo');
    $this->configurarFK('IdContato', 'contato ct', 'ct.id_contato');
    
    //Fila
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeFila','fila.nome','md_utl_adm_fila fila');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeFilaEncTriagem','filatri.nome','md_utl_adm_fila filatri');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeFilaEncAnalise','filaanl.nome','md_utl_adm_fila filaanl');
	$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'RespTacitaDilacao', 'fila.resp_tacita_dilacao', 'md_utl_adm_fila fila');

    //Dados do Processo - Geral
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdTpProcedimento','proced.id_tipo_procedimento','procedimento proced');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'ProtocoloProcedimentoFormatado','prot.protocolo_formatado','protocolo prot');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeTipoProcesso','tp.nome','tipo_procedimento tp');
	
    //Usu�rio Distribui��o
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeUsuarioDistribuicao','ud.nome','usuario ud');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'SiglaUsuarioDistribuicao','ud.sigla','usuario ud');

    //Usu�rio Atual
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeUsuarioAtual','ua.nome','usuario ua');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'SiglaUsuarioAtual','ua.sigla','usuario ua');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdContato', 'ua.id_contato', 'usuario ua');

    //Triagem
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'SinAtivoTriagem','tri.sin_ativo','md_utl_triagem tri');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'StaEncaminhamentoTriagem','tri.sta_encaminhamento_triagem','md_utl_triagem tri');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdMdUtlAdmFilaEncTriagem','tri.id_md_utl_adm_fila','md_utl_triagem tri');

    //An�lise
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'SinAtivoAnalise','anl.sin_ativo','md_utl_analise anl');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'StaEncaminhamentoAnalise','anl.sta_encaminhamento_analise','md_utl_analise anl');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdMdUtlAdmFilaEncAnalise','anl.id_md_utl_adm_fila','md_utl_analise anl');

    //Revis�o
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'SinAtivoRevisao','rev.sin_ativo','md_utl_revisao rev');

    //Unidade
     $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'SiglaUnidade','und.sigla','unidade und');

    //Ajuste de Prazo
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'SinAtivoAjustePrazo', 'mdap.sin_ativo', 'md_utl_ajuste_prazo mdap');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'StaSolicitacaoAjustePrazo', 'mdap.sta_solicitacao', 'md_utl_ajuste_prazo mdap');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'StaTipoSolicitacaoAjustePrazo', 'mdap.sta_tipo_solicitacao', 'md_utl_ajuste_prazo mdap');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'DthPrazoInicialAjustePrazo', 'mdap.dth_prazo_inicial', 'md_utl_ajuste_prazo mdap');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'DthPrazoSolicitacaoAjustePrazo', 'mdap.dth_prazo_solicitacao', 'md_utl_ajuste_prazo mdap');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdMdUtlAdmJustPrazo', 'mdap.id_md_utl_adm_just_prazo', 'md_utl_ajuste_prazo mdap');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'Observacao', 'mdap.observacao', 'md_utl_ajuste_prazo mdap');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'DiasUteisExcedentes', 'mdap.dias_uteis_excedentes', 'md_utl_ajuste_prazo mdap');

    //Justificativa de Prazo - Administra��o
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeJustificativa', 'majp.nome','md_utl_adm_just_prazo majp');

    //Contato
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'Email', 'ct.email','contato ct');


    //Atributos de Apoio
     $this->adicionarAtributo(InfraDTO::$PREFIXO_STR,'SinVerificarPermissao');

  }

  /**
   * @return int|null
   */
  public function getAjustePrazoFK()
  {
    return $this->AjustePrazoFK;
  }

  /**
   * @param int|null $AjustePrazoFK
   */
  public function setAjustePrazoFK($AjustePrazoFK)
  {
    $this->AjustePrazoFK = $AjustePrazoFK;
  }
}
