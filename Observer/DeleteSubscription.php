<?php
namespace Superb\Recommend\Observer;

/*
 * Superb_Recommend
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0), a
 * copy of which is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Superb
 * @package    Superb_Recommend
 * @author     Superb <hello@wearesuperb.com>
 * @copyright  Copyright (c) 2015 Superb Media Limited
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

use Magento\Framework\Event\ObserverInterface;

class DeleteSubscription implements ObserverInterface
{
    /**
     * @var \Superb\Recommend\Logger\Logger
     */
    protected $_logger;

    /**
     * @var \Superb\Recommend\Helper\Tracker
     */
    protected $_helper;

    public function __construct(
        \Superb\Recommend\Logger\Logger $logger,
        \Superb\Recommend\Helper\Tracker $helper
    ) {
        $this->_logger = $logger;
        $this->_helper = $helper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_helper->isEnabled()) {
            return $this;
        }
        $subscriber = $observer->getEvent()->getSubscriber();
        if ($subscriber->getData('subscriber_status')==\Magento\Newsletter\Model\Subscriber::STATUS_SUBSCRIBED) {
            try {
                $data = $this->_helper->getCustomerUnsubscribeData($subscriber->getData('subscriber_email'));
                $this->_helper->setTrackingData($data);
            } catch (\Exception $e) {
                $this->_logger->critical($e);
            }
        }
    }
}
