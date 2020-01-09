<?php
namespace JustinKase\CanadaPostRates\Model\Config;

/**
 * Class Methods
 *
 * This returns DOMESTIC ONLY Canada Post available shipping method values.
 *
 * This is static. This will need to be updated upon changes from Canada Post.
 *
 * To get realtime and contract based available shipping options, upgrade to
 * the Professional/Business edition of this module. For questions or inquiries
 * do not hesitate to contact me.
 *
 * @author Alex Ghiban <drew7721@gmail.com>
 */
class Methods implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'DOM.EP', 'label' => __('Expedited Parcel')],
            ['value' => 'DOM.RP', 'label' => __('Regular Parcel')],
            ['value' => 'DOM.PC', 'label' => __('Priority')],
            ['value' => 'DOM.XP', 'label' => __('Xpresspost')]
        ];
    }
}
