<?

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdUtlAgendamentoAutomaticoRN extends InfraRN
{

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    public static $SIM = 'S';
    public static $NAO = 'N';

    /* M�todo respons�vel por atualizar o andamento dos objs no correios */
    protected function aprovarReprovarAjustesPrazoControlado()
{

    try {
        ini_set('max_execution_time', '0');
        ini_set('memory_limit', '1024M');

        InfraDebug::getInstance()->setBolLigado(true);
        InfraDebug::getInstance()->setBolDebugInfra(false);
        InfraDebug::getInstance()->setBolEcho(false);
        InfraDebug::getInstance()->limpar();

        $numSeg = InfraUtil::verificarTempoProcessamento();
        InfraDebug::getInstance()->gravar('ATUALIZANDO STATUS DO OBJETO DE UTILIDADES');

        $objCtrlDsmpRN = new MdUtlControleDsmpRN();
        $objGestaoAjustPrazoRN = new MdUtlGestaoAjustPrazoRN();

        /*Busca id do usu�rio de utilidades para agendamento autom�tico do sistema*/
        $objUsuarioRN = new MdUtlUsuarioRN();
        $objUsuarioDTO = new UsuarioDTO();
        $objUsuarioDTO = $objUsuarioRN->getObjUsuarioUtilidades();

        /*Verificar todas as solicita��es de de ajuste de prazo com a situa��o igual a pendente de resposta do gestor e que o prazo atual para a execu��o da tarefa vence em D-1*/
        $arrObjCtrlDsmpDTO = $this->_verificarAjustePendente();

        /*Busca na parametriza��o a resposta t�cita*/
        $arrRetorno = $this->_buscarRespostaTacitaParametrizacao($arrObjCtrlDsmpDTO);

        /*Percorre os objetos e aprova ou reprova conforme sua resposta t�cita*/
        foreach ($arrObjCtrlDsmpDTO as $objDTO){
            $idCtrlDsmp = $objDTO->getNumIdMdUtlControleDsmp();
            $idTpCtrlDsmp = $objDTO->getNumIdMdUtlAdmTpCtrlDesemp();
            $strTipoDeSolicitacao = $objDTO->getStrStaTipoSolicitacaoAjustePrazo();
            $idUnidade = $objDTO->getNumIdUnidade();
            $respTacitaFila      = $objDTO->getStrRespTacitaDilacao();
            $respostaTacitaMain  = $arrRetorno[$idTpCtrlDsmp][$strTipoDeSolicitacao];

            if(!is_null($respTacitaFila)&& $strTipoDeSolicitacao == MdUtlControleDsmpRN::$TP_SOLICITACAO_DILACAO){
                $respostaTacitaMain = $respTacitaFila;
            }

            SessaoSEI::getInstance()->simularLogin(null, null, $objUsuarioDTO->getNumIdUsuario(), $idUnidade );

            $objControleDsmpDTO = new MdUtlControleDsmpDTO();

            $objControleDsmpDTO->setNumIdMdUtlControleDsmp($idCtrlDsmp);
            $objControleDsmpDTO->retTodos();
            $objControleDsmpDTO->retStrProtocoloProcedimentoFormatado();
            $objControleDsmpDTO->retNumIdContato();
            $objControleDsmpDTO->retStrEmail();
            $objControleDsmpDTO->setNumMaxRegistrosRetorno(1);
            $objControleDsmpDTO = $objCtrlDsmpRN->consultar($objControleDsmpDTO);

            if($respostaTacitaMain == MdUtlAdmPrmGrRN::$APROVACAO_TACITA){
                $objGestaoAjustPrazoRN->aprovarSolicitacao($objControleDsmpDTO);
            } else {
                $objGestaoAjustPrazoRN->reprovarSolicitacao($objControleDsmpDTO);
            }

        }


        $numSeg = InfraUtil::verificarTempoProcessamento($numSeg);
        InfraDebug::getInstance()->gravar('TEMPO TOTAL DE EXECUCAO: ' . $numSeg . ' s');
        InfraDebug::getInstance()->gravar('FIM');

        LogSEI::getInstance()->gravar(InfraDebug::getInstance()->getStrDebug(), InfraLog::$INFORMACAO);

        InfraDebug::getInstance()->setBolLigado(false);
        InfraDebug::getInstance()->setBolDebugInfra(false);
        InfraDebug::getInstance()->setBolEcho(false);

    } catch (Exception $e) {

        InfraDebug::getInstance()->setBolLigado(false);
        InfraDebug::getInstance()->setBolDebugInfra(false);
        InfraDebug::getInstance()->setBolEcho(false);
        throw new InfraException('Erro atualizando os objetos em andamento de utilidades.', $e);
    }

}

    protected function retornarStatusFinalControlado()
    {

        try {
            ini_set('max_execution_time', '0');
            ini_set('memory_limit', '1024M');

            InfraDebug::getInstance()->setBolLigado(true);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
            InfraDebug::getInstance()->limpar();

            $numSeg = InfraUtil::verificarTempoProcessamento();
            InfraDebug::getInstance()->gravar('ATUALIZANDO STATUS DO OBJETO DE UTILIDADES');

            $objMdUtlControleRN = new MdUtlControleDsmpRN();
            $objMdUtlControleRN->retornaStatusImpedido();

            $numSeg = InfraUtil::verificarTempoProcessamento($numSeg);
            InfraDebug::getInstance()->gravar('TEMPO TOTAL DE EXECUCAO: ' . $numSeg . ' s');
            InfraDebug::getInstance()->gravar('FIM');

            LogSEI::getInstance()->gravar(InfraDebug::getInstance()->getStrDebug(), InfraLog::$INFORMACAO);

            InfraDebug::getInstance()->setBolLigado(false);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);

        } catch (Exception $e) {

            InfraDebug::getInstance()->setBolLigado(false);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
            throw new InfraException('Erro atualizando os objetos em andamento de utilidades.', $e);
        }

    }

    protected function associarProcessoFilaControlado()
    {

        try {
            ini_set('max_execution_time', '0');
            ini_set('memory_limit', '1024M');

            InfraDebug::getInstance()->setBolLigado(true);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
            InfraDebug::getInstance()->limpar();

            $numSeg = InfraUtil::verificarTempoProcessamento();
            InfraDebug::getInstance()->gravar('ATUALIZANDO STATUS DO OBJETO DE UTILIDADES');

            $objMdUtlControleDsmpRN = new MdUtlControleDsmpRN();
            $objHistoricoRN         = new MdUtlHistControleDsmpRN();

            $this->_getAguardandoFila();

            $numSeg = InfraUtil::verificarTempoProcessamento($numSeg);
            InfraDebug::getInstance()->gravar('TEMPO TOTAL DE EXECUCAO: ' . $numSeg . ' s');
            InfraDebug::getInstance()->gravar('FIM');

            LogSEI::getInstance()->gravar(InfraDebug::getInstance()->getStrDebug(), InfraLog::$INFORMACAO);

            InfraDebug::getInstance()->setBolLigado(false);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);

        } catch (Exception $e) {

            InfraDebug::getInstance()->setBolLigado(false);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
            throw new InfraException('Erro atualizando os objetos em andamento de utilidades.', $e);
        }

    }

    private function _getAguardandoFila()
    {
        $objMdUtlTpCtrlDTO = new MdUtlAdmTpCtrlDesempDTO();
        $objMdUtlTpCtrlRN = new MdUtlAdmTpCtrlDesempRN();

        $objMdUtlTpCtrlDTO->adicionarCriterio(array('IdMdUtlAdmFila'), array(InfraDTO::$OPER_DIFERENTE), array(null), array(), 'CriterioFilaPadrao');
        $objMdUtlTpCtrlDTO->adicionarCriterio(array('IdMdUtlAdmFila', 'SinUltimaFila'), array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL), array(null, 'S'), InfraDTO::$OPER_LOGICO_AND, 'CriterioUltimaFila');
        $objMdUtlTpCtrlDTO->agruparCriterios(array('CriterioFilaPadrao', 'CriterioUltimaFila'), InfraDTO::$OPER_LOGICO_OR);

        $objMdUtlTpCtrlDTO->retNumIdMdUtlAdmTpCtrlDesemp();
        $objMdUtlTpCtrlDTO->retNumIdMdUtlAdmFila();
        $objMdUtlTpCtrlDTO->setParametroFiltroFk(InfraDTO::$FILTRO_FK_WHERE);
        $objMdUtlTpCtrlDTO->setParametroFk(InfraDTO::$TIPO_FK_OBRIGATORIA);
        $arrObjsTpCtrlDTO = $objMdUtlTpCtrlRN->listar($objMdUtlTpCtrlDTO);

        $arrTiposControleCondicional = array();
        $arrTiposControleUtilizados = array();

        foreach ($arrObjsTpCtrlDTO as $objDTO) {
            if (is_null($objDTO->getNumIdMdUtlAdmFila())) {
                $arrTiposControleCondicional[] = $objDTO->getNumIdMdUtlAdmTpCtrlDesemp();
            } else {
                $arrTiposControleUtilizados[] = $objDTO->getNumIdMdUtlAdmTpCtrlDesemp();
            }
        }

        if (count($arrTiposControleUtilizados) > 0 || count($arrTiposControleCondicional) > 0) {
            $idsTiposProcessoParametrizados = $objMdUtlTpCtrlRN->getTiposProcessoTodosTipoControle();

            if (count($idsTiposProcessoParametrizados) > 0) {
                $idsTiposProcessoParametrizados = array_unique($idsTiposProcessoParametrizados);
                $objMdUtlAtvPrincipalDTO = new MdUtlAtividadePrincipalDTO();

                $objMdUtlAtvPrincipalDTO->retNumIdUnidade();
                $objMdUtlAtvPrincipalDTO->retDblIdProtocolo();
                $objMdUtlAtvPrincipalDTO->retNumIdMdUtlAdmTpCtrlDesemp();
                $objMdUtlAtvPrincipalDTO->retNumIdUtlTipoProcedimentoProcedimento();
                $objMdUtlAtvPrincipalDTO->setOrd('Abertura', InfraDTO::$TIPO_ORDENACAO_ASC);
                $objMdUtlAtvPrincipalDTO->setNumMaxRegistrosRetorno(10000);
                $objMdUtlAtvPrincipalDTO->setNumIdUtlTipoProcedimentoProcedimento($idsTiposProcessoParametrizados, InfraDTO::$OPER_IN);
                $objMdUtlAtvPrincipalDTO->setDthConclusao(null);
                $objMdUtlAtvPrincipalDTO->setNumIdMdUtlControleDsmp(null);
                $objMdUtlAtvPrincipalDTO->setStrStaUtlNivelAcessoLocalProtocolo(array(ProtocoloRN::$NA_PUBLICO, ProtocoloRN::$NA_RESTRITO), InfraDTO::$OPER_IN);

  /*              $objMdUtlAtvPrincipalDTO->adicionarCriterio(array('Conclusao', 'IdMdUtlControleDsmp'),
                    array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL),
                    array(null, null),
                    array(InfraDTO::$OPER_LOGICO_AND),
                    'CriterioPrincipal');

                $arrGrupos[] = 'CriterioPrincipal';*/

                if (count($arrTiposControleCondicional) > 0) {
                    $objMdUtlAtvPrincipalDTO->adicionarCriterio(array('IdMdUtlAdmTpCtrlDesemp', 'IdMdUtlHistControleDsmp'),
                        array(InfraDTO::$OPER_IN, InfraDTO::$OPER_DIFERENTE),
                        array($arrTiposControleCondicional, null),
                        array(InfraDTO::$OPER_LOGICO_AND),
                        'CriterioUltimasFilas');
                    $arrGrupos[] = 'CriterioUltimasFilas';
                }

                if (count($arrTiposControleUtilizados) > 0) {
                    $objMdUtlAtvPrincipalDTO->adicionarCriterio(array('IdMdUtlAdmTpCtrlDesemp', 'IdMdUtlControleDsmp'),
                        array(InfraDTO::$OPER_IN, InfraDTO::$OPER_IGUAL),
                        array($arrTiposControleUtilizados, null),
                        array(InfraDTO::$OPER_LOGICO_AND),
                        'CriterioFilaPadrao');

                    $arrGrupos[] = 'CriterioFilaPadrao';
                }

                if (count($arrGrupos) == 2) {
                    $objMdUtlAtvPrincipalDTO->agruparCriterios($arrGrupos, array(InfraDTO::$OPER_LOGICO_OR));
                }

                $objMdUtlAtvPrincipalDTO->setDistinct(true);

                $objAtividadeRN = new AtividadeRN();
                $objControleDsmpRN = new MdUtlControleDsmpRN();
                $objHistoricoRN = new MdUtlHistControleDsmpRN();

                $arrObjs = $objAtividadeRN->listarRN0036($objMdUtlAtvPrincipalDTO);

                /*Busca id do usu�rio de utilidades para agendamento autom�tico do sistema*/
                $objUsuarioRN = new MdUtlUsuarioRN();
                $objUsuarioDTO = new UsuarioDTO();
                $objUsuarioDTO = $objUsuarioRN->getObjUsuarioUtilidades();

                if (count($arrObjs) > 0) {
                    $arrTipoProcedimentoCompleto = $objMdUtlTpCtrlRN->getObjTipoControlePorPrm();

                    foreach ($arrObjs as $obj) {
                        $idFilaHistorico = '';
                        $idFilaPadrao = '';

                        $idAtendimentoAntigo = 1;
                        $idProcedimento = $obj->getDblIdProtocolo();
                        $idTipoControle = $obj->getNumIdMdUtlAdmTpCtrlDesemp();
                        $idUnidade = $obj->getNumIdUnidade();
                        $idTipoProcedimento = $obj->getNumIdUtlTipoProcedimentoProcedimento();

                        $arrTiposProcedimento = array_key_exists($idTipoControle, $arrTipoProcedimentoCompleto) ? $arrTipoProcedimentoCompleto[$idTipoControle] : null;

                        if (is_null($arrTiposProcedimento)) {
                            break;
                        }

                        if (array_key_exists($idTipoProcedimento, $arrTiposProcedimento)) {


                            SessaoSEI::getInstance()->simularLogin(null, null, $objUsuarioDTO->getNumIdUsuario(), $idUnidade);

                            $objMdUtlTpCtrlDTO = new MdUtlAdmTpCtrlDesempDTO();
                            $objMdUtlTpCtrlDTO->setNumIdMdUtlAdmTpCtrlDesemp($idTipoControle);
                            $objMdUtlTpCtrlDTO->retStrSinUltimaFila();
                            $objMdUtlTpCtrlDTO->retNumIdMdUtlAdmFila();
                            $objMdUtlTpCtrlDTO->setNumMaxRegistrosRetorno(1);

                            $numRegistrosTpCtrl = $objMdUtlTpCtrlRN->contar($objMdUtlTpCtrlDTO);
                            $objMdUtlTpCtrlDTO = $objMdUtlTpCtrlRN->consultar($objMdUtlTpCtrlDTO);

                            if ($numRegistrosTpCtrl > 0) {
                                $nomeFila = '';
                                $sinUltimaFila = $objMdUtlTpCtrlDTO->getStrSinUltimaFila();
                                $idFilaPadrao = $objMdUtlTpCtrlDTO->getNumIdMdUtlAdmFila();
                                $undEsforco = 0;

                                //Busca os dados da Fila Padr�o
                                if (!is_null($objMdUtlTpCtrlDTO->getNumIdMdUtlAdmFila())) {
                                    $objFilaRN = new MdUtlAdmFilaRN();
                                    $objFilaDTO = new MdUtlAdmFilaDTO();
                                    $objFilaDTO->setNumIdMdUtlAdmFila($objMdUtlTpCtrlDTO->getNumIdMdUtlAdmFila());
                                    $objFilaDTO->retTodos();
                                    $numRegistrosFila = $objFilaRN->contar($objFilaDTO);
                                    $objFilaDTO = $objFilaRN->consultar($objFilaDTO);

                                    if ($numRegistrosFila > 0) {
                                        $nomeFila = $objFilaDTO->getStrNome();
                                        $undEsforco = $objFilaDTO->getStrUndEsforcoTriagem();
                                    }
                                }

                                /*Se possuir ultima fila - buscar em historico e atribuir*/
                                if ($sinUltimaFila == 'S') {
                                    $objHistoricoDTO = new MdUtlHistControleDsmpDTO();
                                    $objHistoricoDTO->setDblIdProcedimento($idProcedimento);
                                    $objHistoricoDTO->setNumIdUnidade($idUnidade);
                                    $objHistoricoDTO->setOrdDthAtual(InfraDTO::$TIPO_ORDENACAO_DESC);
                                    $objHistoricoDTO->setNumMaxRegistrosRetorno(1);
                                    $objHistoricoDTO->retNumIdMdUtlAdmFila();
                                    $objHistoricoDTO->retNumIdAtendimento();
                                    $objHistoricoDTO->retStrNomeFila();

                                    $numRegistrosHist = $objHistoricoRN->contar($objHistoricoDTO);
                                    $objHistoricoDTO = $objHistoricoRN->consultar($objHistoricoDTO);

                                    if ($numRegistrosHist > 0) {
                                        $idFilaHistorico = $objHistoricoDTO->getNumIdMdUtlAdmFila();
                                        $nomeUltimaFila = $objHistoricoDTO->getStrNomeFila();
                                        $idAtendimentoAntigo = $objHistoricoDTO->getNumIdAtendimento() != '' ? $objHistoricoDTO->getNumIdAtendimento() : $idAtendimentoAntigo;
                                    }
                                }

                                /* Se a �ltima fila estiver como SIM e existir ultima fila */
                                if ($sinUltimaFila == 'S' && $idFilaHistorico != '') {
                                    $idFilaCompleto = $idFilaHistorico;
                                    $nomeFilaCompleto = $nomeUltimaFila;
                                }

                                /* Se a �ltima fila estiver como SIM e N�O existir ultima fila, por�m existir  fila Padr�o */
                                if ($sinUltimaFila == 'S' && $idFilaHistorico == '' && $idFilaPadrao != '') {
                                    $idFilaCompleto = $idFilaPadrao;
                                    $nomeFilaCompleto = $nomeFila;
                                }

                                /* Se a �ltima fila estiver como N�O e Fila Padr�o estiver SIM */
                                if (($sinUltimaFila == 'N' || is_null($sinUltimaFila)) && $idFilaPadrao != '') {
                                    $idFilaCompleto = $idFilaPadrao;
                                    $nomeFilaCompleto = $nomeFila;
                                }

                                if ($sinUltimaFila == 'S' && $idFilaHistorico == '' && $idFilaPadrao == '') {
                                    break;
                                }

                                if (($sinUltimaFila == 'N' || is_null($sinUltimaFila)) && $idFilaHistorico == '' && $idFilaPadrao == '') {
                                    break;
                                }

                                $idAtendimento = $idAtendimentoAntigo + 1;

                                if ($idProcedimento != '' && $idUnidade != '') {
                                    $isExiste = $objControleDsmpRN->validaExistenciaProcessoAtivo(array($idProcedimento, $idUnidade));
                                    $isFilasPreenchidas = $idFilaHistorico != '' || $idFilaPadrao != '';

                                    if (!$isExiste && $isFilasPreenchidas) {
                                        $arrParams = array($idProcedimento, $idFilaCompleto, $idTipoControle, MdUtlControleDsmpRN::$AGUARDANDO_TRIAGEM, $idUnidade, $undEsforco, null, null, null, null, $nomeFilaCompleto, MdUtlControleDsmpRN::$STR_TIPO_ACAO_ASSOCIACAO, $idAtendimento, null, null, date('d/m/Y H:i:s', strtotime('+3 second')));
                                        $objControleDsmpRN->cadastrarNovaSituacaoProcesso($arrParams);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /*M�todo �ra consultar os ajustes pendentes e com data igual a D-1*/
    private function _verificarAjustePendente(){
        $data          = InfraData::getStrDataAtual();
        $data          .= ' 00:00:00';

        $objCtrlDsmpDTO = new MdUtlControleDsmpDTO();
        $objCtrlDsmpDTO->setStrStaSolicitacaoAjustePrazo(MdUtlAjustePrazoRN::$PENDENTE_RESPOSTA);
        $objCtrlDsmpDTO->setDthPrazoTarefa($data, InfraDTO::$OPER_MENOR);
        $objCtrlDsmpDTO->setAjustePrazoFK(InfraDTO::$TIPO_FK_OBRIGATORIA);
        $objCtrlDsmpDTO->retTodos();
        $objCtrlDsmpDTO->retDthPrazoTarefa();
        $objCtrlDsmpDTO->retStrStaAtendimentoDsmp();
        $objCtrlDsmpDTO->retStrStaTipoSolicitacaoAjustePrazo();
        $objCtrlDsmpDTO->retStrRespTacitaDilacao();

        $objCtrlDsmpRN  = new MdUtlControleDsmpRN();
        $arrObjCtrlDsmpDTO = $objCtrlDsmpRN->listar($objCtrlDsmpDTO);

        return $arrObjCtrlDsmpDTO;
    }

    /*M�todo que busca a resposta t�cita da parametriza��o*/
    private function _buscarRespostaTacitaParametrizacao($arrObjCtrlDesempDTO){
        $arrIdTpCtrlDsmp = InfraArray::converterArrInfraDTO($arrObjCtrlDesempDTO, 'IdMdUtlAdmTpCtrlDesemp');
        $arrIdTpCtrlDsmp = ($arrIdTpCtrlDsmp) ? array_unique($arrIdTpCtrlDsmp) : null;

        if(!is_null($arrIdTpCtrlDsmp)){
            $objTpCtrlDsmpRN  = new MdUtlAdmTpCtrlDesempRN();
            $objTpCtrlDsmpDTO = new MdUtlAdmTpCtrlDesempDTO();
            $objTpCtrlDsmpDTO->setNumIdMdUtlAdmTpCtrlDesemp($arrIdTpCtrlDsmp, InfraDTO::$OPER_IN);
            $objTpCtrlDsmpDTO->retTodos();
            $objTpCtrlDsmpDTO->retStrRespTacitaDilacao();
            $objTpCtrlDsmpDTO->retStrRespTacitaInterrupcao();
            $objTpCtrlDsmpDTO->retStrRespTacitaSuspensao();
            $arrObjs = $objTpCtrlDsmpRN->listar($objTpCtrlDsmpDTO);

            $arrRetorno = [];

            foreach ($arrObjs as $obj){
                $arrRetorno[$obj->getNumIdMdUtlAdmTpCtrlDesemp()] = array(
                    MdUtlControleDsmpRN::$TP_SOLICITACAO_DILACAO => $obj->getStrRespTacitaDilacao(),
                    MdUtlControleDsmpRN::$TP_SOLICITACAO_SUSPENSAO => $obj->getStrRespTacitaSuspensao(),
                    MdUtlControleDsmpRN::$TP_SOLICITACAO_INTERRUPCAO => $obj->getStrRespTacitaInterrupcao(),
                );

            }
        }

        return $arrRetorno;
    }


}
