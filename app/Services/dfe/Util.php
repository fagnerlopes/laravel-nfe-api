<?php

declare(strict_types=1);


final class Util
{

    /**
     * @param float $number
     * @param int $decimals
     * @return mixed
     */
    public static function formatNumberForXml(float $number, int $decimals = 2)
    {
        return number_format((float)$number, $decimals, ".", "");
    }

    /**
     * @param string $dateTime
     * @return mixed
     */
    public static function getDateIso(string $dateTime = ''): string
    {
        $fuso = new DateTimeZone('America/Sao_Paulo');
        $date = new DateTime();
        $date->setTimezone($fuso);

        return $date->format('Y-m-d\TH:i:sP');
    }

    public static function getDateFormat(string $dateTime = '', string $format = ''): string
    {
        $fuso = new DateTimeZone('America/Sao_Paulo');
        $date = new DateTime($dateTime);
        $date->setTimezone($fuso);

        return $date->format($format);
    }

    public static function getMonthPortuguese(string $dateTime = ''): string
    {
        //setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
        //date_default_timezone_set('America/Sao_Paulo');
        $fuso = new DateTimeZone('America/Sao_Paulo');
        $date = new DateTime($dateTime);
        $date->setTimezone($fuso);

        $mes_extenso = array(
            'Jan' => 'Janeiro',
            'Feb' => 'Fevereiro',
            'Mar' => 'Marco',
            'Apr' => 'Abril',
            'May' => 'Maio',
            'Jun' => 'Junho',
            'Jul' => 'Julho',
            'Aug' => 'Agosto',
            'Nov' => 'Novembro',
            'Sep' => 'Setembro',
            'Oct' => 'Outubro',
            'Dec' => 'Dezembro'
        );

        return strtolower($mes_extenso[$date->format('M')]);
    }

    public static function getUnitStoragePath(): string
    {
        $dirUnit = strtolower(str_replace(' ', '_', TSession::getValue('userunitname')));

        return "app/storage/{$dirUnit}/xml_dfe_files";
    }

    public static function getXmlPath(string $keyNfe): string
    {
        $dirUnit = strtolower(str_replace(' ', '_', TSession::getValue('userunitname')));

        return "app/storage/xml_dfe_files/{$dirUnit}/xmlDfe-/{$keyNfe}.xml";
    }

    public static function getLogoPath(string $keyNfe): string
    {
        $dirUnit = strtolower(str_replace(' ', '_', TSession::getValue('userunitname')));

        return "app/storage/xml_dfe_files/{$dirUnit}/xmlDfe-/{$keyNfe}.xml";
    }

    public static function saveXml(string $xml, string $fileName):bool
    {
        try {

            $dirAno = Util::getDateFormat('', 'Y');
            $dirDia = Util::getDateFormat('', 'd');
            $dirMes = Util::getMonthPortuguese();

            $dirUnit = strtolower(str_replace(' ', '_', TSession::getValue('userunitname')));

            //$caminhoRepositorioXml = "app/storage/{$dirUnit}/xml_dfe_files/{$dirAno}/{$dirMes}/{$dirDia}";

            $caminhoRepositorioXml = "app/storage/06103611000141/xml/{$dirAno}/{$dirMes}/{$dirDia}";

            if(!is_dir($caminhoRepositorioXml)){
                mkdir($caminhoRepositorioXml, 0777, true);
            }

            $handler = fopen("{$caminhoRepositorioXml}/{$fileName}.xml", 'w');
            fwrite($handler, $xml);
            fclose($handler);

            return true;

        } catch( Exception $e) {
            exit($e->getMessage());
        }
    }

    public static function savePDF(string $pdf, string $fileName):bool
    {
        try {

            $dirAno = Util::getDateFormat('', 'Y');
            $dirDia = Util::getDateFormat('', 'd');
            $dirMes = Util::getMonthPortuguese();

            $dirUnit = strtolower(str_replace(' ', '_', TSession::getValue('userunitname')));

            //$caminhoRepositorioXml = "app/storage/{$dirUnit}/xml_dfe_files/{$dirAno}/{$dirMes}/{$dirDia}";

            $caminhoRepositorioPdf= "app/storage/06103611000141/pdf/{$dirAno}/{$dirMes}/{$dirDia}";

            if(!is_dir($caminhoRepositorioPdf)){
                mkdir($caminhoRepositorioPdf, 0777, true);
            }

            $handler = fopen("{$caminhoRepositorioPdf}/{$fileName}.pdf", 'w');
            fwrite($handler, $pdf);
            fclose($handler);

            return true;

        } catch( Exception $e) {
            exit($e->getMessage());
        }
    }

    public static function getPathClientRespository(string $type_file)
    {
        try {

            $dirAno = Util::getDateFormat('', 'Y');
            $dirDia = Util::getDateFormat('', 'd');
            $dirMes = Util::getMonthPortuguese();

            $dirUnit = strtolower(str_replace(' ', '_', TSession::getValue('documento_dono')));

            $pathClientRespository = "app/storage/{$dirUnit}/{$type_file}/{$dirAno}/{$dirMes}/{$dirDia}";

            if(!is_dir($pathClientRespository)){
                mkdir($pathClientRespository, 0777, true);
            }

            return $pathClientRespository;

        } catch( Exception $e) {
            exit($e->getMessage());
        }
    }

    public static function getPathCertificateA1()
    {
        try {

            $cnpj = strtolower(str_replace(' ', '_', TSession::getValue('documento_dono')));

            $pathCertificate = "app/storage/{$cnpj}/certificate/{$cnpj}.pfx";

            return $pathCertificate;

        } catch( Exception $e) {
            exit($e->getMessage());
        }
    }

}