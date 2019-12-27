<?php
namespace JustinKase\CanadaPostRates\Model\Config;

/**
 * Class Mode
 *
 * Backend model for environment options.
 *
 * @author Alex Ghiban <drew7721@gmail.com>
 */
class Mode implements \Magento\Framework\Data\OptionSourceInterface
{

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Development')],
            ['value' => 1, 'label' => __('Production')]
        ];
    }
}
