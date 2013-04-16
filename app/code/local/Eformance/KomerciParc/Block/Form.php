<?php



class Eformance_KomerciParc_Block_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('Eformance_KomerciParc/form.phtml');
    }
    
    //pega configurações do magento
    protected function _getConfig()
    {
        return Mage::getSingleton('payment/config');
    }
    
        /**
     * Retrieve credit card expire months
     *
     * @return array
     */
    public function getCcMonths()
    {
        $months = $this->getData('cc_months');
        if (is_null($months)) {
            $months[0] =  $this->__('Month');
            $months = array_merge($months, $this->_getConfig()->getMonths());
            $this->setData('cc_months', $months);
        }
        return $months;
    }

    /**
     * Retrieve credit card expire years
     *
     * @return array
     */
    public function getCcYears()
    {
        $years = $this->getData('cc_years');
        if (is_null($years)) {
            $years = $this->_getConfig()->getYears();
            $years = array(0=>$this->__('Year'))+$years;
            $this->setData('cc_years', $years);
        }
        return $years;
    }
    
    public function getParcelas(){
             
		$max_parcelas = Mage::getStoreConfig('payment/Eformance_KomerciParc/num_max_parc');
		$valor_minimo = Mage::getStoreConfig('payment/Eformance_KomerciParc/valor_minimo');
		$parcelas_sem_juros = Mage::getStoreConfig('payment/Eformance_KomerciParc/parcelamento_semjuros');
		$taxa_juros = Mage::getStoreConfig('payment/Eformance_KomerciParc/parcelamento_juros');
                
                $descontoavista = Mage::getStoreConfig('payment/Eformance_KomerciParc/desconto_avista');
                $descontoavista_valor = Mage::getStoreConfig('payment/Eformance_KomerciParc/valor_desconto_avista');
                

		$total = Mage::getSingleton('checkout/cart')->getQuote()->getGrandTotal();

		$totals = Mage::getSingleton('checkout/cart')->getQuote()->getTotals();

		if(isset($totals["encargo"])){
			$encargo = $totals["encargo"]->getValue();
		}else{
			$encargo = 0;
		}
		if($encargo > 0){
			$total = $total - $encargo;
		}


		$total_com_juros = $total;

		$n = floor($total / $valor_minimo);
		if($n > $max_parcelas){
			$n = $max_parcelas;
		}elseif($n < 1){
			$n = 1;
		}

		$parcelas = array();
	    for ($i=1; $i < $n; $i++){
			$total_com_juros *= 1 + ($taxa_juros / 100);

			if($taxa_juros > 0 && $i+1 > $parcelas_sem_juros){
				$label = ($i+1).'x - '.$this->helper('checkout')->formatPrice($total_com_juros/($i + 1)).' (juros de '.$taxa_juros.'% ao mês)';
			}else{
				$label = ($i+1).'x  ';
			}
                        
                        if($i+1 == 1){
                            $parcelas[] = array('parcela' => $i, 'label' => $label);
                        }ELSE{
                            $parcelas[] = array('parcela' => $i+1, 'label' => $label);
                        }
                        
		}
		return $parcelas;
                //return array('parcela' => 1, 'label' => $valor_minimo);
                
	}
    
}
