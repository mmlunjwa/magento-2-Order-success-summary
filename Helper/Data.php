<?php

namespace Sqli\OrderDetails\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;


/**
 * Class Data
 * @package Sqli\OrderDetails\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    private $httpContext;
    protected $productRepository;
    protected $_session;
    protected $price_helper;
    protected $tax_calculation;
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Framework\Pricing\Helper\Data
     */
    public function __construct(
    \Magento\Framework\App\Helper\Context $context,
    \Magento\Catalog\Model\ProductRepository $productRepository,
    \Magento\Framework\App\Http\Context $httpContext,
    \Magento\Customer\Model\Session $session,
    \Magento\Framework\Pricing\Helper\Data $price_helper,
    \Magento\Tax\Model\TaxCalculationFactory $tax_calculation
    ) {
        $this->productRepository = $productRepository;
        $this->httpContext = $httpContext;
        $this->_session = $session;
        $this->price_helper=$price_helper;
        $this->tax_calculation=$tax_calculation;
        parent::__construct($context);
    }
    /**
     * Get Any Store Configuration
     * @param string $storePath Full path of any configuration
     * @return string $storeConfig
     */
    public function getStoreConfig($storePath) {
        return $this->scopeConfig->getValue($storePath, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Load product from productId
     * @param int $id Product id
     * @return $this
     */
    public function getProductById($id) {
        return $this->productRepository->getById($id);
    }

    /**
     * Check Customer is login or not
     * @return boolean
     */
    public function isLoggedIn() {
        return $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }

    /**
     * Get Formated Price
     * @param fload price 
     * @return boolean
    */
    public function getFormatedPrice($price='')
    {
        return $this->price_helper->currency($price, true, false);
    }


    /**
     * @param $product_id
     * @return array
     */
    public function getProductCollection($product_id)
    {
        $porIds=array($product_id);
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection');

        $collection = $productCollection->addAttributeToSelect('*')
                                        ->addFieldToFilter('entity_id', array('in' => $porIds));
        $productArray = array();
        foreach ($collection as $prod) {
            $productArray = $prod->getData();
        }
        return $productArray;
    }

    /**
     * @param $_taxClassId
     * @param null $storeId
     * @return mixed
     */
    public function getTaxRate($_taxClassId, $storeId = null){
        return $this->tax_calculation->create()->getCalculatedRate($_taxClassId, null, $storeId);
    }
}