<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4� REGI�O
 *
 * 09/10/2018 - criado por jhon.carvalho
 *
 * Vers�o do Gerador de C�digo: 1.41.0
 */

require_once dirname(__FILE__).'/../../../SEI.php';

class MdUtlHistControleDsmpINT extends InfraINT {


    public static function formatarDataHora($dataHoraCompleta){
        $arrDataHoraCompleta = explode(' ', $dataHoraCompleta);
        $dataCompleta = $arrDataHoraCompleta[0];
        $horaCompleta = $arrDataHoraCompleta[1];
        $arrHoraCompleta =  explode(':', $horaCompleta);

        $hora = $arrHoraCompleta[0];
        $minuto = $arrHoraCompleta[1];

        return $dataCompleta.' '.$hora.':'.$minuto;
    }

}
