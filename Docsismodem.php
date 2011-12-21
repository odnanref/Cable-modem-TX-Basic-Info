<?php

/**
 * Description of Docsismodem
 *
 * @author andref
 */
class DM_Model_Docsismodem 
{

    /**
     *
     * @var DM__Model_Docsismodems
     */
    protected $_table;
    
    protected $_data = array(
        'macaddr' => '',
        'config_file' => '',
        'ipaddress' => '',
        'estado' => '',
        'serialnum' => '',
        'ipaddr' => '',
        'idmodelo' => '',
        'first_online' => '',
        'last_online' => '',
        'reg_count' => '',
        'cmts_vlan' => '',
        'id_aparelho' => '',
        'id_servico' => '',
        'subnum' => '',
        'ipaton' => '',
        'idmarca' => '',
        'nome' => '',
        'descricao' => '',
        'vendormac' => ''
    );

    protected $RemoteQuery;
    
    protected $OnlineReg;
    /**
     * Do a remote query on a cable modem
     * 
     * @param string $community
     * @return Array
     */
    public function remoteQuery($community = 'public')
    {
        if (empty($this->macaddr) || empty($this->ipaddr)) {
            return null;
        }

        
        $ip   = str_replace("0.0.", "10.1.", $this->ipaddr);
        $this->RemoteQuery['ip'] = $ip;
        $this->RemoteQuery['community'] = $community;
        
        $this->RemoteQuery['tx']       = $this->getTX();
        $this->RemoteQuery['snr']      = $this->getSNR();
        $this->RemoteQuery['rx']       = $this->getRX();
        $this->RemoteQuery['version']  = $this->getFirmwareVersion();
                
        return $this->RemoteQuery;
        
    }
    /**
     * Get TX signal values
     *
     * @return double
     */
    private function getTX()
    {
        return $this->getRemote($this->RemoteQuery['ip'], ".1.3.6.1.2.1.10.127.1.2.2.1.3.2", $this->RemoteQuery['community']);
    }
    /**
     * Get RX signal values
     * 
     * @return double
     */
    private function getRX()
    {
        return $this->getRemote($this->RemoteQuery['ip'], ".1.3.6.1.2.1.10.127.1.1.1.1.6.3", $this->RemoteQuery['community']);
    }
    /**
     * Get SNR
     *
     * @return double
     */
    private function getSNR()
    {
        return $this->getRemote($this->RemoteQuery['ip'], ".1.3.6.1.2.1.10.127.1.1.4.1.5.3", $this->RemoteQuery['community']);
    }
    /**
     * Get firmware version and CM info description
     * @return String
     */
    private function getFirmwareVersion()
    {
        return $this->getRemote($this->RemoteQuery['ip'], "SNMPv2-MIB::sysDescr.0", $this->RemoteQuery['community'], "STRING:");
    }
    /**
     * Executes the remote query
     *
     * @param string $ipaddr IP Address of CM
     * @param string $oid OID
     * @param String community
     * @param string $exp Specific separator returned from snmpget to use for str split
     *
     * @return String|null Null on error
     */
    private function getRemote($ipaddr, $oid, $community, $exp = ":")
    {        
        $tmp = explode( $exp, snmpget($ipaddr, $community,$oid));
        if (!array($tmp)) {
            return null;
        }
        
        return $tmp[1];
    }
    
}

