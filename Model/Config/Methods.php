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
            ['value' => 'DOM.XP', 'label' => __('Xpresspost')],
            ['value' => 'INT.PW.ENV', 'label' => __('Priority Worldwide envelope INT')],
            ['value' => 'USA.PW.ENV', 'label' => __('Priority Worldwide envelope USA')],
            ['value' => 'USA.PW.PAK', 'label' => __('Priority Worldwide pak USA')],
            ['value' => 'INT.PW.PAK', 'label' => __('Priority Worldwide pak INT')],
            ['value' => 'INT.PW.PARCEL', 'label' => __('Priority Worldwide parcel INT')],
            ['value' => 'USA.PW.PARCEL', 'label' => __('Priority Worldwide parcel USA')],
            ['value' => 'INT.XP', 'label' => __('Xpresspost International')],
            ['value' => 'INT.IP.AIR', 'label' => __('International Parcel Air')],
            ['value' => 'INT.IP.SURF', 'label' => __('International Parcel Surface')],
            ['value' => 'INT.TP', 'label' => __('Tracked Packet - International')],
            ['value' => 'INT.SP.SURF', 'label' => __('Small Packet International Surface')],
            ['value' => 'INT.SP.AIR', 'label' => __('Small Packet International Air')],
            ['value' => 'USA.XP', 'label' => __('Xpresspost USA')],
            ['value' => 'USA.EP', 'label' => __('Expedited Parcel USA')],
            ['value' => 'USA.TP', 'label' => __('Tracked Packet - USA')],
            ['value' => 'USA.SP.AIR', 'label' => __('Small Packet USA Air')]
        ];
    }
}
