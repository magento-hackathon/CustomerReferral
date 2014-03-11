<?php

class Uqppa_CustomerReferral_Model_Observer
{
	public function setCustomerSource($observer){
	
		$o_customer = $observer->getEvent()->getCustomer();
	
		$COOKIE_NAME = "__utmz";
		$ANALYTICS_SOURCE_STRING = "utmcsr";
		$ANALYTICS_MEDIUM_STRING = "utmcmd";
		$ANALYTICS_CAMPAIGN_STRING = "utmccn";
		$ANALYTICS_GCLID_STRING = "utmgclid";

		$NOT_SET = "not set";
		$DIRECT = "direct";
		$DELIMITER = "|";
		
		if(isset($_COOKIE[$COOKIE_NAME])){
			$a_cookie = explode ($DELIMITER ,$_COOKIE[$COOKIE_NAME]);
			if(is_array($a_cookie) && !empty($a_cookie)){
				foreach($a_cookie as $string){
					if(false !== strpos($string, $ANALYTICS_SOURCE_STRING)){
						$s_source = substr($string , strrpos ($string , "=") + 1);
					}
					if(false !== strpos($string, $ANALYTICS_MEDIUM_STRING)){
						$s_medium = substr($string , strrpos ($string , "=") + 1);
					}
					if(false !== strpos($string, $ANALYTICS_CAMPAIGN_STRING)){
						$s_campaign = substr($string , strrpos ($string , "=") + 1);
					}
					if(false !== strpos($string, $ANALYTICS_GCLID_STRING)){
						$s_gclid = substr($string , strrpos ($string , "=") + 1);
					}
				}
			} else {
				$s_source = empty($_SERVER['HTTP_REFERER']) ? $DIRECT : $_SERVER['HTTP_REFERER'];
				$s_medium = $NOT_SET;
				$s_campaign = $NOT_SET;
				$s_gclid = $NOT_SET;
			}
		} else {
			$s_source = empty($_SERVER['HTTP_REFERER']) ? $DIRECT : $_SERVER['HTTP_REFERER'];
			$s_medium = $NOT_SET;
			$s_campaign = $NOT_SET;
			$s_gclid = $NOT_SET;
		}
		
		if (strlen($s_source) == 0){
			$s_source = empty($_SERVER['HTTP_REFERER']) ? $DIRECT : $_SERVER['HTTP_REFERER'];
		}

		$o_customer->setData('customer_source', $s_source);
		$o_customer->setData('customer_medium', $s_medium);
		$o_customer->setData('customer_campaign', $s_campaign);
		$o_customer->setData('customer_gclid', $s_gclid);
		
		$o_customer->save();
		
		return $o_customer;
	}
	
	public function beforeBlockToHtml(Varien_Event_Observer $observer)
    {
        $grid = $observer->getBlock();

        /**
         * Mage_Adminhtml_Block_Customer_Grid
         */
        if ($grid instanceof Mage_Adminhtml_Block_Customer_Grid) {
            $grid
				->addColumn(
					'customer_source',
					array(
						'header' => 'Source'
						'index'  => 'customer_source'
					)
				)
				->addColumn(
					'customer_medium',
					array(
						'header' => 'Medium',
						'index'  => 'customer_medium'
					)
				)
				->addColumn(
					'customer_campaign',
					array(
						'header' => 'Campaign',
						'index'  => 'customer_campaign'
					)
				)
				->addColumn(
					'customer_gclid',
					array(
						'header' => 'Gclid',
						'index'  => 'customer_gclid'
					)
				);
        }
    }

    public function beforeCollectionLoad(Varien_Event_Observer $observer)
    {
        $collection = $observer->getCollection();
        if (!isset($collection)) {
            return;
        }

        /**
         * Mage_Customer_Model_Resource_Customer_Collection
         */
        if ($collection instanceof Mage_Customer_Model_Resource_Customer_Collection) {
            /* @var $collection Mage_Customer_Model_Resource_Customer_Collection */
            $collection ->addAttributeToSelect('customer_source')
						->addAttributeToSelect('customer_medium')
						->addAttributeToSelect('customer_campaign')
						->addAttributeToSelect('customer_gclid')
						;
        }
    }
}