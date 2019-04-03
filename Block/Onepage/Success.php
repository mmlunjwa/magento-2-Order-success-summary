<?php

namespace Sqli\OrderDetails\Block\Onepage;

class Success extends \Magento\Checkout\Block\Onepage\Success {

    protected $orderRepository;
    protected $renderer;
    protected $searchCriteriaBuilder;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
    \Magento\Checkout\Model\Session $checkoutSession,
    \Magento\Sales\Model\Order\Config $orderConfig,
    \Magento\Framework\App\Http\Context $httpContext,
    \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
    \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
    \Magento\Sales\Model\Order\Address\Renderer $renderer, array $data = []
    ) {
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->renderer = $renderer;
        parent::__construct(
                $context, $checkoutSession, $orderConfig, $httpContext, $data
        );
    }

    public function getOrder() {
        return $this->_checkoutSession->getLastRealOrder();
    }

    /**
     * Get order by increment id.
     *
     * @param string $incrementId
     * @return OrderInterface
     * @throws NoSuchEntityException
     */
    public function getOrderByIncrementId($incrementId)
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter(
            OrderInterface::INCREMENT_ID,
            $incrementId
        )->create();

        $result = $this->orderRepository->getList($searchCriteria);

        if (empty($result->getItems())) {
            throw new NoSuchEntityException(__('No such order.'));
        }

        $orders = $result->getItems();

        return reset($orders);
    }

    public function getFormatedAddress($address) {
        return $this->renderer->format($address, 'html');
    }

    public function getPaymentMethodtitle($order) {
        $payment = $order->getPayment();
        $method = $payment->getMethodInstance();
        return $method->getTitle();
    }
    public function getProductImage() {
        
    }

}
